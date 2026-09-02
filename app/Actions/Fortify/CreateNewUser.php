<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    // public function create(array $input): User
    // {
    //     Validator::make($input, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => $this->passwordRules(),
    //         'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
    //     ])->validate();

    //     return User::create([
    //         'name' => $input['name'],
    //         'email' => $input['email'],
    //         'password' => Hash::make($input['password']),
    //     ]);
    // }
    public function create(array $input): User
{
    Validator::make($input, [
        'username' => ['required', 'string', 'max:50', 'unique:users'], // เพิ่ม username
        'first_name' => ['required', 'string', 'max:100'],              // เพิ่มชื่อ
        'last_name' => ['required', 'string', 'max:100'],               // เพิ่มนามสกุล
        'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
        'phone_number' => ['nullable', 'string', 'max:20'],            // เพิ่มเบอร์โทร
        'password' => $this->passwordRules(),
        'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
    ])->validate();

    return User::create([
        'username' => $input['username'],
        'first_name' => $input['first_name'],
        'last_name' => $input['last_name'],
        'email' => $input['email'],
        // 'phone_number' => $input['phone_number'],
        'password' => Hash::make($input['password']),
        'role_id' => 3, // กำหนดให้สมาชิกใหม่เป็น User ทั่วไป
        'coin_balance' => 0,
    ]);
}
}
