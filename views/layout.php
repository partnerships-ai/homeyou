<?php
/**
 * Main HTML layout frame
 */
$baseDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
if ($baseDir === '/' || $baseDir === '\\') {
    $baseDir = '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Premium Home Improvement Quotes | Home Benefitts'; ?>
    </title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F172A',
                        accent: '#2563EB',
                        success: '#22C55E',
                        background: '#F8FAFC',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Custom CSS (glassmorphism details, animations, hover states) -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseDir); ?>/assets/css/style.css">
</head>

<body class="bg-background text-primary min-h-screen font-sans flex flex-col justify-between antialiased">

    <!-- Header navigation -->
    <header
        class="py-4 px-6 border-b border-slate-200/60 bg-white/80 backdrop-blur-md sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="<?php echo htmlspecialchars($baseDir ?: '/'); ?>" class="flex items-center space-x-2">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-tr from-accent to-blue-500 flex items-center justify-center text-white shadow-md shadow-accent/20">
                    <i class="fa-solid fa-house-chimney-window text-lg"></i>
                </div>
                <span
                    class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-primary via-slate-800 to-accent bg-clip-text text-transparent">Home
                    Benefitts</span>
            </a>
            <!-- Right side empty -->
        </div>
    </header>

    <!-- Main page content placeholder -->
    <main class="flex-grow flex items-center justify-center py-10 px-4 md:px-8 w-full max-w-[95%] mx-auto">
        <?php echo $content; ?>
    </main>

    <!-- Footer information -->
    <footer class="py-8 border-t border-slate-200/80 bg-white text-center text-xs text-slate-500 w-full relative z-10">
        <div class="max-w-6xl mx-auto px-6">
            <!-- Trust and SSL lines removed -->
            <p class="border-t border-slate-100 pt-4">&copy; <?php echo date('Y'); ?> Home Benefitts. All rights reserved. <span class="mx-2">•</span> <a href="<?php echo htmlspecialchars($baseDir); ?>/terms" class="hover:text-accent hover:underline">Terms & Conditions</a> <span class="mx-2">•</span> <a href="<?php echo htmlspecialchars($baseDir); ?>/privacy" class="hover:text-accent hover:underline">Privacy Policy</a></p>
            <p class="mt-2 text-slate-400 leading-relaxed text-[10px]">Disclaimer: Home Benefitts is a free service to assist homeowners in connecting with local service providers. All contractors/providers are independent and Home Benefitts does not warrant or guarantee any work performed. It is the responsibility of the homeowner to verify that the hired contractor furnishes the necessary license and insurance required for the work being performed. All persons depicted in a photo or video are actors or models and not contractors listed on Home Benefitts.</p>
        </div>
    </footer>

</body>

</html>