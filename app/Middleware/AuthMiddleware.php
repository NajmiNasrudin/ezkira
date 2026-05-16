<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please log in to continue.');
            header('Location: ' . BASE_URI . '/login');
            exit;
        }
    }
}
