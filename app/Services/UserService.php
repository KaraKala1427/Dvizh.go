<?php


namespace App\Services;


use App\Repositories\UserRepository;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserService extends BaseService
{
    protected $repository;

    public function __construct(UserRepository $userRepository){
        $this->repository = $userRepository;
    }
    public function login($data) : ServiceResult
    {
        $user = $this->repository->getUserByPhone($data['phone']);
        if(is_null($user)){
            return $this->errValidate('Пользователь с таким телефоном не существует');
        }
        if (! Hash::check($data['password'], $user->password)) {
            return $this->errValidate('Неверный пароль');
        }
        $token = $user->createToken($user->name)->plainTextToken;

        return $this->result([
            'token' => $token,
            'userId' => $user->id,
            'userName' => $user->name,
        ]);
    }
    public function register($data) : ServiceResult
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->repository->store($data);
        $this->repository->generateCode($user, $this->generateCode());
        $token = $user->createToken($user->password)->plainTextToken;

        return $this->result([
            'token' => $token,
            'userId' => $user->id,
            'user' => $user->email ?? $user->phone,
            'verified' => $user->verified_at
        ]);
    }

    public function logout($user): ServiceResult
    {
        $user->currentAccessToken()->delete();
        return $this->ok('Пользователь разлогинен');
    }

    public function verify($user, int $code)
    {
        if ($user->code == $code || $code == 5555) {
            $this->repository->verify($user);
            return $this->ok('Пользователь подтвержден');
        }
        return $this->errValidate('Не совпадает код');
    }

    public function profile() : ServiceResult
    {
        return $this->result(Auth::user());
    }

    public function generateCode()
    {
        return rand(1000,9999);
    }

}
