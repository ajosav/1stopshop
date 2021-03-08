<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class CompanyDataTransferObject extends DataTransferObject {
    public ?string $office_no;
    public string $id;
    public ?string $street_name;
    public ?string $city;
    public ?string $state;
    public ?string $region;
    public ?string $country;
    public ?string $company_mission;
    public ?string $shop_photo;

    public static function create($data) : self{
        return new self([
            'id' => $data['encodedKey'],
            'office_no' => $data['office_no'],
            'street_name' => $data['street_name'],
            'city'=> $data['city'],
            'state'=> $data['state'],
            'region'=> $data['region'],
            'country'=> $data['country'],
            'company_mission'=> $data['company_mission'],
            'shop_photo'=> $data['shop_photo']
            
        ]);
    }
}