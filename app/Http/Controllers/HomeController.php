<?php

namespace App\Http\Controllers;

use App\Services\OrderService;

class HomeController extends Controller
{

    public function index()
    {
        return view('home',
            [
                'title' => 'Home',
                'managersOrders' => OrderService::getCountOrders('manager'),
                'expertsOrders' => OrderService::getCountOrders('expert'),
            ]);
    }
}
