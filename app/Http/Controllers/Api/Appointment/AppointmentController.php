<?php

namespace App\Http\Controllers\Api\Appointment;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Mail\Appointment\BookAppointmentMail;
use App\Services\Appointment\AppointmentService;
use App\Http\Requests\Appointment\AppointmentRequest;
use App\Mail\Appointment\BookAppointmentNotification;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Resources\Appintment\AppointmentResource;

class AppointmentController extends Controller
{
    public $user, $appointment;

    public function __construct(AppointmentService $appointment)
    {
        $this->middleware('auth.jwt');
        $this->appointment = $appointment;
        $this->user = auth('api')->user();
    }

    public function book(AppointmentRequest $request) {
        $data = $request->validated();
        return $this->appointment->createAppointment($data);
    }
    public function cancel(AppointmentRequest $request, $id, $visitor_id) {
        $appointment_id = decrypt($id);
        $today = date('Y-m-d');

        $appointment = Appointment::where('id', $appointment_id)->where('visitor_id', $visitor_id)->firstOrFail();

        if($appointment->date < $today ) {
            return response()->errorResponse('Cannot cancel appointment in the past');
        }

        if(!$appointment->delete()) {
            return  response()->errorResponse('Appoinment cancellation failed');
        }
        
        return  response()->errorResponse('Appointment successfully cancelled');
    }

    public function myAppointment() {
        abort_if(! Gate::allows('mechanic', $this->user), 403, "Only mechanics are allowed to view appointments");
        return AppointmentResource::collection($this->user->mechanic->appointment)->additional([
            'status' => 'success',
            'message' => 'Mechanic appointments retrieved successfully'
        ]);
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'status' => 'required|in:accept,reject'
        ]);

        try {
            $id = decrypt($id);
        } catch(DecryptException $e) {
            return response()->errorResponse('Appointment ID is invalid');
        }

        abort_if(! Gate::allows('mechanic', $this->user), 403, "Only mechanics are allowed to view appointments");
        
        return $this->appointment->updateAppointment($validated, $id);
    }

    // public function createAppointment() {
    //     $employee = \App\Employee::find($request->employee_id);
	// 	$working_hours = \App\WorkingHour::where('employee_id', $request->employee_id)->whereDay('date', '=', date("d", strtotime($request->date)))->whereTime('start_time', '<=', date("H:i", strtotime("".$request->starting_hour.":".$request->starting_minute.":00")))->whereTime('finish_time', '>=', date("H:i", strtotime("".$request->finish_hour.":".$request->finish_minute.":00")))->get();
	// 	if(!$employee->provides_service($request->service_id)) return redirect()->back()->withErrors("This employee doesn't provide your selected service")->withInput();
    //     if($working_hours->isEmpty()) return redirect()->back()->withErrors("This employee isn't working at your selected time")->withInput();
	// 	$appointment = new Appointment;
	// 	$appointment->client_id = $request->client_id;
	// 	$appointment->employee_id = $request->employee_id;
	// 	$appointment->start_time = "".$request->date." ".$request->starting_hour .":".$request->starting_minute.":00";
	// 	$appointment->finish_time = "".$request->date." ".$request->finish_hour .":".$request->finish_minute.":00";
	// 	$appointment->comments = $request->comments;
	// 	$appointment->save();
    // }
}
