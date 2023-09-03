<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    function show($id)
    {
        $item = User::find($id);

        return $item;
    }

    public function store(Request $req)
    {
        $data = $this->schema($req->all(), $this->rules());

        $item = User::create($data);

        return [
            'message' => 'user successfully created',
        ];
    }

    public function update(Request $req, $id)
    {
        $data = $this->schema($req->all(), $this->rules($id));

        $item = User::find($id);

        if (!$req->password) {
            unset($data['password']);
        }

        $item?->update($data);

        return [
            'message' => 'user successfully updated',
        ];
    }

    public function destroy($id)
    {
        $item = User::find($id);

        $item?->delete();

        return [
            'message' => 'user successfully deleted',
        ];
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
