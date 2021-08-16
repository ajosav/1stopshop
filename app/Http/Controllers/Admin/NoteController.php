<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notes = request()->user()->notes()->paginate(20);
        return NoteResource::collection($notes)->additional([
            'message' => "Notes retrieved successfully",
            'status' => "success"
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $new_note = $request->validate([
            "completed"     => "nullable|string|in:true,false",
            "priority"  => "nullable|string|max:100",
            "body"      => "required|string"
        ]);

        try {
            $created_note = $request->user()->notes()->create($new_note);
            return response()->success('Note successfully created', $created_note);
        } catch (QueryException $e) {
            report($e);
            return response()->errorResponse("Failed to create");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        return response()->succees('Note successfully retrieved', $note);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $note)
    {
        $new_note = $request->validate([
            "body"              => "required|string|max:150",
            "completed"         => "required|string|in:true,false"
        ]);

        $note->body = $new_note['body'];
        $note->completed = $new_note['completed'];
        $note->save();

        if(!$note->wasChanged()) {
            response()->errorResponse('Failed to update note');
        }

        return response()->success('Note successfully updated', $note);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        if(!$note->delete()) {
            return response()->errorResponse('Error deleting note');
        }

        return response()->success('Note Deleted Successfully');
    }
}
