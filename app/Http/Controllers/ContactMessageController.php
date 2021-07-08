<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Mail;

class ContactMessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'message' => 'required',
            'company_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        ContactMessage::create($request->all());

        $name = $request->name;
        $email = $request->email;
        $message = $request->message;
        $company_id = $request->company_id;

        $data = [
            "name" => $name,
            "email" => $email,
            "messages" => $message
        ];

        $user = User::find($company_id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $userEmail = $user->email;

        try {

            Mail::send('contactMail', $data, function ($message) use ($email, $name, $userEmail) {
                $message->to($userEmail)->subject('Nova poruka sa sajta CV priča')->from($email, $name);
            });

            return response()->json(["success" => true, "message" => "Message sent"]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => $e]);
        }
    }
}
