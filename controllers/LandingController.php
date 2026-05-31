<?php

namespace Controllers;

use App\Core\Auth;

class LandingController extends \App\Core\Controller
{
    public function index(): void
    {
        // Logged-in users go straight to dashboard
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        // Render standalone landing page (has its own full HTML shell)
        include BASE_PATH . '/views/landing/index.php';
        exit;
    }
}
