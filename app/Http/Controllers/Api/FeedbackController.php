<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\FeedbackRequest;
use App\Http\Resources\Feedback\FeedbackResource;
use App\Models\Feedback;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function create(FeedbackRequest $request) {

        try {
            $feedback = Feedback::create($request->validated());
            return (new FeedbackResource($feedback))->additional([
                'message' => "Feedback submitted successfully",
                'status' => "success"
            ]);;
        } catch(QueryException $e) {
            report($e);
            return response()->errorResponse("Feedback could not be submitted");
        }


    }

    public function index() {
        return FeedbackResource::collection(Feedback::orderBy('created_at', 'desc')->paginate(50));
    }
}
