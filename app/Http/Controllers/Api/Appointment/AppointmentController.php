<?php

namespace App\Http\Controllers\Api\Appointment;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Appointment\AppointmentRequest;
use App\Mail\Appointment\BookAppointmentMail;
use App\Mail\Appointment\BookAppointmentNotification;

class AppointmentController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware('auth.jwt');
        $this->user = auth('api')->user();
    }

    public function book(AppointmentRequest $request) {
        $appointment = $request->validated();
        $mechanic  = User::where('encodedKey', $request->mechanic_id)->whereHas('permissions', function($query) {
            return $query->whereName('mechanic');
        })->first();

        if(!$mechanic) {
            return response()->errorResponse('Unable to find any Mechanic with the given ID');
        }

        Mail::to($mechanic->email)->send(new BookAppointmentMail($this->user, $mechanic, $appointment));
        return response()->success('Appointment sent successfully');
    }
}
