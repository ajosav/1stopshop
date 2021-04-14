<?php

namespace App\Models;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdProductType extends Model implements Searchable
{
    use HasFactory;

    public function format() {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }


    public function getSearchResult(): SearchResult
    {
        $url = route('product.find_ad_type', $this->id);

        return new SearchResult(
            $this,
            $this->name,
            $url
         );
    }
}
