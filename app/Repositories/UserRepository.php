<?php


namespace App\Repositories;


use App\Models\User;

class UserRepository
{
    public function getUserByPhone($phone)
    {
        return User::where('phone',$phone)->first();
    }
    public function store($data)
    {
        return User::create($data);
    }

    public function generateCode(User $user, $code)
    {
        $user->code = $code;
        $user->save();
    }

    public function verify(User $user)
    {
        $user->verified_at = now();
        $user->save();
    }
}
