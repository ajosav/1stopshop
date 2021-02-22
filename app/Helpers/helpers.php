<?php

use Tymon\JWTAuth\Facades\JWTAuth;
use libphonenumber\PhoneNumberUtil;
use Intervention\Image\Facades\Image;
use libphonenumber\PhoneNumberFormat;
use Illuminate\Support\Facades\Storage;
use libphonenumber\NumberParseException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\DataTransferObjects\UserDataTransferObject;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


function getAuthenticatedUser()
{
    try {
        JWTAuth::parseToken()->authenticate();
        if (! $user = auth('api')->user()) {
                return response()->errorResponse('User not found', 404);
        }

    } catch (TokenExpiredException $e) {

        return response()->errorResponse('token_expired', ["token" => $e->getMessage()]);

    } catch (TokenInvalidException $e) {

        return response()->errorResponse('token_invalid', ["token" => $e->getMessage()]);

    } catch (JWTException $e) {

        return response()->errorResponse('token_absent', ["token" => $e->getMessage()]);

    }
    return response()->success('User data retreived', $user);
}

function sanitizePhoneNumber($phoneNumber, $national = true, $trim = true)
{
    $phoneUtil = PhoneNumberUtil::getInstance();

    if (isset($phoneNumber)) {
        try {
            $phoneNumber = $phoneUtil->format($phoneUtil->parse($phoneNumber, 'NG'), $national ? PhoneNumberFormat::NATIONAL : PhoneNumberFormat::E164);

            if ($trim) {
                $phoneNumber = ltrim($phoneNumber, '+');
            }
        } catch (NumberParseException $exception) {
            $phoneNumber = null;
        }
    } else {
        $phoneNumber = null;
    }

    return $phoneNumber;
}

function respondWithToken($token) {
    return [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60
    ];
}

function generateEncodedKey() {
    $hash = time() . bin2hex(openssl_random_pseudo_bytes(10)). random_bytes(10);
    return  md5($hash);
}

function photoType($photo) {
    if(@is_string($photo)) {
        try {
            $image_data = preg_replace('#^data:image/\w+;base64,#i', '', $photo);
            $decode_image = base64_decode($image_data);

            if(!imagecreatefromstring($decode_image)) {
                return false;
            }
            return "Base64";
        } catch(ErrorException $e) {
            return false;
        }
       
        // dd($image_data);
        // if (base64_encode(base64_decode($photo, true)) === $image_data){
        // } 
    }
   
    return @is_file($photo) ? "file" : false; 
}

function uploadImage($photo) {
    $photo_type = photoType($photo);

    if(!$photo_type) {
        return null;
    }

    if($photo_type == "Base64") {
        try {
           $name = time(). rand(1,10) . '.' . explode('/', explode(':', substr($photo, 0, strpos($photo, ';')))[1])[1];
        } catch(ErrorException $e) {
            $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photo));
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $image_data, FILEINFO_MIME_TYPE);
            $extension =  substr($mime_type, (strpos($mime_type, "/") + 1));
            $name = time() . rand(1,10) . '.' . $extension;
        }

        $image = \Image::make($photo)->stream();
        Storage::put('images/shop/' . $name, $image);
        // ->save(storage_path('images/shop/').$name);
        return 'images/shop/' . $name; 
        
    } 
    
    if($photo_type == "file") {
        $imageName = time(). rand(1,10) . '.' . $photo->getClientOriginalExtension();
        $newImage = \Image::make($photo->getRealPath());
        Storage::put('images/shop/' . $imageName, $newImage->stream());
        return 'images/shop/' . $imageName;
    }

    return null;
}