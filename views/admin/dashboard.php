<?php
/**
 * Admin Dashboard View
 */
$title = "Admin Dashboard | Home Benefitts";
$path = '/admin';
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
            class="px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all bg-accent/10 text-accent shadow-sm shadow-accent/5">
            <i class="fa-solid fa-chart-pie"></i> Dashboard
        </a>
        <a href="<?php echo htmlspecialchars($baseDir); ?>/admin/leads"
            class="px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-900">
            <i class="fa-solid fa-table-list"></i> Lead Management
        </a>
        <a href="<?php echo htmlspecialchars($baseDir); ?>/admin/settings"
            class="px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-900">
            <i class="fa-solid fa-gears"></i> Campaign Settings
        </a>
        <div class="flex-grow"></div>
        <a href="<?php echo htmlspecialchars($baseDir); ?>/admin/logout"
            class="px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all text-red-500 hover:bg-red-50 hover:text-red-600">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>
    </nav>

    <!-- Welcome section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-primary">Performance Dashboard</h1>
            <p class="text-xs text-slate-400 mt-1">Real-time syndication metrics for the home improvement campaign
                funnel.</p>
        </div>
        <a href="<?php echo htmlspecialchars($baseDir); ?>/admin/export"
            class="px-4 py-2.5 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 hover:-translate-y-0.5 transition-all text-xs flex items-center gap-1.5 shadow-md">
            <i class="fa-solid fa-file-csv"></i> Export Leads CSV
        </a>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Total -->
        <div
            class="border border-slate-200/60 border-t-4 border-t-blue-500 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Leads</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-users text-sm"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-primary tracking-tight"><?php echo $stats['total']; ?></div>
            <div class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                <i class="fa-regular fa-clock"></i> Drafts + Submissions
            </div>
        </div>

        <!-- Successful -->
        <div
            class="border border-slate-200/60 border-t-4 border-t-emerald-500 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Successful</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-emerald-600 tracking-tight"><?php echo $stats['success']; ?></div>
            <div class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                <i class="fa-solid fa-share-nodes"></i> Syndicated to partners
            </div>
        </div>

        <!-- Rejected -->
        <div
            class="border border-slate-200/60 border-t-4 border-t-rose-500 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rejected</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-xmark text-sm"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-rose-600 tracking-tight"><?php echo $stats['rejected']; ?></div>
            <div class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                <i class="fa-solid fa-triangle-exclamation"></i> Validation / ZIP errors
            </div>
        </div>

        <!-- Conversion Rate -->
        <div
            class="border border-slate-200/60 border-t-4 border-t-violet-500 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Conv. Rate</span>
                <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-sm"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-violet-600 tracking-tight"><?php echo $stats['conv_rate']; ?>%
            </div>
            <div class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                <i class="fa-solid fa-percent"></i> Success / Total leads
            </div>
        </div>

    </div>

    <!-- Quick Insights section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="border border-slate-200/60 bg-slate-50/50 rounded-2xl p-5 md:col-span-2">
            <h3 class="font-bold text-sm text-primary mb-3">Admin Quick Operations</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="<?php echo htmlspecialchars($baseDir); ?>/admin/leads"
                    class="group p-4 border border-slate-200/60 rounded-xl bg-white hover:border-accent/40 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center space-x-3 text-left">
                    <div
                        class="w-10 h-10 rounded-xl bg-blue-50 group-hover:bg-accent group-hover:text-white text-accent flex items-center justify-center flex-shrink-0 transition-all duration-300">
                        <i class="fa-solid fa-clipboard-list text-base"></i>
                    </div>
                    <div>
                        <span
                            class="block text-xs font-bold text-primary group-hover:text-accent transition-colors">Review
                            Leads Grid</span>
                        <span class="text-[10px] text-slate-400">Inspect PII logs, cURL request/response
                            payloads.</span>
                    </div>
                </a>
                <a href="<?php echo htmlspecialchars($baseDir); ?>/admin/settings"
                    class="group p-4 border border-slate-200/60 rounded-xl bg-white hover:border-accent/40 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center space-x-3 text-left">
                    <div
                        class="w-10 h-10 rounded-xl bg-purple-50 group-hover:bg-purple-600 group-hover:text-white text-purple-600 flex items-center justify-center flex-shrink-0 transition-all duration-300">
                        <i class="fa-solid fa-screwdriver-wrench text-base"></i>
                    </div>
                    <div>
                        <span
                            class="block text-xs font-bold text-primary group-hover:text-purple-600 transition-colors">API
                            Endpoint Settings</span>
                        <span class="text-[10px] text-slate-400">Configure WiserLeads integration routes.</span>
                    </div>
                </a>
            </div>
        </div>

        <?php
        $isMockMode = (bool) Setting::get('mock_mode', '1');
        ?>
        <div
            class="border border-slate-200/60 bg-slate-50/50 rounded-2xl p-5 flex flex-col justify-between hover:shadow-sm transition-all">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full <?php echo $isMockMode ? 'bg-amber-400' : 'bg-emerald-400'; ?> opacity-75"></span>
                        <span
                            class="relative inline-flex rounded-full h-2.5 w-2.5 <?php echo $isMockMode ? 'bg-amber-500' : 'bg-emerald-500'; ?>"></span>
                    </span>
                    <h3 class="font-bold text-sm text-primary">Simulation Mode:
                        <?php echo $isMockMode ? 'MOCKED' : 'LIVE'; ?></h3>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    By default, the campaign funnel routes in <strong>Mock Mode</strong>. This allows testing submission
                    results without writing live API endpoints. Toggle this mode off inside settings to send actual
                    payloads.
                </p>
            </div>
            <a href="<?php echo htmlspecialchars($baseDir); ?>/admin/settings"
                class="text-xs font-semibold text-accent hover:text-blue-700 mt-4 flex items-center gap-1 group">
                Configure Toggles <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>