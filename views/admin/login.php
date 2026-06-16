<?php
/**
 * Admin Login View
 */
$title = "Admin Login | Home Benefitts";
$baseDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
if ($baseDir === '/' || $baseDir === '\\') {
    $baseDir = '';
}
ob_start();
?>

<!-- Glowing backgrounds -->
<div class="glow-blur-1"></div>
<div class="glow-blur-2"></div>

<div class="w-full max-w-md mx-auto glass-panel rounded-3xl p-6 md:p-8 relative">

    <div class="text-center mb-6">
        <div
            class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-accent to-blue-500 flex items-center justify-center text-white shadow-md mx-auto mb-3">
            <i class="fa-solid fa-user-lock text-lg"></i>
        </div>
        <h1 class="text-2xl font-extrabold tracking-tight text-primary">Admin Access</h1>
        <p class="text-xs text-slate-400 mt-1">Please enter credentials to access dashboard statistics.</p>
    </div>

    <!-- Error block -->
    <?php if (isset($error) && $error): ?>
        <div
            class="p-3.5 bg-red-50 border border-red-200 text-red-500 text-xs rounded-xl mb-4 font-semibold flex items-center space-x-2 animate-pulse">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <!-- CSRF Token field -->
        <?php echo CSRF::getFormField(); ?>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Username</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">
                    <i class="fa-solid fa-user"></i>
                </span>
                <input type="text" name="username" required placeholder="e.g. admin"
                    class="w-full pl-9 pr-4 py-2.5 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full pl-9 pr-4 py-2.5 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm">
            </div>
        </div>

        <button type="submit"
            class="w-full py-2.5 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 transition-colors text-sm shadow-md mt-6">
            Log In <i class="fa-solid fa-right-to-bracket ml-1 text-xs"></i>
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="<?php echo htmlspecialchars($baseDir ?: '/'); ?>"
            class="text-xs font-semibold text-slate-400 hover:text-accent transition-all">
            <i class="fa-solid fa-arrow-left mr-1"></i> Return to Funnel
        </a>
    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>