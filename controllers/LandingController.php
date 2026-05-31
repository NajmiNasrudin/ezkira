<?php

namespace Controllers;

use App\Core\Auth;

class LandingController extends \App\Core\Controller
{
    public function index(): void
    {
        // Logged-in users go straight to dashboard (unless ?preview=1)
        if (Auth::check() && ($_GET['preview'] ?? '') !== '1') {
            $this->redirect('/dashboard');
        }

        // Render standalone landing page (has its own full HTML shell)
        include BASE_PATH . '/views/landing/index.php';
        exit;
    }
}
