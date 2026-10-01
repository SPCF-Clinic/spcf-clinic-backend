<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Dashboard\IndexDashboardRepository;

class DashboardController extends Controller
{
    protected $index;

    public function __construct(IndexDashboardRepository $index)
    {
        $this->index = $index;
    }

    public function index()
    {
        return $this->index->execute();
    }
}
