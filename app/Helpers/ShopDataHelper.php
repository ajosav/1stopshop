<?php

namespace App\Helpers;

class ShopDataHelper {

    public static function userUpdateProfile($data) {
        return [
            'tax_identification_no' => isset($data['tax_identification_no']) ? $data['tax_identification_no'] : null,
            'identification_type' => isset($data['identification_type']) ? $data['identification_type'] : null,
            'identity_number' => isset($data['identity_number']) ? $data['identity_number'] : null,
            'professional_skill' => isset($data['professional_skill']) ? $data['professional_skill'] : null,
            'specialization' => isset($data['specialization']) ? $data['specialization'] : null,
            'experience_years' => isset($data['experience_years']) ? $data['experience_years'] : null,
            'service_area' => isset($data['service_area']) ? json_encode($data['service_area']) : null
        ];
    }

    public static function userUpdateCompany($data) {
        $photo = isset($data['shop_photo']) ? uploadImage('images/shop/', $data['shop_photo']) : null;
        return [
            'office_no' => isset($data['office_number']) ? $data['office_number'] : null,
            'street_name' => isset($data['street_name']) ? $data['street_name'] : null,
            'state' => isset($data['state']) ? $data['state'] : null,
            'region' => isset($data['region']) ? $data['region'] : null,
            'country' => isset($data['country']) ? $data['country'] : null,
            'company_mission' => isset($data['company_mission']) ? $data['company_mission'] : null,
            'shop_photo' => $photo
        ];
    }

    public static function createReviewPhotoData($data) {
        $product_photos = [];
        if(isset($data['review_photo'])) {
            foreach($data['review_photo'] as $photo) {
                $product_photos[] = uploadImage('images/reviews/', $photo);
            }
        }

        return [
            "review_photo"      =>      empty($data['review_photo']) ? null :json_encode($product_photos),
            "display_name"      =>      $data['display_name']
        ];
    }
}