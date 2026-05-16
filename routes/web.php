<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

// ============================================================
// Guest routes (redirect to dashboard if already logged in)
// ============================================================
$router->get('/login',     'AuthController@loginForm',    [GuestMiddleware::class]);
$router->post('/login',    'AuthController@login',         [GuestMiddleware::class]);
$router->get('/register',  'AuthController@registerForm', [GuestMiddleware::class]);
$router->post('/register', 'AuthController@register',      [GuestMiddleware::class]);

// ============================================================
// Authenticated routes
// ============================================================
$router->post('/logout',   'AuthController@logout',        [AuthMiddleware::class]);

// Dashboard
$router->get('/dashboard', 'DashboardController@index',    [AuthMiddleware::class]);

// Revenue
$router->get('/revenue',                         'RevenueController@index',     [AuthMiddleware::class]);
$router->post('/revenue/store',                  'RevenueController@store',     [AuthMiddleware::class]);
$router->post('/revenue/target',                 'RevenueController@setTarget', [AuthMiddleware::class]);
$router->post('/revenue/{id}/delete',            'RevenueController@delete',    [AuthMiddleware::class]);
$router->get('/revenue/export-pnl',             'RevenueController@exportPnl', [AuthMiddleware::class]);

// Expenses
$router->get('/expenses',                        'ExpenseController@index',       [AuthMiddleware::class]);
$router->post('/expenses/store',                 'ExpenseController@store',       [AuthMiddleware::class]);
$router->post('/expenses/budget-pct',            'ExpenseController@saveBudgetPct',[AuthMiddleware::class]);
$router->get('/expenses/receipt/{id}',           'ExpenseController@receipt',  [AuthMiddleware::class]);
$router->post('/expenses/{id}/delete',           'ExpenseController@delete',   [AuthMiddleware::class]);

// Profile
$router->get('/profile',                 'ProfileController@index',       [AuthMiddleware::class]);
$router->post('/profile/update',         'ProfileController@update',      [AuthMiddleware::class]);
$router->post('/profile/password',       'ProfileController@password',    [AuthMiddleware::class]);
$router->post('/profile/avatar',         'ProfileController@avatar',      [AuthMiddleware::class]);
$router->post('/profile/preferences',    'ProfileController@preferences', [AuthMiddleware::class]);

// ============================================================
// Utility routes (no auth required — handle gracefully)
// ============================================================
$router->post('/lang/switch',   'AuthController@switchLang',   []);
$router->post('/theme/toggle',  'AuthController@toggleTheme',  []);

// ============================================================
// Root redirect
// ============================================================
$router->get('/', 'DashboardController@index', [AuthMiddleware::class]);
