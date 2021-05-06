<?php

namespace App\Http\Controllers\Api\Appointment;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Appointment\AppointmentRequest;
use App\Mail\Appointment\BookAppointmentMail;
use App\Mail\Appointment\BookAppointmentNotification;
use App\Services\Appointment\AppointmentService;

class AppointmentController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware('auth.jwt');
        $this->user = auth('api')->user();
    }

    public function book(AppointmentRequest $request, AppointmentService $appointment) {
        $data = $request->validated();
        return $appointment->createAppointment($data);
    }


    public function createAppointment() {
        $employee = \App\Employee::find($request->employee_id);
		$working_hours = \App\WorkingHour::where('employee_id', $request->employee_id)->whereDay('date', '=', date("d", strtotime($request->date)))->whereTime('start_time', '<=', date("H:i", strtotime("".$request->starting_hour.":".$request->starting_minute.":00")))->whereTime('finish_time', '>=', date("H:i", strtotime("".$request->finish_hour.":".$request->finish_minute.":00")))->get();
		if(!$employee->provides_service($request->service_id)) return redirect()->back()->withErrors("This employee doesn't provide your selected service")->withInput();
        if($working_hours->isEmpty()) return redirect()->back()->withErrors("This employee isn't working at your selected time")->withInput();
		$appointment = new Appointment;
		$appointment->client_id = $request->client_id;
		$appointment->employee_id = $request->employee_id;
		$appointment->start_time = "".$request->date." ".$request->starting_hour .":".$request->starting_minute.":00";
		$appointment->finish_time = "".$request->date." ".$request->finish_hour .":".$request->finish_minute.":00";
		$appointment->comments = $request->comments;
		$appointment->save();
    }
}
