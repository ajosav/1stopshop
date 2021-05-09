<?php

namespace App\Services\AdProductService;

use Exception;
use App\Models\AdService;
use App\Models\ProductView;
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
    public function updateProduct($product, $request) {
        try {
            $update_product = DB::transaction(function() use ($product, $request) {

                $data = AdProductDataHelper::updateProductData($request);
                return  $product->update($data);
            });

            if(!$update_product) {
                return response()->errorResponse("Error updating Ad service");
            }
            $new_product = AdService::where('encodedKey', $product->encodedKey)->first(); 
            return response()->success('product updated successfully', $new_product);
        } catch (QueryException $e) {
            report($e);
            return response()->errorResponse("Error updating product");
        } catch(Exception $e) {
            report($e);
            return response()->errorResponse("Failed to update product", ["Product Creation" => $e->getMessage()]);
        }
    }

    public function viewAllAds() {
        return AdService::query();
    }

    public function findProductByUser($userEncodedKey) {
        return $this->viewAllAds()->whereHas('user', function($query) use ($userEncodedKey) {
            $query->where('encodedKey', $userEncodedKey);
        })->with('user');
    }

    public function findProductByEncodedKey($productEncodedKey) {
        $product = $this->viewAllAds()->where('encodedKey', $productEncodedKey);

        if($product->first()) {
            $this->incrementProductView($productEncodedKey);
        }

        return $product;
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


    public function incrementProductView($productKey) {
        return ProductView::firstOrCreate(['request_ip' => request()->ip()], ['ad_id' => $productKey]);
    }



}