<?php

namespace App\Middleware;

use App\Core\Auth;

class RoleMiddleware
{
    public function __construct(private array $roles) {}

    public function handle(): void
    {
        if (!Auth::hasRole(...$this->roles)) {
            http_response_code(403);
            include BASE_PATH . '/views/errors/403.php';
            exit;
        }
    }
}
