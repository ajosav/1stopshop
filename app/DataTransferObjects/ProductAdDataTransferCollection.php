<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class ProductAdDataTransferCollection extends DataTransferObjectCollection
{
    public $first_page_url;
    public $from;
    public $last_page;
    public $last_page_url;
    public $links;
    public $next_page_url;
    public $path;
    public $per_page;
    public $prev_page_url;
    public $to;
    public $total;
    public $current_page;

    // public function current(): ProductAdDataTransferObject
    // {
    //     return parent::current();
    // }

    public static function create($data): ProductAdDataTransferCollection
    {
        $collection = [];

        foreach ($data as $item) {
            $collection[] = ProductAdDataTransferObject::create($item);
        }

        return new self($collection);
    }
}
