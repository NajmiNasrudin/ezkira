<?php
/**
 * Expenses → Pinjaman: loan cards, add/edit modal and "Bayar ansuran" modal.
 *
 * @var array $loans  Models\Loan::forUser() rows (terms + live state)
 * @var int   $year
 * @var int   $month
 */
$le    = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$lname = fn($v) => $le(html_entity_decode((string) $v, ENT_QUOTES, 'UTF-8')); // lender is stored escaped
$rm    = fn($v) => 'RM ' . number_format((float) $v, 2);
$today = date('Y-m-d');
$input = 'w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 outline-none';
$lbl   = 'block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1';
?>
<!-- ===== LOANS (PINJAMAN) ===== -->
<div id="loans" class="mb-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden scroll-mt-20">
    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-2 h-8 rounded-full bg-rose-500"></div>
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white"><?= __('loans') ?></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400"><?= __('loans_subtitle') ?></p>
            </div>
        </div>
        <button type="button" onclick="openLoanForm()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <?= __('loan_add') ?>
        </button>
    </div>

    <?php if (!$loans): ?>
        <p class="px-6 py-6 text-sm text-gray-500 dark:text-gray-400"><?= __('loans_empty') ?></p>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 sm:p-6">
        <?php foreach ($loans as $loan):
            $done    = $loan['outstanding'] <= 0;
            $overdue = !$done && $loan['next_due'] < $today;
            $nextAmt = min((float) $loan['installment'], $loan['outstanding'] + \App\Core\LoanMath::interestFor(
                $loan['outstanding'], (float) $loan['principal'], (float) $loan['rate'], $loan['rate_type']));
            $js = [
                'id' => (int) $loan['id'], 'lender' => html_entity_decode($loan['lender'], ENT_QUOTES, 'UTF-8'),
                'principal' => (float) $loan['principal'], 'rate' => (float) $loan['rate'], 'rate_type' => $loan['rate_type'],
                'months' => (int) $loan['months'], 'installment' => (float) $loan['installment'],
                'loan_date' => $loan['loan_date'], 'first_due_date' => $loan['first_due_date'],
                'paid_before' => (int) $loan['paid_before'], 'add_to_cash' => (int) $loan['add_to_cash'],
                'outstanding' => $loan['outstanding'], 'next_amount' => round($nextAmt, 2), 'next_no' => $loan['paid_count'] + 1,
            ];
        ?>
        <div class="rounded-xl border <?= $done ? 'border-gray-200 dark:border-gray-700 opacity-70' : 'border-gray-200 dark:border-gray-700' ?> p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900 dark:text-white truncate"><?= $lname($loan['lender']) ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        <?= $rm($loan['principal']) ?> · <?= rtrim(rtrim(number_format((float) $loan['rate'], 3), '0'), '.') ?>% ·
                        <?= __($loan['rate_type'] === 'flat' ? 'loan_type_flat' : 'loan_type_reducing') ?> · <?= (int) $loan['months'] ?> <?= __('loan_months_short') ?>
                    </p>
                </div>
                <?php if ($done): ?>
                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-bold bg-brand-700 text-white"><?= __('loan_settled') ?></span>
                <?php endif; ?>
            </div>

            <div class="mt-3">
                <div class="flex items-baseline justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400"><?= __('loan_balance') ?></span>
                    <strong class="text-rose-600 dark:text-rose-400"><?= $rm($loan['outstanding']) ?></strong>
                </div>
                <div class="mt-1.5 h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                    <div class="h-full rounded-full bg-brand-600" style="width: <?= (int) $loan['progress'] ?>%"></div>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                    <?= __('loan_paid_of', ['n' => (int) $loan['paid_count'], 'total' => (int) $loan['months'], 'pct' => (int) $loan['progress']]) ?>
                </p>
            </div>

            <dl class="grid grid-cols-2 gap-x-3 gap-y-1.5 mt-3 text-xs">
                <dt class="text-gray-500 dark:text-gray-400"><?= __('loan_installment') ?></dt>
                <dd class="text-right font-semibold text-gray-900 dark:text-white"><?= $rm($loan['installment']) ?></dd>
                <?php if (!$done): ?>
                    <dt class="text-gray-500 dark:text-gray-400"><?= __('loan_next_due') ?></dt>
                    <dd class="text-right font-semibold <?= $overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' ?>">
                        <?= date('d M Y', strtotime($loan['next_due'])) ?><?= $overdue ? ' · ' . __('loan_overdue') : '' ?>
                    </dd>
                    <dt class="text-gray-500 dark:text-gray-400"><?= __('loan_months_left') ?></dt>
                    <dd class="text-right font-semibold text-gray-900 dark:text-white">
                        <?= $loan['months_left'] === null ? __('loan_never') : (int) $loan['months_left'] . ' ' . __('loan_months_short') ?>
                    </dd>
                <?php endif; ?>
                <dt class="text-gray-500 dark:text-gray-400"><?= __('loan_interest_paid') ?></dt>
                <dd class="text-right font-semibold text-gray-900 dark:text-white"><?= $rm($loan['interest_paid']) ?></dd>
            </dl>

            <div class="flex items-center gap-2 mt-4">
                <?php if (!$done): ?>
                    <button type="button" onclick='openLoanPay(<?= $le(json_encode($js)) ?>)'
                            class="flex-1 px-3 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors">
                        <?= __('loan_pay') ?>
                    </button>
                <?php endif; ?>
                <button type="button" onclick='openLoanForm(<?= $le(json_encode($js)) ?>)'
                        class="px-3 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <?= __('loan_edit_btn') ?>
                </button>
                <form method="POST" action="<?= BASE_URI ?>/expenses/loans/<?= (int) $loan['id'] ?>/delete"
                      onsubmit="return confirm(<?= $le(json_encode(__('loan_confirm_delete'))) ?>)">
                    <?= \App\Core\CSRF::field() ?>
                    <input type="hidden" name="year" value="<?= (int) $year ?>">
                    <input type="hidden" name="month" value="<?= (int) $month ?>">
                    <button type="submit" class="px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                        <?= __('delete') ?>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Add / edit loan -->
<div id="loan-form-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 id="loan-form-title" class="font-semibold text-gray-900 dark:text-white"><?= __('loan_add') ?></h3>
            <button type="button" onclick="closeLoanModal('loan-form-modal')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form id="loan-form" method="POST" action="<?= BASE_URI ?>/expenses/loans/store" class="overflow-y-auto px-6 py-4 space-y-3">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="year" value="<?= (int) $year ?>">
            <input type="hidden" name="month" value="<?= (int) $month ?>">

            <div>
                <label class="<?= $lbl ?>"><?= __('loan_lender') ?></label>
                <input type="text" name="lender" maxlength="100" required placeholder="<?= $le(__('loan_lender_ph')) ?>" class="<?= $input ?>">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="<?= $lbl ?>"><?= __('loan_principal') ?></label>
                    <input type="number" name="principal" step="0.01" min="1" required class="<?= $input ?>" oninput="loanPreview()">
                </div>
                <div>
                    <label class="<?= $lbl ?>"><?= __('loan_rate') ?></label>
                    <input type="number" name="rate" step="0.001" min="0" max="100" required class="<?= $input ?>" oninput="loanPreview()">
                </div>
            </div>
            <div>
                <label class="<?= $lbl ?>"><?= __('loan_rate_type') ?></label>
                <div class="grid grid-cols-2 gap-2">
                    <?php foreach (['reducing', 'flat'] as $t): ?>
                        <label class="flex items-start gap-2 p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50 dark:has-[:checked]:bg-brand-900/20">
                            <input type="radio" name="rate_type" value="<?= $t ?>" <?= $t === 'reducing' ? 'checked' : '' ?> class="mt-0.5" onchange="loanPreview()">
                            <span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white"><?= __('loan_type_' . $t) ?></span>
                                <span class="block text-[11px] text-gray-500 dark:text-gray-400"><?= __('loan_type_' . $t . '_hint') ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="<?= $lbl ?>"><?= __('loan_months') ?></label>
                    <input type="number" name="months" step="1" min="1" max="600" required class="<?= $input ?>" oninput="loanPreview()">
                </div>
                <div>
                    <label class="<?= $lbl ?>"><?= __('loan_installment') ?></label>
                    <input type="number" name="installment" step="0.01" min="0.01" class="<?= $input ?>" placeholder="<?= $le(__('loan_auto')) ?>">
                </div>
            </div>
            <p id="loan-preview" class="text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/40 rounded-lg px-3 py-2 hidden"></p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 -mt-1"><?= __('loan_installment_hint') ?></p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="<?= $lbl ?>"><?= __('loan_date') ?></label>
                    <input type="date" name="loan_date" required value="<?= $today ?>" class="<?= $input ?>">
                </div>
                <div>
                    <label class="<?= $lbl ?>"><?= __('loan_first_due') ?></label>
                    <input type="date" name="first_due_date" class="<?= $input ?>">
                </div>
            </div>
            <div>
                <label class="<?= $lbl ?>"><?= __('loan_paid_before') ?></label>
                <input type="number" name="paid_before" step="1" min="0" value="0" class="<?= $input ?>">
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1"><?= __('loan_paid_before_hint') ?></p>
            </div>
            <label class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" name="add_to_cash" value="1" checked class="mt-0.5">
                <span><?= __('loan_add_to_cash') ?><span class="block text-[11px] text-gray-500 dark:text-gray-400"><?= __('loan_add_to_cash_hint') ?></span></span>
            </label>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeLoanModal('loan-form-modal')" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"><?= __('cancel') ?></button>
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg"><?= __('save') ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Pay an instalment -->
<div id="loan-pay-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 id="loan-pay-title" class="font-semibold text-gray-900 dark:text-white"><?= __('loan_pay') ?></h3>
            <button type="button" onclick="closeLoanModal('loan-pay-modal')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form id="loan-pay-form" method="POST" enctype="multipart/form-data" class="overflow-y-auto px-6 py-4 space-y-3">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="year" value="<?= (int) $year ?>">
            <input type="hidden" name="month" value="<?= (int) $month ?>">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="<?= $lbl ?>"><?= __('amount') ?> (RM)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required class="<?= $input ?>" oninput="loanPaySplit()">
                </div>
                <div>
                    <label class="<?= $lbl ?>"><?= __('date') ?></label>
                    <input type="date" name="pay_date" required value="<?= $today ?>" class="<?= $input ?>">
                </div>
            </div>
            <p id="loan-pay-split" class="text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/40 rounded-lg px-3 py-2"></p>
            <div>
                <label class="<?= $lbl ?>"><?= __('receipt') ?></label>
                <input type="file" name="receipts[]" multiple accept="image/*,application/pdf" onchange="ezShrinkImages(this)" class="block w-full text-xs text-gray-600 dark:text-gray-300">
            </div>
            <p class="text-[11px] text-gray-500 dark:text-gray-400"><?= __('loan_pay_hint') ?></p>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeLoanModal('loan-pay-modal')" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"><?= __('cancel') ?></button>
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg"><?= __('loan_pay_save') ?></button>
            </div>
        </form>
    </div>
</div>

<script>
// Same maths as App\Core\LoanMath, for live previews only — the server recalculates on save
(function () {
    var T = <?= json_encode([
        'add'      => __('loan_add'),
        'edit'     => __('loan_edit'),
        'preview'  => __('loan_preview'),
        'payTitle' => __('loan_pay_title'),
        'split'    => __('loan_pay_split'),
    ], JSON_UNESCAPED_UNICODE) ?>;
    var base = <?= json_encode(BASE_URI . '/expenses/loans/') ?>;
    var money = function (v) { return 'RM ' + Number(v).toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2}); };
    var fill = function (s, o) { return s.replace(/:(\w+)/g, function (m, k) { return k in o ? o[k] : m; }); };

    function installment(p, rate, n, type) {
        if (!(p > 0) || !(n > 0)) return 0;
        if (type === 'flat') return p / n + p * rate / 1200;
        var r = rate / 1200;
        return r > 0 ? p * r / (1 - Math.pow(1 + r, -n)) : p / n;
    }

    window.closeLoanModal = function (id) { document.getElementById(id).classList.add('hidden'); };

    window.loanPreview = function () {
        var f = document.getElementById('loan-form');
        var p = parseFloat(f.principal.value), rate = parseFloat(f.rate.value) || 0, n = parseInt(f.months.value, 10);
        var type = f.querySelector('[name=rate_type]:checked').value;
        var inst = installment(p, rate, n, type);
        var box = document.getElementById('loan-preview');
        if (!(inst > 0)) { box.classList.add('hidden'); return; }
        inst = Math.round(inst * 100) / 100;
        box.textContent = fill(T.preview, {inst: money(inst), interest: money(Math.max(0, inst * n - p)), total: money(inst * n)});
        box.classList.remove('hidden');
    };

    window.openLoanForm = function (loan) {
        var f = document.getElementById('loan-form');
        f.reset();
        document.getElementById('loan-form-title').textContent = loan ? T.edit : T.add;
        f.action = base + (loan ? loan.id + '/update' : 'store');
        if (loan) {
            ['lender', 'principal', 'rate', 'months', 'installment', 'loan_date', 'first_due_date', 'paid_before'].forEach(function (k) { f[k].value = loan[k]; });
            f.querySelector('[name=rate_type][value=' + loan.rate_type + ']').checked = true;
            f.add_to_cash.checked = !!loan.add_to_cash;
        }
        loanPreview();
        document.getElementById('loan-form-modal').classList.remove('hidden');
    };

    var payLoan = null;
    window.loanPaySplit = function () {
        var f = document.getElementById('loan-pay-form');
        var amt = parseFloat(f.amount.value) || 0;
        var l = payLoan, bal = l.outstanding;
        var interest = Math.round((l.rate_type === 'flat' ? l.principal : bal) * l.rate / 1200 * 100) / 100;
        interest = Math.min(amt, interest);
        var principal = Math.min(bal, Math.max(0, amt - interest));
        if (principal > 0 && bal - principal < 1) principal = bal;
        document.getElementById('loan-pay-split').textContent = fill(T.split, {
            principal: money(principal), interest: money(interest), balance: money(Math.max(0, bal - principal))
        });
    };

    window.openLoanPay = function (loan) {
        payLoan = loan;
        var f = document.getElementById('loan-pay-form');
        f.action = base + loan.id + '/pay';
        f.amount.value = loan.next_amount.toFixed(2);
        document.getElementById('loan-pay-title').textContent = fill(T.payTitle, {lender: loan.lender, n: loan.next_no, total: loan.months});
        loanPaySplit();
        document.getElementById('loan-pay-modal').classList.remove('hidden');
    };
})();
</script>
<!-- ===== END LOANS ===== -->
