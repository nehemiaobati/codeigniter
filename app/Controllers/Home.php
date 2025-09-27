<?php declare(strict_types=1);

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'pageTitle' => 'Welcome, ' . session()->get('username'),
            'username'  => session()->get('username'),
            'email'     => session()->get('userEmail'), // Corrected to match session key
            'member_since' => session()->get('member_since'), // Assuming 'member_since' is in session
        ];
        return view('home/welcome_user', $data);
    }

    public function landing(): string
    {
        $data = [
            'pageTitle' => 'Welcome to Our Custom Landing Page!',
            'heroTitle' => 'Build Your Dreams with Us',
            'heroSubtitle' => 'We provide innovative solutions to help you succeed.',
        ];
        return view('home/landing_page', $data);
    }
}
