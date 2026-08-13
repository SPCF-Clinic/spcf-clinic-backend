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
    ShowAuthenticatedUserRepository,
};

class AuthController extends Controller
{
    protected $register, $login, $logout, $showAuthenticatedUser;

    public function __construct(
        RegisterRepository $register,
        LoginRepository $login,
        LogoutRepository $logout,
        ShowAuthenticatedUserRepository $showAuthenticatedUser
    ) {
        $this->register = $register;
        $this->login = $login;
        $this->logout = $logout;
        $this->showAuthenticatedUser = $showAuthenticatedUser;
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

    public function authenticatedUser()
    {
        return $this->showAuthenticatedUser->execute();
    }
}