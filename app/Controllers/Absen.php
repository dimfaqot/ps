<?php

namespace App\Controllers;

class Absen extends BaseController
{
    // function __construct()
    // {
    //     helper('functions');
    //     check_role();
    // }
    public function index(): string
    {

        return view('absen', ['judul' => 'Home - ABSEN']);
    }
}
