<?php

namespace App\Http\Controllers\Api\ProductService;

use App\Models\AdService;
use Illuminate\Http\Request;
use App\Traits\GetRequestType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Facades\ProductAdServiceFacade;
use App\Http\Requests\Ad\CreateAdProductRequest;
use App\Models\Category;
use App\Models\RecordViewContact;

class ProductAdController extends Controller
{
    use GetRequestType;

    public function __construct()
    {
        $this->middleware('auth.jwt')->except(['index', 'show', 'searchProduct', 'find', 'viewContact']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $searchResults = (new Search())
        //     ->registerModel(AdService::class, function(ModelSearchAspect $modelSearchAspect) {
        //         $modelSearchAspect
        //         ->addSearchableAttribute('product_title') // return results for partial matches on product title
        //         ->addExactSearchableAttribute('keyword') // only return results that exactly match the keyword
        //         ;
        // })->perform('durolast');

        // return $searchResults;
        // $ads = ProductAdServiceFacade::viewAllAds();
        // return ProductAdServiceFacade::filterProduct()->toSql();
        $ads = ProductAdServiceFacade::filterProduct();
        return $this->getFullProductDetails($ads)->additional([
            'message' => 'Ad services retrieved successfully',
            'status' => "success"
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAdProductRequest $request)
    {
        
        $user = auth('api')->user();
        abort_if(! Gate::allows('part_dealer', $user), 403, "Only Part dealers are allowed to create products");
        
        return ProductAdServiceFacade::createProduct($user, $request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($encodedKey)
    {
        $ad_query = ProductAdServiceFacade::findProductByEncodedKey($encodedKey);
        return $this->getSingleRelatedProduct($ad_query)->additional([
            'message' => 'Ad retrieved successfully',
            'status' => "success"
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct(AdService $encodedKey)
    {
        return ProductAdServiceFacade::deleteProduct($encodedKey);
    }

    public function deactivateProduct(AdService $adservice) {
        return ProductAdServiceFacade::deactivateProduct($adservice, 'inactive');
    }

    public function activateProduct(AdService $adservice) {
        return ProductAdServiceFacade::deactivateProduct($adservice, 'active');
    }

    public function searchProduct() {
        if(!request()->has('query')) {
            return response()->errorResponse("Missing search query");
        }

        $query = request('query');

        $searchResults = ProductAdServiceFacade::searchProduct($query);

        return $searchResults;
    }


    public function viewContact(Request $request) {
        $validate_input = $request->validate([
            'product_id' => 'required',
            'owner_id'  => 'required'
        ]);

        RecordViewContact::firstOrCreate([
            "user_ip" => $request->ip(),
        ],[
            "product_id" => $validate_input['product_id'],
            "owner_id" => $validate_input['owner_id'],
            "user_id" => optional(auth('api')->user())->encodedKey
        ]);

        return response()->success(true);
        
    }   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateAdProductRequest $request, AdService $ad)
    {
        $user = auth('api')->user();
        abort_if(! Gate::allows('part_dealer', $user), 403, "Only Part dealers are allowed to update products");

        if($ad->user->encodedKey !== $user->encodedKey) {
            return response()->errorResponse('Permisson denied! You did not create this ad', [], 403);
        }
        
        return ProductAdServiceFacade::updateProduct($ad, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function find($encodedKey) {
        $ads = ProductAdServiceFacade::findProductByUser($encodedKey);

        return $this->getFullProductDetails($ads)->additional([
            'message' => 'All services created by user retrieved successfully',
            'status' => "success"
        ]);
    }

    public function userProducts() {
        $user  = auth('api')->user();
        abort_if(! Gate::allows('part_dealer', $user), 403, "Only Part dealer are allowed to create products");
        
        $ads = ProductAdServiceFacade::findProductByUser($user->encodedKey);

        return $this->getFullProductDetails($ads)->additional([
            'message' => 'All services created by user retrieved successfully',
            'status' => "success"
        ]);

    }
}
