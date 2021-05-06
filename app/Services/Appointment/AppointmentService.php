<?php

namespace App\Services\Appointment;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment\WorkingHour;
use App\Mail\Appointment\BookAppointmentMail;

class AppointmentService {
    public function createAppointment($request) {
        $mechanic_id = $request['mechanic_id'];
        $user  = User::where('encodedKey', $mechanic_id)->whereHas('permissions', function($query) {
            return $query->whereName('mechanic');
        })->with('mechanic')->first();

        if(!$user) {
            return response()->errorResponse('Unable to find any Mechanic with the given ID');
        }

        $mechanic = $user->mechanic;
        
        // to_hour
        $appointment_date = $request['date'];
        $appointment_time = (int) $request['time'];
        if($request['meridian'] == "PM" && $request['time'] != 12) {
            $appointment_time = (int) $request['time'] + 12;
        }

        if($request['meridian'] == "AM" && $request['time'] == 12) {
            $appointment_time = '00';
        }

        $working_hours = WorkingHour::where('user_id', $mechanic->encodedKey)
                        ->where('day', '=', date("l", strtotime($appointment_date)))
                        ->where('from_hour', '<=', $appointment_time)
                        ->where('to_hour', '>=', $appointment_time)->get();

        if($working_hours->isEmpty()) return response()->errorResponse("This Mechanic isn't working at your selected time");

        $appointment = Appointment::where('mechanic_id', $mechanic->encodedKey)->where('hour', $appointment_time)->whereDate('date', date('Y-m-d', strtotime($appointment_date)))->where('status', '<>', 'Rejected')->get();

        if(!$appointment->isEmpty()) return response()->errorResponse("This Mechanic already booked at your selected time");

        $new_appointment = new Appointment;
        $new_appointment->mechanic_id = $mechanic->encodedKey;
        $new_appointment->visitor_id = auth('api')->user()->encodedKey;
        $new_appointment->date = date('Y-m-d', strtotime($appointment_date));
        $new_appointment->hour = $appointment_time;
        $new_appointment->meridian = $request['meridian'];
        $new_appointment->description = isset($request['description']) ? $request['description'] : null;

        $new_appointment->save();

        Mail::to($user->email)->send(new BookAppointmentMail(auth('api')->user(), $mechanic, $request));

        return response()->success('Appointment successfully submitted; You will be notified when the mechanic accepts your booking');



    }
}