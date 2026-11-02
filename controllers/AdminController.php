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

        $this->view('admin/index', [
            'users'          => $stats->users(),
            'active'         => $stats->active(),
            'revenue'        => $stats->revenue(),
            'usage'          => $stats->usage(),
            'perDay'         => $stats->signupsPerDay(30),
            'perMonth'       => $stats->signupsPerMonth(),
            'businessTypes'  => $stats->businessTypes(),
            'languages'      => $stats->languages(),
            'recentUsers'    => $stats->recentUsers(20),
            'topUsers'       => $stats->topUsers(10),
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
