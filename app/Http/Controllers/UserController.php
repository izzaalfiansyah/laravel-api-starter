<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $req)
    {
        $limit = $req->input('limit', 10);
        $search = $req->input('q');
        $role = $req->input('role');

        $builder = new User;

        if ($role) {
            $builder = $builder->where('role', $role);
        }

        if ($search) {
            $builder = $builder->where(function ($query) use ($search) {
                return $query->where('email', 'like', "%$search%")
                    ->orWhere('nama', 'like', "%$search%");
            });
        }

        $items = $builder->paginate($limit);

        return $items;
    }
}
