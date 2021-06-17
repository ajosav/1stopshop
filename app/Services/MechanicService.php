<?php

namespace App\Services;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Arr;
use App\Helpers\ResourceHelpers;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Filters\MechanicFilter\Location;
use App\Filters\MechanicFilter\VehicleType;
use App\Filters\MechanicFilter\WorkingHours;
use App\Filters\MechanicFilter\YearOfExperience;
use App\Filters\MechanicFilter\ProfessionalSkill;
use App\Http\Resources\WorkHours\WorkHoursResource;

class MechanicService {
    public function getVerifiedMechanics() {
        return User::whereHas('permissions', function($query) {
                return $query->whereName('mechanic');
            })->whereHas('mechanic');
    }

    public function createNewMechanic($data, $user) {
        try {
            $new_mechanic = DB::transaction(function () use($data, $user) {
                if($user->mechanic()->create($data)) {
                    $user->givePermissionTo('mechanic');
                }

                return $user;
            });
        } catch (QueryException $e) {
            report($e);
            return response()->errorResponse("Error encountered while trying to create mechanic profile");
        }

        $mechanic_user = User::where('encodedKey', $new_mechanic->encodedKey)->with('mechanic', 'partDealer')->first();

        return ResourceHelpers::fullUserWithRoles($mechanic_user, 'Mechanic data created successfully');

    }
    public function updateMechanicData($data, $user) {
        try {
            $mechanic = $user->mechanic;
            foreach($data as $index => $update) {
                if(!is_null($update) && $update !== "") {
                    if($index == 'company_photo') {
                        if(file_exists(storage_path("app/" . $mechanic->company_photo))) {
                            @unlink(storage_path("app/" . $mechanic->company_photo));
                        }
                    }
                    $mechanic->$index = $update;
                }
            }
    
            if(!$mechanic->isDirty()) {
                return response()->success("Nothing Changed");
            }

            if(!$mechanic->save()) {
                return response()->errorResponse('Error updating mechanic details');
            }
           
        } catch (QueryException $e) {
            report($e);
            return response()->errorResponse("Error encountered while trying to update mechanic profile");
        } catch (Exception $e) {
            report($e);
            return response()->errorResponse("Error encountered while trying to update mechanic profile; Comfirm Mechanic profile exist");   
        }

        $mechanic_user = User::where('encodedKey', $user->encodedKey)->with('mechanic', 'partDealer')->first();

        return ResourceHelpers::fullUserWithRoles($mechanic_user, 'Mechanic profile update successfully');

    }

    public function filterMechanicServices() {
        $filter_mechanics = app(Pipeline::class)
                        ->send(User::has('mechanic'))
                        ->through([
                            Location::class,
                            ProfessionalSkill::class,
                            VehicleType::class,
                            WorkingHours::class,
                            YearOfExperience::class
                        ])
                        ->thenReturn();

        return $filter_mechanics;
    }

    public function editMechanicSchedule($schedules) {
        $user = auth('api')->user();

        
        DB::transaction(function () use ($user, $schedules) {
            $mechanic = $user->mechanic;
            $working_hour = $mechanic->update([
                'schedule_data' => $schedules['schedule_data']
            ]);

            $this->modelSchedule($schedules['schedule'], $mechanic->workingHours());
        });

        return WorkHoursResource::collection($user->mechanic->workingHours()->get())->additional([
            'message' => 'Schedule successfully updated for mechanic',
            'status' => "success"
        ]);

    }

    public function getMechanicSchedule($mechanic) {
        $mechanic_working_hours = $mechanic->workingHours()->get();
        $appointments = $mechanic->appointment()->whereDate('date', '>', now())->select('date', 'hour')->get();

        $appointments = $appointments->map(function($data){
			$data['day'] = DateTime::createFromFormat('Y-m-d', $data['date'])->format('l');
			// $data['day'] = date_format(date_create($data['day']), 'l');
            return $data;
        });

        return $mechanic_working_hours->map(function($days) use($appointments) {
            $hours = json_decode($days['schedule']);
            // return strtolower($days['day']);
            $booked_hours = $this->hoursBookedByDays($appointments, $days['day']);

            return [
                'day' => $days['day'],
                'hours_available' => Arr::sort($hours),
                "booked" => $booked_hours
            ];
        });

        return WorkHoursResource::collection($mechanic_working_hours)->additional([
            'message' => 'Successfully retrieved mechanic working hours',
            'status' => 'success'
        ]);
    }

    private function hoursBookedByDays($appointment, $day) {
        return $appointment->where('day', $day)
                    ->mapToGroups(function($item, $key) {
                        return [
                            $item['date'] => $item['hour']
                        ];
                    });
    }

    public function modelSchedule($schedule, $working_hour) {
        foreach($schedule as $day => $time) {
            $work_hours_range = range(1, 24);
            $existing_schedule = $working_hour->where('day', $day)->first();
            
            // if($existing_schedule) {
            //     $mechanic_shedule = json_decode($existing_schedule->schedule);
            //     $work_hours_range = array_intersect($mechanic_shedule, $work_hours_range);
            // }
            // else {
            //     dd($existing_schedule);
            // }

            if($existing_schedule) {
                $mechanic_shedule = json_decode($existing_schedule->schedule);
                $work_hours_range = array_intersect($mechanic_shedule, $work_hours_range);
            }
            foreach($time as $plan) {
                if($plan['meridian'] == 'PM') {
                    if($plan['hour'] != 12) {
                        $plan['hour'] = (int) $plan['hour'] + 12;
                    }
                }
                if($plan['meridian'] == "AM" && $plan['hour'] == 12){
                    $plan['hour'] = 24;
                }

                if (($key = array_search($plan['hour'], $work_hours_range)) !== false && $plan['isActive'] === 'false' ) {
                    // found hour in collection but mechainic isn't available
                    unset($work_hours_range[$key]);
                } elseif(($key = array_search($plan['hour'], $work_hours_range)) !== false && $plan['isActive'] === 'true') {
                    // found hour in collection and mechanic is available
                    continue;
                } elseif(($key = array_search($plan['hour'], $work_hours_range)) === false && $plan['isActive'] === 'true') {
                    // cannot find hour and mechanic is available
                    array_push($work_hours_range, (int) $plan['hour']);
                } else {
                    // cannot find hour and mechanic isn't available
                    continue;
                }
            }
           
            $working_hour->where('day', $day)->update([
                'schedule' => json_encode(array_values($work_hours_range))
            ]);
        }
    }


}