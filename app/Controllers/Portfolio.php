<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Portfolio extends BaseController
{
    public function index()
    {
        return view('portfolio');
    }
}
