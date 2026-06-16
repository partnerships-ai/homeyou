<?php
/**
 * Terms and Conditions view page
 */
$title = "Terms & Conditions | Home Benefitts";
$baseDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
if ($baseDir === '/' || $baseDir === '\\') {
    $baseDir = '';
}
ob_start();
?>

<div class="w-full max-w-3xl mx-auto glass-panel rounded-3xl p-6 md:p-10 relative my-6 text-left">

    <h1 class="text-3xl font-extrabold tracking-tight text-primary border-b border-slate-200 pb-4 mb-6">
        Terms & Conditions
    </h1>

    <p class="text-xs text-slate-400 font-semibold mb-6">
        Last Updated: May 2026
    </p>

    <div class="space-y-6 text-sm text-slate-600 leading-relaxed font-medium">
        <p>
            Welcome to Home Benefitts, operated by <strong>Innovative Hand Marketing Management LLC</strong>. By
            using this website, you agree to the following terms and conditions.
        </p>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-primary">1. Information & Services</h3>
            <p>
                We are a marketing and lead generation company that connects consumers with third-party service
                providers. We do not directly provide home improvement or other services.
            </p>
        </section>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-primary">2. User Information</h3>
            <p>
                By submitting your information, you confirm that all details provided are accurate and complete.
            </p>
        </section>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-primary">3. Consent to Contact</h3>
            <p>
                By using this website, you consent to receive calls, text messages, and emails from Innovative Hand
                Marketing Management LLC and its partners regarding products and services. Consent is not required to
                make a purchase.
            </p>
        </section>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-primary">4. Third-Party Services</h3>
            <p>
                We do not guarantee the availability, pricing, or quality of services offered by third-party providers.
            </p>
        </section>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-primary">5. Disclaimer</h3>
            <p>
                All information on this website is provided "as is" without warranties of any kind. We are not liable
                for any damages resulting from the use of this website or services provided by third parties.
            </p>
        </section>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-primary">6. Changes to These Terms</h3>
            <p>
                We may update these Terms and Conditions at any time. Continued use of the website constitutes
                acceptance of any changes.
            </p>
        </section>

        <section class="space-y-2 pt-4 border-t border-slate-100">
            <h3 class="text-base font-bold text-primary">7. Contact Us</h3>
            <p class="text-xs space-y-1">
                <strong>Email:</strong> <a href="mailto:[EMAIL_ADDRESS]"
                    class="text-accent font-bold hover:underline">support@homebenefitts.com</a>
            </p>
        </section>

        <p class="pt-6 text-xs text-slate-400 border-t border-slate-100 italic">
            By using this website, you acknowledge that you have read and agreed to these Terms and Conditions.
        </p>
    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>