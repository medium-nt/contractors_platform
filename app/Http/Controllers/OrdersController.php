<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderResponse;
use App\Models\PlagiarismPlatform;
use App\Models\Status;
use App\Models\Subject;
use App\Models\Task;
use App\Models\TypeWork;
use App\Models\User;
use App\Services\OrderService;
use App\Services\YandexDiskService;
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

        if ($request->has('expert_id')) {
            $request->merge(['status_id' => 2]);
        }

        $order = Order::query()->create($request->all());

        if ($order->expert_id) {
            OrderService::sendExpertSelectionMessage($order);
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = $file->getClientOriginalName();
                $path = 'orders/' . $order->id . '/order_files/' . $filename;

                YandexDiskService::write($path, file_get_contents($file));
            }
        }

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

    public function show(Order $order): View
    {
        return view('orders.show', [
            'title' => 'Заказ',
            'order' => $order,
            'files' => YandexDiskService::listFiles('/alexstud/orders/' . $order->id . '/order_files'),
            'resultFiles' => YandexDiskService::listFiles('/alexstud/orders/' . $order->id . '/result_files'),
            'expertFiles' => YandexDiskService::listFiles('/alexstud/orders/' . $order->id . '/expert_files'),
            'managerFiles' => YandexDiskService::listFiles('/alexstud/orders/' . $order->id . '/manager_files'),
            'responses' => OrderResponse::query()
                ->where('order_id', $order->id)
                ->get()
                ->map(function ($response) {
                    $response->all_tasks = Order::query()
                        ->where('expert_id', $response->expert_id)
                        ->count();
                    $response->working_tasks = Order::query()
                        ->where('expert_id', $response->expert_id)
                        ->whereIn('status_id', [2, 3, 4])
                        ->count();
                    return $response;
                }),
            'response' => OrderResponse::query()
                ->where('order_id', $order->id)
                ->where('expert_id', auth()->user()->id)
                ->first(),
        ]);
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
            'files' => YandexDiskService::listFiles('/alexstud/orders/' . $order->id . '/order_files'),
        ]);
    }

    public function update(OrderRequest $request, Order $order): RedirectResponse
    {
        $order->update($request->all());

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = $file->getClientOriginalName();
                $path = 'orders/' . $order->id . '/order_files/' . $filename;

                YandexDiskService::write($path, file_get_contents($file));
            }
        }

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
            ->route('orders.show', ['order' => $order->id])
            ->with('success', 'Изменения сохранены.');
    }

    public function destroy(User $user): RedirectResponse
    {
//        User::query()->findOrFail($user->id)->delete();

        return redirect()->route('orders.index')->with('success', 'Заказ удален');
    }

    public function complete(FileRequest $request, Order $order)
    {
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = $file->getClientOriginalName();
                $path = 'orders/' . $order->id . '/result_files/' . $filename;

                YandexDiskService::write($path, file_get_contents($file));
            }
        }

        $order->update([
            'status_id' => 3
        ]);

        return redirect()->route('orders.index')->with('success', 'Заказ выполнен');
    }

    public function downloadOrderFile(Order $order, $fileName)
    {
        $path = 'orders/' . $order->id . '/order_files/' . $fileName;
        return OrderService::downloadFile($path);
    }

    public function downloadResultFile(Order $order, $fileName)
    {
        $path = 'orders/' . $order->id . '/result_files/' . $fileName;
        return OrderService::downloadFile($path);
    }

    public function downloadExpertFile(Order $order, $fileName)
    {
        $path = 'orders/' . $order->id . '/expert_files/' . $fileName;
        return OrderService::downloadFile($path);
    }

    public function downloadManagerFile(Order $order, $fileName)
    {
        $path = 'orders/' . $order->id . '/manager_files/' . $fileName;
        return OrderService::downloadFile($path);
    }

    public function deleteOrderFile(Order $order, $fileName)
    {
        return OrderService::deleteFile($order, $fileName, 'order_files');
    }

    public function deleteFileManager(Order $order, $fileName)
    {
        return OrderService::deleteFile($order, $fileName, 'manager_files');
    }

    public function deleteFileExpert(Order $order, $fileName)
    {
        return OrderService::deleteFile($order, $fileName, 'expert_files');
    }

    public function changeStatus(Order $order, Status $status): RedirectResponse
    {
        $result = OrderService::changeStatus($order, $status);

        if (!$result) {
            return redirect()->route('orders.show', ['order' => $order->id])->with('error', 'Ошибка! Статус не изменен');
        }

        return redirect()->route('orders.show', ['order' => $order->id])->with('success', 'Статус изменен');

    }

    public function setResponse(Order $order, Request $request): RedirectResponse
    {
        $request->validate([
            'comment' => 'required|string|min:5|max:255',
        ], [
            'comment.required' => 'Обязательно добавьте комментарий.',
            'comment.string' => 'Комментарий должен быть строкой.',
            'comment.min' => 'Комментарий должен быть не менее 5 символов.',
            'comment.max' => 'Комментарий должен быть не более 255 символов.',
        ]);

        OrderResponse::query()->create([
            'order_id' => $order->id,
            'expert_id' => auth()->user()->id,
            'comment' => $request->comment,
        ]);

        return redirect()->route('orders.show', ['order' => $order->id])->with('success', 'Ваш отклик принят');
    }

    public function delResponse(Order $order)
    {
        OrderResponse::query()
            ->where('order_id', $order->id)
            ->where('expert_id', auth()->user()->id)
            ->delete();

        return redirect()->route('orders.show', ['order' => $order->id])->with('success', 'Ваш отклик удален');
    }

    public function checkExpert(Order $order, User $expert)
    {
        $order->update([
            'expert_id' => $expert->id,
            'status_id' => 2
        ]);

        OrderService::sendExpertSelectionMessage($order);

        return redirect()
            ->route('orders.show', ['order' => $order->id])
            ->with('success', 'Заказ передан в работу выбранному эксперту');

    }

    public function addFileExpert(FileRequest $request, Order $order)
    {
        return OrderService::addFile($request, $order, 'expert_files');
    }

    public function addFileManager(FileRequest $request, Order $order)
    {
        return OrderService::addFile($request, $order, 'manager_files');
    }
}
