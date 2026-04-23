<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function SendMessage(Request $request){
        $request->validate([
            'msg' => 'required'
        ]);

        ChatMessage::create([
            'sender_id'=>Auth::user()->id,
            'receiver_id'=>$request->receiver_id,
            'message'=>$request->msg,
            'created_at' => Carbon::now(),
        ]);
        return response()->json(['message'=>'Message Send Successfuly']);
    }//End method
}
