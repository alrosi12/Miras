<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * قائمة المستخدمين مع بحث (اسم/بريد) وفلتر is_admin.
     */
    public function index(Request $request): View
    {
        Gate::authorize('admin');

        $query = User::query()->orderByDesc('id');

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)->orWhere('email', 'like', $term);
            });
        }

        if ($request->query('admin') === '1') {
            $query->where('is_admin', true);
        } elseif ($request->query('admin') === '0') {
            $query->where('is_admin', false);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * تفاصيل مستخدم مع جلساته وخططه (eager loading).
     */
    public function show(User $user): View
    {
        Gate::authorize('admin');

        // تحميل مسبق للجلسات (مع الخطة والمجموعات والتمرين) وللخطط لتفادي N+1 في العرض.
        $user->load([
            'workoutSessions' => fn ($q) => $q->with([
                'workoutPlan:id,name',
                'sessionSets.exercise:id,name',
            ])->latest('date')->limit(30),
            'workoutPlans' => fn ($q) => $q->latest('updated_at')->limit(20),
        ]);

        $sessionsCount = $user->workoutSessions()->count();
        $plansCount = $user->workoutPlans()->count();

        return view('admin.users.show', compact('user', 'sessionsCount', 'plansCount'));
    }

    public function edit(User $user): View
    {
        Gate::authorize('admin');

        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateAdminUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('admin');

        $data = $request->safe()->only(['name', 'email', 'is_admin']);

        if ($user->is(auth()->user()) && array_key_exists('is_admin', $data) && ! $data['is_admin']) {
            return redirect()
                ->back()
                ->with('error', __('You cannot remove your own admin privileges here.'));
        }

        if ($request->filled('password')) {
            // نموذج User يطبّق cast «hashed» على كلمة المرور — نمرّر النص الصريح فقط.
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        if (User::query()->where('is_admin', true)->count() === 0) {
            $user->update(['is_admin' => true]);

            return redirect()
                ->back()
                ->with('error', __('At least one admin must remain.'));
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', __('User updated.'));
    }

    /**
     * حذف مستخدم وجميع بياناته المرتبطة (cascade على الجداول المعرّفة في الهجرات).
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('admin');

        if ($user->is(auth()->user())) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', __('You cannot delete your own account from the admin panel.'));
        }

        if ($user->is_admin && User::query()->where('is_admin', true)->count() <= 1) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', __('Cannot delete the last admin account.'));
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('User deleted.'));
    }

    /**
     * تبديل صلاحية المشرف — لا يمكن إزالة صلاحية admin عن نفسك.
     */
    public function toggleAdmin(User $user): RedirectResponse
    {
        Gate::authorize('admin');

        if ($user->is(auth()->user()) && $user->is_admin) {
            return redirect()
                ->back()
                ->with('error', __('You cannot remove admin privileges from yourself.'));
        }

        if ($user->is_admin && User::query()->where('is_admin', true)->count() <= 1) {
            return redirect()
                ->back()
                ->with('error', __('There must be at least one admin account.'));
        }

        $user->update(['is_admin' => ! $user->is_admin]);

        return redirect()
            ->back()
            ->with('status', __('Admin status updated.'));
    }
}
