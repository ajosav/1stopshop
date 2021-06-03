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

    public function userViewContact() {
        return $this->hasMany(RecordViewContact::class, 'product_id', 'encodedKey');
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

    public function scopeRelatedProducts($query) {
        return $query->where(function($category) {
            return $category->where('sub_category_name', $this->sub_category_name)
                    ->orWhere('category_name', $this->category_name);
        })->where('id', '!=', $this->id)->take(5);
        // return $query->whereHas('category', function($category){
        //     return $category->orWhereHas('subCategories');
        // })->where('id', '!=', $this->id)->take(5);
    }
}
