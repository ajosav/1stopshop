<?php

namespace App\DataTransferObjects;

use phpDocumentor\Reflection\Types\Boolean;
use Spatie\DataTransferObject\DataTransferObject;

class ProfileDataTransferObject extends DataTransferObject {
    public string $id;
    public string $phone_number;
    public ?string $profile_photo;
    public ?string $tax_identification_no;
    public ?string $identification_type;
    public ?string $identity_number;
    public ?string $professional_skill;
    public ?string $specialization;
    public $experience_years;
    public $service_area;   
    public $isVerified;   
    

    public static function create($data) : self{
        return new self([
            'id' => $data['encodedKey'],
            'phone_number' => $data['phone_number'],
            'profile_photo'=> $data['profile_photo'],
            'tax_identification_no' => $data['tax_identification_no'],
            'identification_type' => $data['identification_type'],
            'identity_number' => $data['identity_number'],
            'professional_skill' => $data['professional_skill'],
            'specialization' => $data['specialization'],
            'experience_years' => $data['experience_years'],
            'service_area' => $data['service_area'],
            'isVerified' => $data['isVerified'] == 0 ? 'no' : 'yes',
        ]);
    }
}