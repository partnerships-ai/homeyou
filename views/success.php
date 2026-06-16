<?php
/**
 * Success landing page
 */
$title = "Quotes Matched Successfully! | Home Benefitts";
$baseDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
if ($baseDir === '/' || $baseDir === '\\') {
    $baseDir = '';
}
ob_start();
?>

<!-- Glowing backgrounds -->
<div class="glow-blur-1"></div>
<div class="glow-blur-2"></div>

<div class="w-full max-w-2xl mx-auto glass-panel rounded-3xl p-8 md:p-12 text-center relative overflow-hidden">

    <!-- Animated success green ring -->
    <div
        class="w-20 h-20 rounded-full bg-green-50 text-success flex items-center justify-center mx-auto mb-6 shadow-md shadow-success/10 animate-bounce">
        <i class="fa-solid fa-check text-4xl"></i>
    </div>

    <h1 class="text-3xl font-extrabold tracking-tight text-primary leading-tight">
        <?php if (isset($lead) && $lead && !empty($lead['first_name'])): ?>
            Thank You, <?php echo htmlspecialchars($lead['first_name']); ?>!
        <?php else: ?>
            You're Matched!
        <?php endif; ?>
    </h1>
    <p class="mt-4 text-slate-600 text-sm max-w-md mx-auto leading-relaxed">
        Excellent news! We have successfully matched your request with top-rated local professionals who service your
        neighborhood.
    </p>

    <!-- Lead details preview box -->
    <?php if (isset($lead) && $lead): ?>
        <div class="mt-8 border border-slate-200/60 rounded-2xl bg-white p-5 text-left max-w-md mx-auto">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3.5 flex items-center">
                <i class="fa-solid fa-file-invoice mr-1.5 text-accent"></i> Project Summary
            </h3>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Service:</span>
                    <span class="font-bold text-primary"><?php echo htmlspecialchars($lead['service']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">ZIP Code:</span>
                    <span class="font-bold text-primary"><?php echo htmlspecialchars($lead['zip_code']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Contact:</span>
                    <span class="font-bold text-primary">
                        <?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?>
                    </span>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-2.5 mt-2.5">
                    <span class="text-slate-500">Status:</span>
                    <span
                        class="inline-flex items-center text-xs font-semibold text-success bg-green-50 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-success mr-1.5 animate-pulse"></span> Processing Match
                    </span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-8 space-y-4">
        <div
            class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50 max-w-md mx-auto text-left flex items-start space-x-3">
            <i class="fa-solid fa-circle-info text-accent text-lg mt-0.5 flex-shrink-0"></i>
            <div>
                <h4 class="text-xs font-bold text-accent uppercase tracking-wider">What happens next?</h4>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    1. Matches will review your project specs immediately.<br>
                    2. Up to 3 pre-screened contractors will call, text or email you with estimates.<br>
                    3. Compare rates and choose the best pro for your budget!
                </p>
            </div>
        </div>
    </div>

    <div class="mt-10 pt-6 border-t border-slate-200/60 max-w-sm mx-auto flex justify-center space-x-6">
        <a href="<?php echo htmlspecialchars($baseDir ?: '/'); ?>"
            class="text-sm font-semibold text-accent hover:text-blue-700 transition-colors flex items-center">
            <i class="fa-solid fa-rotate-left mr-1.5 text-xs"></i> Submit Another Request
        </a>
    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>