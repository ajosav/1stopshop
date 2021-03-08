<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ProductAdDataTransferObject extends DataTransferObject {
    public string $id;
    public ?string $product_title;
    public ?string $keyword;
    public ?string $condition;
    public ?string $year;
    public ?string $make;
    public ?string $model;
    public ?string $warranty;
    public $product_photo;
    public ?string $description;
    public $price;
    public $negotiable;
    public $product_no;
    public $date_created;

    public static function create($data) : self{
        return new self([
            'id'                =>          $data['encodedKey'],
            'product_title'     =>          $data['product_title'],
            'keyword'           =>          $data['keyword'],
            'condition'         =>          $data['condition'],
            'year'              =>          $data['year'],
            'make'              =>          $data['make'],
            'model'             =>          $data['model'],
            'warranty'          =>          $data['warranty'],
            'product_photo'     =>          $data['product_photo'],
            'description'       =>          $data['description'],
            'price'             =>          $data['price'],
            'negotiable'        =>          $data['negotiable'],
            'product_no'        =>          $data['product_no'],
            'date_created'      =>          $data['date_created']
        ]);
    }
}