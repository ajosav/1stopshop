<?php

namespace App\Models;

use Exception;
use BinaryCats\Sku\HasSku;
use App\Models\ProductView;
use App\Traits\ExtendReview;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Intervention\Image\Facades\Image;
use BinaryCats\Sku\Concerns\SkuOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\ImageException;
use Codebyray\ReviewRateable\Contracts\ReviewRateable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Codebyray\ReviewRateable\Traits\ReviewRateable as ReviewRateableTrait;

class AdService extends Model implements Searchable, ReviewRateable
{
    use HasFactory, HasSku, ReviewRateableTrait, ExtendReview;

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

    /**
     * Get all of the product's abuses.
     */
    public function abuses()
    {
        return $this->morphMany(Abuse::class, 'abusable');
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

    public function starRatingPercent($max = 5)
    {
        $ratings = $this->ratings();
        $quantity = $ratings->count();
        $total = $ratings->selectRaw("SUM(rating) as total")->where('rating', $max)->pluck('total')->first();
        // return $total;
        return ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;
    }

    
    public function customerReviews() {
        return [
            "average_overall_rating" => $this->averageRating(2),
            "average_durability" => $this->averageCustomerServiceRating(2),
            "average_quality" =>  $this->averageQualityRating(2),
            "average_value_for_money" => $this->averageFriendlyRating(2),
            "total_rating" => $this->countRating(),
            "percentageRatings" => (object) [
                "5" => $this->starRatingPercent(),
                "4" => $this->starRatingPercent(4),
                "3" => $this->starRatingPercent(3),
                "2" => $this->starRatingPercent(2),
                "1" => $this->starRatingPercent(1),
            ]

        ];
    }
}
