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
use App\Services\YandexDiskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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

    public function takeToWork(Order $order)
    {
        $order->update([
            'expert_id' => auth()->user()->id,
            'status_id' => 2
        ]);

        return redirect()->route('orders.index')->with('success', 'Заказ принят');
    }

    public function complete(Request $request, Order $order)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|mimetypes:image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.doc',
        ], [
            'files.required' => 'Обязательно добавьте хотя бы один файл.',
            'files.array' => 'Обязательно добавьте хотя бы один файл.',

            'files.*.required' => 'Не загружено ни одного файла.',
            'files.*.file' => 'Вы пытаетесь загрузить не файл.',
            'files.*.mimetypes' => 'Допустимые форматы: изображения, PDF, DOC, DOCX.',
        ]);

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

    public function downloadFile(Order $order, $fileName)
    {
        $path = 'orders/' . $order->id . '/order_files/' . $fileName;
        $content = YandexDiskService::read('alexstud/' . $path);
        $filename = basename('alexstud/' . $path);

        return Response::make($content, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function deleteFile(Order $order, $fileName): RedirectResponse
    {
        $result = YandexDiskService::deleteFile('alexstud/orders/' . $order->id . '/order_files/' . $fileName);

        if (!$result) {
            return redirect()->route('orders.edit', ['order' => $order->id])->with('error', 'Ошибка! Файл не удален');
        }

        return redirect()->route('orders.edit', ['order' => $order->id])->with('success', 'Файл удален');
    }

    public function changeStatus(Order $order, Status $status): RedirectResponse
    {
        $result = OrderService::changeStatus($order, $status);

        if (!$result) {
            return redirect()->route('orders.show', ['order' => $order->id])->with('error', 'Ошибка! Статус не изменен');
        }

        return redirect()->route('orders.show', ['order' => $order->id])->with('success', 'Статус изменен');

    }
}
