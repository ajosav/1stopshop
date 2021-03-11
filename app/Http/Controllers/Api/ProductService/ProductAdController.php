<?php

namespace App\Http\Controllers\Api\ProductService;

use App\Models\AdService;
use App\Filters\Book\Order;
use Illuminate\Http\Request;
use App\Models\AdProductType;
use Spatie\Searchable\Search;
use App\Filters\Shop\UserType;
use App\Traits\GetRequestType;
use Illuminate\Pipeline\Pipeline;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Facades\ProductAdServiceFacade;
use Spatie\Searchable\ModelSearchAspect;
use App\Http\Requests\Ad\CreateAdProductRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductAdController extends Controller
{
    use GetRequestType;

    public function __construct()
    {
        $this->middleware('auth.jwt')->only('store');
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

    
    public function allAdTypes()
    {
        $product_types = AdProductType::all()->map->format();
        return response()->success('Product types retrieved successfully', $product_types);
    }
    public function findAdType(AdProductType $ad_type)
    {
        $product_types = $ad_type->format();
        return response()->success('Product type retrieved successfully', $product_types);
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
        abort_if(! Gate::allows('isPartDealer', $user->encodedKey), 403, "Only Part dealers are allowed to create products");
        
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
        return $this->getSingleProduct($ad_query)->additional([
            'message' => 'Ad retrieved successfully',
            'status' => "success"
        ]);

    }

    public function searchProduct() {
        if(!request()->has('query')) {
            return response()->errorResponse("Missing search query");
        }

        $query = request('query');

        $searchResults = ProductAdServiceFacade::searchProduct($query);

        return $searchResults;
    }


    public function filterProduct() {

    }

   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
        abort_if(! Gate::allows('isPartDealer', $user->encodedKey), 403, "Only Part dealer are allowed to create products");
        
        $ads = ProductAdServiceFacade::findProductByUser($user->encodedKey);

        return $this->getFullProductDetails($ads->with('adProductType'))->additional([
            'message' => 'All services created by user retrieved successfully',
            'status' => "success"
        ]);

    }

    // $ad_services = app(Pipeline::class)
    //     ->send(AdService::query())
    //     ->through([
    //         Order::class,
    //         UserType::class
    //     ])
    //     ->thenReturn()
    //     ->get();

    //     return response()->success('Found services within your search');
}
