<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PaymentModel;

class AccountController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $paymentModel = new PaymentModel();

        // Assuming user ID is stored in session after login
        $userId = session()->get('userId'); // Corrected session key to match AuthController

        if (!$userId) {
            // Redirect to login if user is not logged in
            return redirect()->to(url_to('login'));
        }

        $user = $userModel->find($userId);
        $transactions = $paymentModel->where('user_id', $userId)->findAll();

        if (!$user) {
            // Handle case where user is not found (should not happen if logged in)
            return redirect()->to(url_to('home'))->with('error', 'User not found.');
        }

        $data = [
            'pageTitle' => 'My Account',
            'user' => $user,
            'transactions' => $transactions,
        ];

        return view('account/index', $data);
    }
}
