<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User;

class Auth extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function register()
    {
        helper(['form']);
        $data = [];
        return view('auth/register', $data);
    }

    public function store()
    {
        helper(['form']);
        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[255]',
            'confirmpassword' => 'matches[password]',
        ];

        if (! $this->validate($rules)) {
            return view('auth/register', [
                'validation' => $this->validator,
            ]);
        }

        $userModel = new User();
        $data = [
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
        ];
        $userModel->save($data);

        return redirect()->to('/login')->with('success', 'Registration Successful');
    }

    public function login()
    {
        helper(['form']);
        $data = [];
        return view('auth/login', $data);
    }

    public function authenticate()
    {
        helper(['form']);
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return view('auth/login', [
                'validation' => $this->validator,
            ]);
        }

        $userModel = new User();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $userModel->where('email', $email)->first();

        if (! $user || ! password_verify($password, $user->password)) {
            return redirect()->back()->withInput()->with('error', 'Invalid login credentials.');
        }

        $this->session->set([
            'isLoggedIn' => true,
            'userId'     => $user->id,
            'userEmail'  => $user->email,
            'username'   => $user->username, // Add username to session
        ]);

        return redirect()->to('/dashboard')->with('success', 'Login Successful');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login')->with('success', 'Logged out successfully.');
    }
}
