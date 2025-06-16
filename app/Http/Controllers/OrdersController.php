<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsersRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $data = array();
//        $data['users'] = Order::query()->paginate(10);
        $data['title'] = 'Заказы';

        return view('orders.index', $data);
    }

    public function create()
    {
        return view('users.create', [
            'title' => 'Добавить задание',
        ]);
    }

    public function store(StoreUsersRequest $request): RedirectResponse
    {
//        $validate = $request->safe()->toArray();
//        $validate['is_approved'] = 1;
//
//        User::query()->create($validate);
//
        return redirect()->route('orders.index')->with('success', 'Заказ добавлен');
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'title' => 'Изменить задание',
//            'user' => User::query()->findOrFail($user->id),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->saved($request, $user);

        return redirect()->route('orders.index')->with('success', 'Изменения сохранены.');
    }

    public function destroy(User $user): RedirectResponse
    {
//        User::query()->findOrFail($user->id)->delete();

        return redirect()->route('orders.index')->with('success', 'Заказ удален');
    }

    private function saved(Request $request, User $user): void
    {
//        $rules = [
//            'name' => 'required|string|min:2|max:255',
//            'email' => 'required|email|max:255',
//            'password' => 'nullable|confirmed|string|min:6',
//            'role_id' => 'sometimes|required|in:1,2',
//        ];
//
//        $validatedData = $request->validate($rules);
//
//        if ($request->filled('password')) {
//            $validatedData['password'] = bcrypt($validatedData['password']);
//        } else {
//            unset($validatedData['password']);
//        }
//
//        $user->update($validatedData);
    }

}
