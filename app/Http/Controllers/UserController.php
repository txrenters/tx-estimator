<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\SaveUserRequest;
use App\Models\User;
use App\Notifications\InviteUser;
use App\Policies\UserPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);
        $actor = $request->user();

        $users = User::query()
            ->with('company')
            ->when(! $actor->isGlobal(), fn ($q) => $q->where('company_id', $actor->company_id))
            ->when($actor->isGlobal() && $request->filled('company'), fn ($q) => $q->where('company_id', $request->integer('company')))
            ->orderBy('name')
            ->get()
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role->value,
                'roleLabel' => $u->role->label(),
                'companyId' => $u->company_id,
                'company' => $u->company?->name,
                'isActive' => $u->is_active,
                'canEdit' => $actor->can('update', $u),
                'canToggle' => $actor->can('toggleActive', $u),
            ]);

        return Inertia::render('users/Index', [
            'users' => $users,
            'roles' => array_map(fn (Role $r) => $r->value, UserPolicy::assignableRoles($actor)),
            'filters' => ['company' => $request->input('company', '')],
        ]);
    }

    public function store(SaveUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            'company_id' => $request->companyId(),
            'password' => Str::password(32),
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->sendInvite($user);

        return back();
    }

    public function invite(User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $this->sendInvite($user);

        return back();
    }

    /**
     * Send a set-password invitation. Each link expires (auth.passwords.*.expire),
     * so managers can resend; resends are throttled by the password broker.
     */
    private function sendInvite(User $user): void
    {
        $status = Password::sendResetLink(
            ['email' => $user->email],
            fn (User $u, string $token) => $u->notify(new InviteUser($token)),
        );

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['invite' => __($status)]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => "Invitation sent to {$user->email}."]);
    }

    public function update(SaveUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $user->update([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            'company_id' => $request->companyId(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'User updated.']);

        return back();
    }

    public function toggleActive(User $user): RedirectResponse
    {
        Gate::authorize('toggleActive', $user);

        $user->update(['is_active' => ! $user->is_active]);

        Inertia::flash('toast', ['type' => 'success', 'message' => $user->is_active ? 'User reactivated.' : 'User deactivated.']);

        return back();
    }
}
