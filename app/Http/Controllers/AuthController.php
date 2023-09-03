<?php

namespace App\Http\Controllers;

use App\Mail\GetCodeForResetPassword;
use App\Mail\VerifyAccount;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        $data = $this->schema($req->all(), [
            'email' => 'required|email',
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

    public function register(Request $req)
    {
        $data = $this->schema($req->all(), [
            'email' => 'required|email|max:255|unique:users',
            'nama' => 'required|max:255',
            'password' => 'required|min:8|confirmed',
            'device_name' => 'required'
        ]);

        $user = User::create($data);

        $token = $user->createToken($req->device_name, $user->role != '1' ? ['access-public'] : ['*'])->plainTextToken;

        return ['token' => $token];
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

    public function sendForgotPasswordEmail(Request $req)
    {
        $this->schema($req->all(), [
            'email' => 'required|email',
        ]);

        $user = User::where('email', $req->email)->first();

        if ($user) {
            PasswordResetToken::where('email', $user->email)->delete();

            $passwordResetToken = PasswordResetToken::create([
                'email' => $user->email,
                'token' => random_int(000000, 999999),
            ]);

            Mail::to($user->email)->send(new GetCodeForResetPassword($passwordResetToken));

            return [
                'message' => 'reset password code sent'
            ];
        } else {
            $this->badRequest([
                'errors' => [
                    'message' => ['reset password code failed to send'],
                ],
            ]);
        }
    }

    public function resetPasswordEmail(Request $req)
    {
        $this->schema($req->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required',
        ]);

        $user = User::where('email', $req->email)->first();

        if (!$user) {
            $this->badRequest([
                'errors' => [
                    'message' => ['you can\'t reset your password'],
                ],
            ]);
        }

        $passwordResetToken = PasswordResetToken::where('email', $req->email)->first();

        if (!$passwordResetToken) {
            $this->badRequest([
                'errors' => [
                    'message' => ['resetting password failed']
                ],
            ]);
        }

        if ($passwordResetToken->token != $req->token) {
            $this->badRequest([
                'errors' => [
                    'message' => ['your token mismatch'],
                ]
            ]);
        }

        if (date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($passwordResetToken->created_at))) < date('Y-m-d H:i:s')) {
            $this->badRequest([
                'errors' => [
                    'message' => ['your token has expired'],
                ],
            ]);
        }

        $user->update([
            'password' => $req->password
        ]);

        PasswordResetToken::where('email', $req->email)->delete();

        return [
            'message' => 'password successfully changed',
        ];
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
