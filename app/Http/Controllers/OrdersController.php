<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\PlagiarismPlatform;
use App\Models\Subject;
use App\Models\Task;
use App\Models\TypeWork;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index()
    {
        $data = array();
        $data['orders'] = Order::query()->paginate(10);
        $data['title'] = 'Заказы';

        return view('orders.index', $data);
    }

    public function create()
    {
        return view('orders.create', [
            'title' => 'Создать новый заказ',
            'typeWorks' => TypeWork::all(),
            'subjects' => Subject::all(),
            'experts' => User::query()->where('role_id', 2)->get(),
            'plagiarismPlatforms' => PlagiarismPlatform::all(),
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $request->merge(['manager_id' => auth()->user()->id]);

        $order = Order::query()->create($request->all());

        $tasks = $request->input('task');
        $deadlines = $request->input('deadline_task');

        foreach ($tasks as $i => $taskText) {
            Task::query()->create([
                'order_id' => $order->id,
                'title' => $taskText,
                'deadline_at' => $deadlines[$i],
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'Новый заказ создан');
    }

    public function edit(Order $order): View
    {
        return view('users.edit', [
            'title' => 'Изменить задание',
            'order' => Order::query()->findOrFail($order->id),
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
