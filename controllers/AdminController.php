<?php

namespace Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Schema;
use App\Core\Session;
use Models\AdminStats;
use Models\ReceiptShrinker;
use Models\User;

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

    // POST /dashboard-admin/shrink-receipts — one time-boxed batch of the old-receipt clean-up
    public function shrinkReceipts(): void
    {
        CSRF::check();
        if (!Schema::ensureBilling()) {
            $this->json(['ok' => false], 500);
        }
        @set_time_limit(60);
        $result = (new ReceiptShrinker())->run((string) ($_POST['cursor'] ?? ''));
        $this->json(['ok' => true] + $result);
    }

    // POST /dashboard-admin/users/{id}/delete — removes a spam/test sign-up and all its data
    public function deleteUser(string $id): void
    {
        CSRF::check();
        $userModel = new User();
        $user      = $userModel->findById((int) $id);

        if (!$user || ($user['role'] ?? '') !== 'client' || (int) $user['id'] === Auth::id()) {
            Session::flash('error', __('adm_delete_not_allowed'));
            $this->back('/dashboard-admin');
        }
        if (Schema::ensureBilling()) {
            $paid = getDB()->prepare("SELECT COUNT(*) FROM payments WHERE user_id = ? AND status = 'paid'");
            $paid->execute([$user['id']]);
            if ((int) $paid->fetchColumn() > 0) {
                Session::flash('error', __('adm_delete_has_payments'));
                $this->back('/dashboard-admin');
            }
        }

        foreach ($userModel->deleteWithData((int) $user['id']) as $path) {
            $full = BASE_PATH . '/' . $path;
            if (is_file($full)) {
                @unlink($full);
            }
        }
        Session::flash('success', __('adm_deleted', ['email' => $user['email']]));
        $this->back('/dashboard-admin');
    }
}
