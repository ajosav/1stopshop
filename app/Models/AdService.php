<?php

namespace App\Models;

use Exception;
use BinaryCats\Sku\HasSku;
use App\Models\ProductView;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Intervention\Image\Facades\Image;
use BinaryCats\Sku\Concerns\SkuOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\ImageException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class AdService extends Model implements Searchable
{
    use HasFactory, HasSku;

    protected $guarded = [];

    protected $dates = [
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'encodedKey';
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function productViews() {
        return $this->hasMany(ProductView::class, 'ad_id', 'encodedKey');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_name', 'name');
    }

    public function skuOptions() : SkuOptions
    {
        return SkuOptions::make()
            ->from(['product_title', 'make'])
            ->target('product_no')
            ->using('-')
            ->forceUnique(true)
            ->generateOnCreate(true)
            ->refreshOnUpdate(false);
    }

    public function getSearchResult(): SearchResult
    {
        $url = route('product.find', $this->encodedKey);

        return new SearchResult(
            $this,
            $this->product_title,
            $url
        );
    }

    public function getProductPhotoAttribute($value) {
        if(!$value) {
            return $value;
        }

        $photos = [];        

        $product_photo = json_decode($value);
        foreach($product_photo as $photo){
            try {
                $image = Storage::get($photo);
                $photos[] = Image::make($image)->encode('data-url'); 
             } catch(ImageException $e) {
                 return null;
             } catch(Exception $e) {
                 return null;
             } catch(FileNotFoundException $e) {
                 return null;
             }
        }

        return $photos;
    }

    public function scopeRelatedProducts($query) {
        return $query->whereHas('category', function($category){
            return $category->orWhereHas('subCategories');
        })->where('id', '!=', $this->id)->take(5);
        return $query
                ->join('categories', 'ad_services.category_id', '=', 'categories.id')
                    ->join('categories as sub_category', 'ad_services.category_id', '=', 'sub_category.parent_id')
                    ->where(function($query) {
                        $query->where('ad_services.id', 'categories.id')
                            ->orWhere('ad_services.id', 'sub_categories.id');
                    })
                    ->take(5);
    }
}
