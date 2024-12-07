<?php

namespace App\Controllers;

class Firebase_notif extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role('id');
    }
    public function index(): string
    {
        return view('firebase_notif', ['judul' => 'Home - Notif Firebase']);
    }
}
