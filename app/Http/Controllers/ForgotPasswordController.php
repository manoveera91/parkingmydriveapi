<?php

namespace App\Http\Controllers;
use App\Models\AuthOwner;
use App\Models\AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
class ForgotPasswordController extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['status' => 'success', 'message' => __($status)]);
        } else {
            return response()->json(['status' => 'error', 'message' => __($status)], 422);
        }
    }

    public function reset()
    {
        $credentials = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $email = $credentials['email'];
        $token = $credentials['token'];
        $password = $credentials['password'];
        $passwordEnc = bcrypt($password);

        $owner = AuthOwner::where('email', $email)->first();

        if ($owner && Hash::check($password, $owner->password)) {
            return response()->json(['message' => 'Password already has been used'], 400);
        } else {
            if ($owner) {
                $owner->password = $passwordEnc;
                $owner->save();
            }
        }

        $reset_password_status = Password::broker('auth_users')->reset($credentials, function ($user, $password) use ($passwordEnc) {
            $user->password = $passwordEnc;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(['message' => 'Invalid token provided'], 400);
        }

        return response()->json(['message' => 'Password has been successfully changed']);
    }

    public function changepassword()
    {
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'newPassword' => 'required|string',
        ]);

        $email = $credentials['email'];
        $password = $credentials['password'];
        $newPassword = $credentials['newPassword'];
        $passwordEnc = bcrypt($newPassword);

        $owner = AuthOwner::where('email', $email)->first();
        $user = AuthUser::where('email', $email)->first();
        if ($owner && Hash::check($password, $owner->password)) {
            $owner->password = $passwordEnc;
            $owner->save();
        } else {
            return response()->json(['message' => 'Invalid current password'], 400);
        }

        if ($user && Hash::check($password, $user->password)) {
            $user->password = $passwordEnc;
            $user->save();
        } else {
            return response()->json(['message' => 'Invalid password'], 400);
        }

        // if ($reset_password_status == Password::INVALID_TOKEN) {
        //     return response()->json(['message' => 'Invalid token provided'], 400);
        // }

        return response()->json(['message' => 'Password has been successfully changed']);
    }
}
