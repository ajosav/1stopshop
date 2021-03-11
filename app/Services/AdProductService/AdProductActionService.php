<?php

namespace App\Services\AdProductService;

use Exception;
use App\Models\AdService;
use App\Models\AdProductType;
use Spatie\Searchable\Search;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use App\Helpers\AdProductDataHelper;
use App\Filters\ProductAdFilter\Make;
use App\Filters\ProductAdFilter\Type;
use App\Filters\ProductAdFilter\Year;
use App\Filters\ProductAdFilter\Model;
use Illuminate\Database\QueryException;
use Spatie\Searchable\ModelSearchAspect;
use App\Filters\ProductAdFilter\Location;
use App\Filters\ProductAdFilter\Condition;
use App\Http\Resources\Product\ProductResource;
use App\Filters\ProductAdFilter\Search as DBSearch;
use App\Http\Resources\Product\ProductResourceCollection;

class AdProductActionService {

    public function createProduct($user, $request) {
        try {
            $create_ad = DB::transaction(function() use ($user, $request) {

                $data = AdProductDataHelper::createNewProductData($request);
                return  $user->adService()->create($data);
            });

            $new_Ad = new ProductResource($create_ad);

            return response()->success('New Product Successfully Created', $new_Ad);
        } catch (QueryException $e) {
            return response()->errorResponse("Error creating product");
        } catch(Exception $e) {
            return response()->errorResponse("Product creation failed", ["Product Creation" => $e->getMessage()]);
        }
    }

    public function viewAllAds() {
        return AdService::query();
    }

    public function findProductByUser($userEncodedKey) {
        return $this->viewAllAds()->with('adProductType')->whereHas('user', function($query) use ($userEncodedKey) {
            $query->where('encodedKey', $userEncodedKey);
        })->with('user');
    }

    public function findProductByEncodedKey($productEncodedKey) {
        return $this->viewAllAds()->with('adProductType')->where('encodedKey', $productEncodedKey);
    }

    public function searchProduct($query) {
        $searchResults = (new Search())
            ->registerModel(AdService::class, function(ModelSearchAspect $modelSearchAspect) {
                $modelSearchAspect
                ->addSearchableAttribute('make') // return results for partial matches on make
                ->addSearchableAttribute('product_title') // return results for partial matches on product title
                ->addExactSearchableAttribute('keyword') // only return results that exactly match the keyword
                ->has('user')
                ->with('user');
        })
        ->registerModel(AdProductType::class, 'name')
        ->perform($query);

        return $searchResults;
    }

    public function filterProduct() {
        $filter_products = app(Pipeline::class)
                        ->send(AdService::query())
                        ->through([
                            Condition::class,
                            Make::class,
                            Model::class,
                            Type::class,
                            Year::class,
                            DBSearch::class,
                            Location::class
                        ])
                        ->thenReturn();
                        // ->jsonPaginate();

        return $filter_products;
    }



}