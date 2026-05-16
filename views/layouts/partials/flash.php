<?php
use App\Core\Session;

$success = Session::getFlash('success');
$error   = Session::getFlash('error');
$info    = Session::getFlash('info');
$errors  = Session::getFlash('errors'); // Validation errors array
?>

<?php if ($success): ?>
<div class="mb-5 flex items-start gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flash-message" role="alert">
    <svg class="w-5 h-5 text-green-500 dark:text-green-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm text-green-800 dark:text-green-300 font-medium"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="mb-5 flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flash-message" role="alert">
    <svg class="w-5 h-5 text-red-500 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm text-red-800 dark:text-red-300 font-medium"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
</div>
<?php endif; ?>

<?php if ($info): ?>
<div class="mb-5 flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl flash-message" role="alert">
    <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm text-blue-800 dark:text-blue-300 font-medium"><?= htmlspecialchars($info, ENT_QUOTES, 'UTF-8') ?></p>
</div>
<?php endif; ?>

<?php if (!empty($errors) && is_array($errors)): ?>
<div class="mb-5 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flash-message">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm font-semibold text-red-800 dark:text-red-300">Please fix the following errors:</p>
    </div>
    <ul class="list-disc list-inside space-y-0.5">
        <?php foreach ($errors as $err): ?>
            <li class="text-sm text-red-700 dark:text-red-400"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
