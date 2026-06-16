<?php
/**
 * Admin settings manager view
 */
$title = "Campaign Settings | Home Benefitts";
$path = '/admin/settings';
$baseDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
if ($baseDir === '/' || $baseDir === '\\') {
    $baseDir = '';
}
ob_start();
?>

<div class="w-full max-w-5xl mx-auto glass-panel rounded-3xl p-6 md:p-8 relative">

    <!-- Admin shared navigation -->
    <nav class="w-full flex flex-wrap gap-2 border-b border-slate-200/60 pb-4 mb-6 text-sm font-semibold">
        <a href="<?php echo htmlspecialchars($baseDir); ?>/admin"
            class="px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-900">
            <i class="fa-solid fa-chart-pie"></i> Dashboard
        </a>
        <a href="<?php echo htmlspecialchars($baseDir); ?>/admin/leads"
            class="px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-900">
            <i class="fa-solid fa-table-list"></i> Lead Management
        </a>
        <a href="<?php echo htmlspecialchars($baseDir); ?>/admin/settings"
            class="px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all bg-accent/10 text-accent shadow-sm shadow-accent/5">
            <i class="fa-solid fa-gears"></i> Campaign Settings
        </a>
        <div class="flex-grow"></div>
        <a href="<?php echo htmlspecialchars($baseDir); ?>/admin/logout"
            class="px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all text-red-500 hover:bg-red-50 hover:text-red-600">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>
    </nav>

    <!-- Header section -->
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-primary">Campaign Configurations</h1>
        <p class="text-xs text-slate-400 mt-1">Configure active API endpoint URLs and campaign security credentials.</p>
    </div>

    <!-- Alert Success -->
    <?php if (isset($message) && $message): ?>
        <div
            class="p-3.5 bg-green-50 border border-green-200 text-success text-xs rounded-xl mb-4 font-semibold flex items-center space-x-2">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    <?php endif; ?>

    <!-- Alert Error -->
    <?php if (isset($error) && $error): ?>
        <div
            class="p-3.5 bg-red-50 border border-red-200 text-red-500 text-xs rounded-xl mb-4 font-semibold flex items-center space-x-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5 max-w-2xl">
        <!-- CSRF Protection Field -->
        <?php echo CSRF::getFormField(); ?>

        <div class="grid grid-cols-1 gap-4">

            <!-- Campaign Code -->
            <div class="border border-slate-200/60 bg-white rounded-2xl p-5 shadow-sm">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Campaign Code</label>
                <input type="text" name="campaign_code" required
                    value="<?php echo htmlspecialchars($campaignCode); ?>"
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm font-semibold text-primary">
                <span class="block text-[10px] text-slate-400 mt-2">The unique identifier code assigned to your lead distribution campaign (e.g. <code>innovative-hand</code>).</span>
            </div>

            <!-- Campaign token -->
            <div class="border border-slate-200/60 bg-white rounded-2xl p-5 shadow-sm">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Campaign
                    Token</label>
                <input type="text" name="campaign_token" required
                    value="<?php echo htmlspecialchars($campaignToken); ?>"
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm font-semibold text-primary">
                <span class="block text-[10px] text-slate-400 mt-2">The unique identifier token linked to your lead
                    distribution campaign.</span>
            </div>

            <!-- Ping endpoint -->
            <div class="border border-slate-200/60 bg-white rounded-2xl p-5 shadow-sm">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ping API
                    Endpoint</label>
                <input type="url" name="ping_url" required value="<?php echo htmlspecialchars($pingUrl); ?>"
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm font-mono text-slate-600">
                <span class="block text-[10px] text-slate-400 mt-2">The endpoint URL that qualifies the initial non-PII
                    details. Default: <code>https://api.wiserleads.com/services/ping</code></span>
            </div>

            <!-- Post endpoint -->
            <div class="border border-slate-200/60 bg-white rounded-2xl p-5 shadow-sm">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Post API
                    Endpoint</label>
                <input type="url" name="post_url" required value="<?php echo htmlspecialchars($postUrl); ?>"
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm font-mono text-slate-600">
                <span class="block text-[10px] text-slate-400 mt-2">The endpoint URL that collects PII contact info for
                    final syndication. Default: <code>https://api.wiserleads.com/services/post</code></span>
            </div>

            <!-- Simulation switch -->
            <div
                class="border border-slate-200/60 bg-white rounded-2xl p-5 shadow-sm flex items-center justify-between">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">API Mock
                        Simulation Mode</label>
                    <span class="block text-[10px] text-slate-400">If active, outbound cURL API calls are mocked with
                        simulated successes and rejections without hitting the actual servers.</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="mock_mode" value="1" <?php echo $mockMode ? 'checked' : ''; ?>
                        class="sr-only peer">
                    <div
                        class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent">
                    </div>
                </label>
            </div>

        </div>

        <div class="flex justify-end pt-2">
            <button type="submit"
                class="px-6 py-2.5 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 transition-colors text-sm shadow-md flex items-center gap-1.5">
                <i class="fa-solid fa-floppy-disk"></i> Save Campaign Settings
            </button>
        </div>
    </form>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>