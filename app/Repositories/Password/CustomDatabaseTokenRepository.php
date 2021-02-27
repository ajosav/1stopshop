<?php

namespace App\Repositories\Password;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
 
class CustomDatabaseTokenRepository extends DatabaseTokenRepository {
    
    // Overrides the standard token creation function
    public function createNewToken()
    {
        return substr(parent::createNewToken(), 0, 10);
    }

}
