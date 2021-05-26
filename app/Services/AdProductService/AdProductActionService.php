<?php

namespace App\Services\AdProductService;

use Exception;
use App\Models\AdService;
use App\Models\ProductView;
use Jenssegers\Agent\Agent;
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
        return AdService::where('status', 'active');
        $filter_products = app(Pipeline::class)
                        ->send(AdService::where('status', 'active'))
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
        $agent = new Agent();
        // if($agent->isRobot()) {
        //     return false;
        // }
        return ProductView::create([
            'ad_id' => $productKey,
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
            'desktop_view' => $agent->isDesktop(),
            'mobile_view' => $agent->isMobile() || $agent->isTablet() ?? true,
            'browser_version' => $agent->version($agent->platform()),
            'request_ip' => request()->ip()
        ]);
    }

    public function deactivateProduct($adservice, $status) {
        $user = auth('api')->user();
        
        if($adservice->user->encodedKey != $user->encodedKey) {
            return response()->errorResponse('Permission Denied!', [], 403);
        }
        
        $adservice->status = $status;

        if(!$adservice->isDirty()) {
            return response()->errorResponse("Product is already {$status}");
        }
        $message = $status == 'active' ? 'activate' : 'deactivate';
        
        if(!$adservice->save()) {
            return response()->errorResponse("Failed to {$message} product");
        }

        return response()->success("Product successfully {$message}d", $adservice);
    }


    public function deleteProduct($adservice) {
        $user = auth('api')->user();
        
        if($adservice->user->encodedKey != $user->encodedKey) {
            return response()->errorResponse('Permission Denied!', [], 403);
        }

        $product_photo = json_decode($adservice->product_photo);
    
        foreach($product_photo as $photo){
            if(file_exists(storage_path("app/" . $photo))) {
                @unlink(storage_path("app/" . $photo));
            }
        }
        
        $adservice->productViews()->delete();

        if(!$adservice->delete()) {
            return response()->errorResponse("Product deleted successfully");
        }

        return response()->success("Product deleted successfully");
    }



}