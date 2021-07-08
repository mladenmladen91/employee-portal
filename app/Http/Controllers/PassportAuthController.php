<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class PassportAuthController extends Controller
{
    /**
     * Registration
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'full_name' => 'required',
            'role' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => 0,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('LaravelAuthApp')->accessToken;

        if (!$user) {
            return response()->json(['success' => false, "message" => "Error"], 500);
        }
        return response()->json(['success' => true, "message" => "User successfully registered"], 200);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($request->all())) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['success' => true, 'token' => $token, "role" => auth()->user()->role], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorised'], 401);
        }
    }

    // function for logging or registering using google api
    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required",
            "google_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = User::where("email", $request->email);

        if (!$user->first()) {
            $user = User::create([
                'full_name' => $request->name,
                'email' => $request->email,
                'google_id' => $request->google_id,
                'role' => $request->role,
                'is_active' => 1
            ]);
            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['success' => true, 'token' => $token, "role" => $user->role], 200);
        } else {
            $userModel = $user->first();
            if ($userModel->google_id == $request->google_id) {
                $token = $userModel->createToken('LaravelAuthApp')->accessToken;
                return response()->json(['success' => true, 'token' => $token, "role" => $userModel->role], 200);
            } else {
                if (!$userModel->google_id) {
                    $user->update(["google_id" => $request->google_id]);
                    $token = $userModel->createToken('LaravelAuthApp')->accessToken;
                    return response()->json(['success' => true, 'token' => $token, "role" => $userModel->role], 200);
                } else {
                    return response()->json(['success' => false, 'message' => 'Unauthorised'], 401);
                }
            }
        }
    }


    // function for logging or registering using facebook api
    public function facebookLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required",
            "facebook_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = User::where("email", $request->email);

        if (!$user->first()) {
            $user = User::create([
                'full_name' => $request->name,
                'email' => $request->email,
                'facebook_id' => $request->facebook_id,
                'role' => $request->role,
                'is_active' => 1
            ]);
            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['success' => true, 'token' => $token, "role" => $user->role], 200);
        } else {
            $userModel = $user->first();
            if ($userModel->facebook_id == $request->facebook_id) {
                $token = $userModel->createToken('LaravelAuthApp')->accessToken;
                return response()->json(['success' => true, 'token' => $token, "role" => $userModel->role], 200);
            } else {
                if (!$userModel->facebook_id) {
                    $user->update(["facebook_id" => $request->facebook_id]);
                    $token = $userModel->createToken('LaravelAuthApp')->accessToken;
                    return response()->json(['success' => true, 'token' => $token, "role" => $userModel->role], 200);
                } else {
                    return response()->json(['success' => false, 'message' => 'Unauthorised'], 401);
                }
            }
        }
    }

    // function for logging or registering using apple api
    public function appleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required",
            "apple_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = User::where("email", $request->email);

        if (!$user->first()) {
            $user = User::create([
                'full_name' => $request->name,
                'email' => $request->email,
                'apple_id' => $request->google_id,
                'role' => $request->role,
                'is_active' => 1
            ]);
            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['success' => true, 'token' => $token, "role" => $user->role], 200);
        } else {
            $userModel = $user->first();
            if ($userModel->apple_id == $request->apple_id) {
                $token = $userModel->createToken('LaravelAuthApp')->accessToken;
                return response()->json(['success' => true, 'token' => $token, "role" => $userModel->role], 200);
            } else {
                if (!$userModel->apple_id) {
                    $user->update(["apple_id" => $request->apple_id]);
                    $token = $userModel->createToken('LaravelAuthApp')->accessToken;
                    return response()->json(['success' => true, 'token' => $token, "role" => $userModel->role], 200);
                } else {
                    return response()->json(['success' => false, 'message' => 'Unauthorised'], 401);
                }
            }
        }
    }


    // function for logging or registering using linkedin api
    public function linkedinLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required",
            "linkedin_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $user = User::where("email", $request->email);

        if (!$user->first()) {
            $user = User::create([
                'full_name' => $request->name,
                'email' => $request->email,
                'linkedin_id' => $request->google_id,
                'role' => $request->role,
                'is_active' => 1
            ]);
            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['success' => true, 'token' => $token, "role" => $user->role], 200);
        } else {
            $userModel = $user->first();
            if ($userModel->linkedin_id == $request->linkedin_id) {
                $token = $userModel->createToken('LaravelAuthApp')->accessToken;
                return response()->json(['success' => true, 'token' => $token, "role" => $userModel->role], 200);
            } else {
                if (!$userModel->linkedin_id) {
                    $user->update(["linkedin_id" => $request->linkedin_id]);
                    $token = $userModel->createToken('LaravelAuthApp')->accessToken;
                    return response()->json(['success' => true, 'token' => $token, "role" => $userModel->role], 200);
                } else {
                    return response()->json(['success' => false, 'message' => 'Unauthorised'], 401);
                }
            }
        }
    }

    //function for checking password
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "old_password" => "required",
            "password" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->messages()]);
        }

        $userAuth = auth()->user();
        if (!$userAuth) {
            return response()->json(['success' => false, 'message' => "User unauthorized"], 401);
        }

        $user = User::find($userAuth->id);

        if (Hash::check($request->old_password, $userAuth->password)) {
            $user->update(["password" => bcrypt($request->password)]);
            return response()->json(['success' => true, 'match' => 1], 200);
        }

        return response()->json(['success' => false, 'match' => 0], 200);
    }

    // function for getting token
    public function token()
    {
        /** @var User $user */
        $user = auth('api')->user();
        $accessToken = $user->createToken('LaravelAuthApp');

        return response()->json(['success' => true, "accessToken" => $accessToken->accessToken], 200);
    }
    // function for logout
    public function logout()
    {
        if (auth()->user()) {
            auth()->user()->logout(); // log the user out of our application
        }
        return response()->json(["success" => true, "message" => "User logout"], 200);
    }

    public function test()
    {
        return response()->json(["test" => "test"]);
    }
}
