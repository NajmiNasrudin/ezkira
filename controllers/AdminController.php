<?php

namespace Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Schema;
use Models\AdminStats;

class AdminController extends Controller
{
    // GET /dashboard-admin (admin only — RoleMiddleware on the route)
    public function index(): void
    {
        $stats = new AdminStats(Schema::ensureBilling(), Schema::ensureAdminInvites());

        $sort   = array_key_exists($_GET['sort'] ?? '', AdminStats::USER_SORTS) ? $_GET['sort'] : 'newest';
        $plan   = in_array($_GET['plan'] ?? '', ['pro', 'free'], true) ? $_GET['plan'] : 'all';
        $search = trim(mb_substr((string) ($_GET['q'] ?? ''), 0, 100));
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 50;

        $this->view('admin/index', [
            'users'          => $stats->users(),
            'active'         => $stats->active(),
            'revenue'        => $stats->revenue(),
            'usage'          => $stats->usage(),
            'perDay'         => $stats->signupsPerDay(30),
            'perMonth'       => $stats->signupsPerMonth(),
            'businessTypes'  => $stats->businessTypes(),
            'languages'      => $stats->languages(),
            'userList'       => $stats->allUsers($sort, $plan, $search, $page, $perPage),
            'userQuery'      => ['sort' => $sort, 'plan' => $plan, 'q' => $search, 'page' => $page, 'per' => $perPage],
            'upsell'         => $stats->upsellCandidates(20),
            'recentPayments' => $stats->recentPayments(20),
        ], 'main', __('admin_dashboard'));
    }

    // POST /dashboard-admin/invite/{id} — marks a Pro WhatsApp invite as sent
    public function markInvited(string $id): void
    {
        CSRF::check();
        if (!Schema::ensureAdminInvites()) {
            $this->json(['ok' => false], 500);
        }
        $sentAt = (new AdminStats(Schema::ensureBilling(), true))->markInvited((int) $id);
        $this->json(['ok' => true, 'sent_at' => $sentAt]);
    }
}
