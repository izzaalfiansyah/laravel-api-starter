<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;

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
                $token = $user->createToken($req->device_name)->plainTextToken;
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
}
