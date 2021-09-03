<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Exception;

class MessageController extends Controller
{
    public function storeMessage(Request $request) {
        $message = $request->validate([
            'full_name' => 'required',
            'message' => 'required|string|max:300',
            'email' => 'required|email'
        ]);

        try {
            $new_message = Message::create($message);
            return response()->success('Message successfully sent', $new_message);
        } catch(Exception $e) {
            report($e);
            return response()->errorResponse("Error sending message");
        }
    }
}
