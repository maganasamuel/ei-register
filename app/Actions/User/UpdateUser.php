<?php

namespace App\Actions\User;

use App\Models\User;
use App\Traits\Validators\UserValidator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class UpdateUser
{
    use UserValidator;

    public function update($input, User $user)
    {
        $rules = $this->userRules();

        $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)];
        $rules['password'] = ['nullable', 'string', new Password(), 'confirmed'];

        $data = Validator::make($input, $rules, [], $this->userAttributes())->validate();

        $role = $data['role'];

        unset($data['role']);

        $user->update($data);

        $user->syncRoles([$role]);

        return $user;
    }
}
