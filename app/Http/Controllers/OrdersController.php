<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\PlagiarismPlatform;
use App\Models\Status;
use App\Models\Subject;
use App\Models\Task;
use App\Models\TypeWork;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $items = OrderService::getFiltered($request);
        $paginatedItems = $items->paginate(10);

        return view('orders.index', [
            'title' => 'Заказы',
            'typeWorks' => TypeWork::all(),
            'subjects' => Subject::all(),
            'statuses' => Status::all(),
            'orders' => $paginatedItems
                ->appends($request->except(['page'])),
        ]);
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

    public function store(OrderRequest $request): RedirectResponse
    {
        $request->merge(['manager_id' => auth()->user()->id]);

        $order = Order::query()->create($request->all());

        $tasks = $request->input('task');
        $deadlines = $request->input('deadline_task');

        if (!empty($tasks)) {
            foreach ($tasks as $i => $taskText) {
                Task::query()->create([
                    'order_id' => $order->id,
                    'title' => $taskText,
                    'deadline_at' => $deadlines[$i],
                ]);
            }
        }

        return redirect()->route('orders.index')->with('success', 'Новый заказ создан');
    }

    public function edit(Order $order): View
    {
        return view('orders.edit', [
            'title' => 'Изменить задание',
            'typeWorks' => TypeWork::all(),
            'subjects' => Subject::all(),
            'experts' => User::query()->where('role_id', 2)->get(),
            'plagiarismPlatforms' => PlagiarismPlatform::all(),
            'order' => Order::query()->findOrFail($order->id),
        ]);
    }

    public function update(OrderRequest $request, Order $order): RedirectResponse
    {
        $order->update($request->all());

        $taskIds = $request->input('task_ids', []);
        $tasks = $request->input('task');
        $deadlines = $request->input('deadline_task');

        $existingTaskIds = [];

        if (!empty($tasks)) {
            foreach ($tasks as $i => $taskText) {
                $task = Task::query()->updateOrCreate(
                    [
                        'id' => $taskIds[$i] ?? null
                    ],
                    [
                        'order_id' => $order->id,
                        'title' => $taskText,
                        'deadline_at' => $deadlines[$i] ?? null
                    ]
                );

                $existingTaskIds[] = $task->id;
            }
        }

        Task::where('order_id', $order->id)
            ->whereNotIn('id', $existingTaskIds)
            ->delete();

        return redirect()
            ->route('orders.edit', ['order' => $order->id])
            ->with('success', 'Изменения сохранены.');
    }

    public function destroy(User $user): RedirectResponse
    {
//        User::query()->findOrFail($user->id)->delete();

        return redirect()->route('orders.index')->with('success', 'Заказ удален');
    }

    public function show(Order $order): View
    {
        return view('orders.show', [
            'title' => 'Заказ',
            'order' => $order,
        ]);
    }

    public function takeToWork(Order $order)
    {
        $order->update([
            'expert_id' => auth()->user()->id,
            'status_id' => 2
        ]);

        return redirect()->route('orders.index')->with('success', 'Заказ принят');
    }

    public function complete(Order $order)
    {
        $order->update([
            'status_id' => 3
        ]);

        return redirect()->route('orders.index')->with('success', 'Заказ выполнен');
    }
}
