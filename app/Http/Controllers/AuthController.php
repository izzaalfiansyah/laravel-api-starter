<?php

namespace App\Http\Controllers;

use App\Mail\GetCodeForResetPassword;
use App\Mail\VerifyAccount;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

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
        $user = User::find($req->user()->id);

        Mail::to($user->email)->send(new VerifyAccount($user));

        return [
            'message' => 'verification link sent',
        ];
    }

    public function verify(Request $req)
    {
        $user = User::find($req->route('id'));

        if ($req->route('hash') != sha1($user->email)) {
            throw new AuthorizationException;
        }

        $user->markEmailAsVerified();

        return '
            <script>
                window.close();
            </script>
        ';
    }

    function sendForgotPasswordEmail(Request $req)
    {
        $this->schema($req->all(), [
            'email' => 'required',
        ]);

        $user = User::where('email', $req->email)->first();

        if ($user) {
            Mail::to($user->email)->send(new GetCodeForResetPassword());

            return [
                'message' => 'reset password code sent'
            ];
        } else {
            $this->badRequest([
                'errors' => [
                    'message' => ['reset password code failed to send']
                ]
            ]);
        }
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
