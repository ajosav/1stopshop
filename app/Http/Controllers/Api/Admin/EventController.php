<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = request()->user()->events()->paginate(20);
        return EventResource::collection($events)->additional([
            'status'    => "success",
            'message'   => "User events retrieved sucessfully"
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {
        try {
            $event = request()->user()->events()->create($request->validated());
            return (new EventResource($event))->additional([
                'status'    => "success",
                'message'   => "User event sucessfully created"
            ]);
        } catch(QueryException $e) {
            report($e);
            return response()->errorResponse("Error creating event");
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        if($event->user_id !== request()->user()->id) 
            throw (new ModelNotFoundException)->setModel(Event::class);
        return (new EventResource($event))->additional([
            'status'    => "success",
            'message'   => "Event sucessfully retrieved"
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function findByDate($date)
    {
        $events = Event::where(function($query) use ($date) {
            return $query->where('start', $date)
                        ->orWhere('end', $date);
        })->where('user_id', request()->user()->id)->get();

        return (EventResource::collection($events))->additional([
            'status'    => "success",
            'message'   => "Events sucessfully retrieved"
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventRequest $request, Event $event)
    {
        try {
            $event->update($request->validated());
            return (new EventResource($event))->additional([
                'status'    => "success",
                'message'   => "User events sucessfully created"
            ]);
            return response()->success("Event updated successfully", $event->format());
        } catch(QueryException $e) {
            report($e);
            return response()->errorResponse('Error updating event');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return response()->success("Event successfully deleted");
        } catch(QueryException $e) {
            report($e);
            return response()->errorReponse("Error deleting event");
        }
    }
}
