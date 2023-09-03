<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        $data = $this->schema($req->all(), [
            'email' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $req->email)->first();

        if ($user) {
            if (Hash::check($req->password, $user->password)) {
                $token = $user->createToken($req->device_name, $user->role != '1' ? ['access-public'] : ['*'])->plainTextToken;
                return ['token' => $token];
            } else {
                $this->badRequest([
                    'errors' => [
                        'password' => ['password incorrect']
                    ]
                ]);
            }
        } else {
            $this->badRequest([
                'errors' => [
                    'email' => ['email not found']
                ]
            ]);
        }
    }

    public function sendVerificationEmail(Request $req)
    {
        $req->user()->sendEmailVerificationNotification();

        return [
            'message' => 'verification link sent',
        ];
    }

    public function verify(Request $req)
    {
        $user = User::find($req->route('id'));

        if (!$req->route('hash') == sha1($user->email)) {
            throw new AuthorizationException;
        }

        $user->markEmailAsVerified();

        return redirect()->away('app://open');
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();

        return [
            'message' => 'logout successfully',
        ];
    }

    public function profile(Request $req)
    {
        return $req->user();
    }
}
