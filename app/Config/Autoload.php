<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

class Autoload extends AutoloadConfig
{
    public $psr4 = [
        APP_NAMESPACE => APPPATH,
        //'App\Modules' => APPPATH . 'Modules', // This is the required addition
        'App\Modules\Blog' => APPPATH . 'Modules/Blog',
    ];

    public $classmap = [];

    public $files = [];

    public $helpers = ['cookie_consent_helper'];
}
