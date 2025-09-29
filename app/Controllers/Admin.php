<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;

class Admin extends BaseController
{
    public function index()
    {
        $userModel = new User();
        $data['users'] = $userModel->findAll();
        $data['total_balance'] = $userModel->selectSum('balance')->first()->balance;

        return view('admin/index', $data);
    }

    public function show($id)
    {
        $userModel = new User();
        $data['user'] = $userModel->find($id);

        return view('admin/show', $data);
    }

    public function updateBalance($id)
    {
        $userModel = new User();
        $user = $userModel->find($id);

        $amount = $this->request->getPost('amount');
        $action = $this->request->getPost('action');

        if ($action === 'deposit') {
            $newBalance = $user->balance + $amount;
        } elseif ($action === 'withdraw') {
            $newBalance = $user->balance - $amount;
        }

        $userModel->update($id, ['balance' => $newBalance]);

        return redirect()->to(url_to('admin.users.show', $id))->with('success', 'Balance updated successfully.');
    }

    public function delete($id)
    {
        $userModel = new User();
        $userModel->delete($id);

        return redirect()->to(url_to('admin.index'))->with('success', 'User deleted successfully.');
    }
}
