<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsersRequest;
use App\Models\Order;
use App\Models\User;
use App\Services\TgService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $data = array();
        $data['users'] = User::query()->paginate(10);
        $data['title'] = 'Пользователи';

        return view('users.index', $data);
    }

    public function create()
    {
        return view('users.create', [
            'title' => 'Добавить сотрудника',
        ]);
    }

    public function store(StoreUsersRequest $request): RedirectResponse
    {
        $validate = $request->safe()->toArray();
        $validate['is_approved'] = 1;

        User::query()->create($validate);

        return redirect()->route('users.index')->with('success', 'Пользователь добавлен');
    }

    public function edit(User $user): View
    {
        $countOrders = Order::query()
            ->where('expert_id', $user->id)
            ->orWhere('manager_id', $user->id)
            ->count();

        return view('users.edit', [
            'title' => 'Изменить пользователя',
            'has_orders' => $countOrders,
            'user' => User::query()->findOrFail($user->id),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->saved($request, $user);

        return redirect()->route('users.index')->with('success', 'Изменения сохранены.');
    }

    public function destroy(User $user): RedirectResponse
    {
//        if ($user->orders()->count() > 0) {
//            return redirect()
//                ->route('users.index')
//                ->with('error', 'Нельзя удалить пользователя, у которого есть заказы');
//        }

        User::query()->findOrFail($user->id)->delete();

        return redirect()->route('users.index')->with('success', 'Пользователь удален');
    }

    public function profile(Request $request)
    {
        $tgId = $request->all()['tg_id'] ?? null;

        if ($tgId) {
            auth()->user()->update([
                'tg_id' => $tgId,
            ]);

            TgService::sendMessage(
                $tgId,
                'Поздравляю, ' . auth()->user()->name . ' ' . auth()->user()->last_name .
                '! Вы авторизовались в системе как ' . auth()->user()->role->title .
                ' и теперь будете получать все уведомления системы через меня.'
            );

            Log::channel('tg_api')
                ->info(
                    'Пользователь ' . auth()->user()->name . ' ' . auth()->user()->last_name .
                    ' (' . auth()->user()->id . ') подключился к боту с tg_id: ' . $tgId
                );

            return redirect()->route('profile');
        }

        return view('users.profile', [
            'title' => 'Профиль',
            'user' => auth()->user()
        ]);
    }

    public function profileUpdate(Request $request)
    {
        $this->saved($request, auth()->user());

        return redirect()->route('profile')->with('success', 'Изменения сохранены.');
    }

    private function saved(Request $request, User $user): void
    {
        $rules = [
            'name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|confirmed|string|min:6',
            'role_id' => 'sometimes|required|in:1,2',
        ];

        $validatedData = $request->validate($rules);

        if ($request->filled('password')) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => 1]);

        return redirect()->route('users.index')->with('success', 'Пользователь одобрен');
    }

    public function disconnectTg()
    {
        $tgId = auth()->user()->tg_id;

        auth()->user()->update([
            'tg_id' => null,
        ]);

        Log::channel('tg_api')
            ->info(
                'Сотрудник ' . auth()->user()->name . ' ' . auth()->user()->last_name .
                ' (' . auth()->user()->id . ') отключился от бота.'
            );

        TgService::sendMessage(
            $tgId,
            'Вы успешно отключили свою учетную запись Telegram от системы! Больше вам не будут поступать уведомления.'
        );

        return redirect()->route('profile');
    }
}
