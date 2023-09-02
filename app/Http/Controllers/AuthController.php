<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        $data = $this->schema($req->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $req->email)->first();

        if ($user) {
            if (Hash::check($req->password, $user->password)) {
                $token = Random::generate(100);
                $user->setRememberToken($token);
                $user->save();

                return $user->getRememberToken();
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
