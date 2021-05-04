<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Mechanic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function searchLocation(Request $request) {
        $request->validate(['q' => 'required']);
        $query = request('q');
        $location = Mechanic::where('office_address', 'like', '%'.$query.'%')
                    ->orWhere('state', 'like', '%'.$query.'%')
                    ->orWhere('city', 'like', '%'.$query.'%')
                    ->select('office_address', 'state', 'city')
                    ->get();
        // return $location;
        $addresses = $location->map(function($value) {
            $places = [];
            $places[] = $value->office_address;
            $places[] = $value->state;
            $places[] = $value->city;
            return $places;
        })->collapse();

        return response()->success('Suggested locations successfully retrieved', $addresses);
    }
}
