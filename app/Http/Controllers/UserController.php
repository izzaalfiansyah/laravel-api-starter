<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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

    public function store(Request $req)
    {
        $data = $this->schema($req->all(), $this->rules());

        $item = User::create($data);

        return $item;
    }

    public function rules($id = null)
    {
        return [
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'nama' => 'required|max:255',
            'password' => [$id ? 'nullable' : 'required', 'min:8', 'confirmed'],
            'role' => 'nullable|in:1,2',
        ];
    }
}
