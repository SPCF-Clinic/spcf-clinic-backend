<?php

namespace App\Repositories;

use App\Traits\Pagination;
use App\Traits\ResponseApi;

class BaseRepository
{
    use ResponseApi, Pagination;
}
