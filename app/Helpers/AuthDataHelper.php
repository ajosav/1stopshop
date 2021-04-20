<?php

namespace App\Helpers;

class AuthDataHelper {
    public static function createUserWithSocialData($data, $provider) {
        $name = explode(' ', $data->getName());
        return [
            'first_name' => $name[1],
            'last_name' => $name[0],
            'email' => $data->getEmail(),
            'provider' => $provider,
            'provider_id' => $data->getId(),
            'encodedKey' => generateEncodedKey()
        ];
    }

    public static function userCreateProfile($data) {
        $photo = isset($data['profile_photo']) ? uploadImage('images/profile/', $data['profile_photo']) : null;
        return [
            'encodedKey' => generateEncodedKey(),
            'phone_number' => isset($data['phone_number']) ? $data['phone_number'] : null,
            'tax_identification_no' => isset($data['tax_identification_no']) ? $data['tax_identification_no'] : null,
            'identification_type' => isset($data['identification_type']) ? $data['identification_type'] : null,
            'identity_number' => isset($data['identity_number']) ? $data['identity_number'] : null,
            'professional_skill' => isset($data['professional_skill']) ? $data['professional_skill'] : null,
            'specialization' => isset($data['specialization']) ? $data['specialization'] : null,
            'experience_years' => isset($data['experience_years']) ? $data['experience_years'] : null,
            'service_area' => isset($data['service_area']) ? json_encode($data['service_area']) : null,
            'profile_photo' => $photo,
        ];
    }

    public static function userCreateCompany($data) {
        $photo = isset($data['shop_photo']) ? uploadImage('images/shop/', $data['shop_photo']) : null;
        return [
            'office_no' => isset($data['office_number']) ? $data['office_number'] : null,
            'encodedKey' => generateEncodedKey(),
            'street_name' => isset($data['street_name']) ? $data['street_name'] : null,
            'city' => isset($data['city']) ? $data['city'] : null,
            'state' => isset($data['state']) ? $data['state'] : null,
            'region' => isset($data['region']) ? $data['region'] : null,
            'country' => isset($data['country']) ? $data['country'] : null,
            'company_mission' => isset($data['company_mission']) ? $data['company_mission'] : null,
            'shop_photo' => $photo
        ];
    }

}