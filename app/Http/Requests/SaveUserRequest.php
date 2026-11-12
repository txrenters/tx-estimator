<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $target */
        $target = $this->route('user');

        return $target
            ? $this->user()->can('update', $target)
            : $this->user()->can('create', User::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var User|null $target */
        $target = $this->route('user');
        $assignable = array_map(fn (Role $r) => $r->value, UserPolicy::assignableRoles($this->user()));

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($target?->id)],
            'role' => ['required', Rule::in($assignable)],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['role.in' => 'You cannot assign this role.'];
    }

    /** @return list<\Closure(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $role = Role::tryFrom((string) $this->input('role'));

                if ($role && ! $role->isGlobal() && $this->user()->isGlobal() && ! $this->filled('company_id')) {
                    $validator->errors()->add('company_id', 'Choose a company for this user.');
                }

                /** @var User|null $target */
                $target = $this->route('user');
                if ($target && $target->isSuperadmin() && $role !== Role::Superadmin
                    && User::where('role', Role::Superadmin)->where('is_active', true)->whereKeyNot($target->id)->doesntExist()) {
                    $validator->errors()->add('role', 'At least one active superadmin is required.');
                }
            },
        ];
    }

    /** The company the saved user belongs to (null for global roles). */
    public function companyId(): ?int
    {
        $role = Role::from($this->validated('role'));

        if ($role->isGlobal()) {
            return null;
        }

        return $this->user()->isGlobal() ? (int) $this->validated('company_id') : $this->user()->company_id;
    }
}
