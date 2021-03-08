<?php

namespace App\Helpers;

use Illuminate\Support\Str;


class AdProductDataHelper {
    public static function createNewProductData($data) {
        $product_photos = [];
        foreach($data['product_photo'] as $photo) {
            $product_photos[] = uploadImage('images/product/', $photo);
        }
        return [
            "encodedKey"                =>      generateEncodedKey(),
            "product_title"             =>      $data['product_title'],
            "ad_product_type_id"        =>      $data['product_type'],
            "keyword"                   =>      isset($data['keyword']) ? $data['keyword'] : null,
            "condition"                 =>      $data['condition'],
            "year"                      =>      $data['year'],
            "make"                      =>      $data['make'],
            "model"                     =>      $data['model'],
            "warranty"                  =>      isset($data['warranty']) ? $data['warranty'] : null,
            "description"               =>      $data['description'],
            "price"                     =>      cleanAmount($data['price']),
            "product_photo"             =>      json_encode($product_photos),
            "negotiable"                =>      isset($data['negotiable']) ? $data['negotiable'] : 0
        ];
    }
}