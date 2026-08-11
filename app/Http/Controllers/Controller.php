<?php

namespace App\Http\Controllers;

use App\Traits\ResponseApi;
use App\Traits\Pagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use ResponseApi, AuthorizesRequests, Pagination;
}
