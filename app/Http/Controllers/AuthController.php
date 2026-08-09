<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\{
    RegisterRequest,
    LoginRequest,
};

use App\Repositories\Auth\{
    RegisterRepository,
    LoginRepository,
    LogoutRepository,
};

class AuthController extends Controller
{
    protected $register, $login, $logout;

    public function __construct(
        RegisterRepository $register,
        LoginRepository $login,
        LogoutRepository $logout
    ) {
        $this->register = $register;
        $this->login = $login;
        $this->logout = $logout;
    }

    public function register(RegisterRequest $request)
    {
        return $this->register->execute($request);
    }

    public function login(LoginRequest $request)
    {
        return $this->login->execute($request);
    }

    public function logout()
    {
        return $this->logout->execute();
    }
}