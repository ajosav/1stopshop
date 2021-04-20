<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class UserDataTransferObject extends DataTransferObject {
    public string $id;
    public string $first_name;
    public string $last_name;
    public string $email;

    public static function create($data) : self{
        return new self([
            'id' => $data['encodedKey'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email'=> $data['email']
        ]);
    }
}