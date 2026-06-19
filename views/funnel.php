<?php
/**
 * Wizard Funnel View
 */
$title = "Get Free Home Improvement Quotes | Home Benefitts";
$baseDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
if ($baseDir === '/' || $baseDir === '\\') {
    $baseDir = '';
}
ob_start();
?>

<!-- Fullscreen Background Image -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
    <img src="<?php echo htmlspecialchars($baseDir); ?>/assets/images/home_background.png" alt="" class="w-full h-full object-cover filter blur-[2px] brightness-[0.9]">
    <div class="absolute inset-0 bg-slate-900/10"></div>
</div>

<div class="w-full max-w-2xl mx-auto relative z-10 py-4">

    <!-- Header info above centered card -->
    <div class="text-center mb-8">
        <span class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-accent bg-blue-50/90 backdrop-blur-sm px-3 py-1 rounded-full mb-3 shadow-sm border border-blue-100/50">
            <i class="fa-solid fa-bolt mr-1"></i> Quick 2-Min Match
        </span>
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight">
            Compare Quotes from Certified Local Contractors
        </h1>
    </div>

    <!-- Wizard Funnel Form Card -->
    <div class="w-full glass-panel rounded-3xl p-6 md:p-8 flex flex-col justify-between relative overflow-hidden min-h-[480px]">

        <!-- Wizard Top header and progress bar -->
        <div id="wizardHeader" class="mb-6">
            <div class="flex justify-between items-center text-xs text-slate-500 font-semibold mb-2.5">
                <span id="stepTitleText">Step 1 of 10: Select Service</span>
                <span id="stepPercentText">10% Complete</span>
            </div>
            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                <div id="progressBar"
                    class="h-full bg-gradient-to-r from-accent to-blue-500 w-[10%] transition-all duration-500 ease-out rounded-full">
                </div>
            </div>
        </div>

        <!-- Hidden input for CSRF validation -->
        <input type="hidden" id="csrfToken" value="<?php echo htmlspecialchars($csrfToken); ?>">

        <!-- Loading overlay -->
        <div id="funnelLoading" style="display: none;"
            class="absolute inset-0 bg-white/95 backdrop-blur-sm z-40 flex flex-col items-center justify-center p-8 text-center animate-fade-in">
            <div class="loading-spinner mb-4"></div>
            <h3 id="loadingTitle" class="text-lg font-bold text-primary">Matching local service providers...</h3>
            <p id="loadingSubtitle" class="text-sm text-slate-500 mt-2 max-w-sm">Checking credentials and matching
                availability in your ZIP code. Please wait.</p>
        </div>

        <!-- Steps container -->
        <div class="flex-grow flex flex-col justify-center">

            <!-- STEP 1: Select Service -->
            <div id="step-1" class="funnel-step active-step">
                <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary mb-4 text-center md:text-left">
                    What service do you need?
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mt-2 max-h-[340px] overflow-y-auto pr-1">

                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectService('Roofing', this)">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-accent mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-house-chimney text-lg"></i>
                        </div>
                        <div>
                            <div class="font-bold text-sm text-primary">Roofing</div>
                            <div class="text-xs text-slate-400 mt-0.5">Replacement or repairs</div>
                        </div>
                    </button>

                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectService('HVAC', this)">
                        <div
                            class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-wind text-lg"></i>
                        </div>
                        <div>
                            <div class="font-bold text-sm text-primary">HVAC / Heating & AC</div>
                            <div class="text-xs text-slate-400 mt-0.5">Installations or maintenance</div>
                        </div>
                    </button>

                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectService('Windows', this)">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-table-cells text-lg"></i>
                        </div>
                        <div>
                            <div class="font-bold text-sm text-primary">Windows</div>
                            <div class="text-xs text-slate-400 mt-0.5">Installation or restoration</div>
                        </div>
                    </button>

                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectService('Electrical', this)">
                        <div
                            class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600 mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-bolt text-lg"></i>
                        </div>
                        <div>
                            <div class="font-bold text-sm text-primary">Electrical</div>
                            <div class="text-xs text-slate-400 mt-0.5">Panels, wiring, outlets</div>
                        </div>
                    </button>

                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectService('Siding', this)">
                        <div
                            class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-border-all text-lg"></i>
                        </div>
                        <div>
                            <div class="font-bold text-sm text-primary">Siding</div>
                            <div class="text-xs text-slate-400 mt-0.5">Vinyl, wood, fiber cement</div>
                        </div>
                    </button>

                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectService('Interior Painting', this)">
                        <div
                            class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-paint-roller text-lg"></i>
                        </div>
                        <div>
                            <div class="font-bold text-sm text-primary">Interior Painting</div>
                            <div class="text-xs text-slate-400 mt-0.5">Single rooms, ceilings, full trim</div>
                        </div>
                    </button>

                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectService('Exterior Painting', this)">
                        <div
                            class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-brush text-lg"></i>
                        </div>
                        <div>
                            <div class="font-bold text-sm text-primary">Exterior Painting</div>
                            <div class="text-xs text-slate-400 mt-0.5">Walls, trim, fences, decks</div>
                        </div>
                    </button>

                </div>
            </div>

            <!-- STEP 2: Dynamic Service Questions -->
            <div id="step-2" class="funnel-step hidden-step">
                <h2 id="step2Title" class="text-xl md:text-2xl font-bold tracking-tight text-primary mb-4">
                    Tell us more about your project
                </h2>

                <div id="dynamicQuestionsContainer" class="space-y-4 py-2">
                    <!-- Loaded dynamically via JavaScript -->
                </div>
            </div>

            <!-- STEP 3: Home Ownership -->
            <div id="step-3" class="funnel-step hidden-step">
                <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary mb-4 text-center md:text-left">
                    Do you own the home?
                </h2>
                <div class="grid grid-cols-1 gap-3.5 mt-2">
                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectOwnership('Yes', this)">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-accent mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <span class="font-bold text-sm text-primary">Yes, I am the owner</span>
                    </button>
                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectOwnership('Authorized', this)">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-accent mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <span class="font-bold text-sm text-primary">I am authorized to make decisions</span>
                    </button>
                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectOwnership('No', this)">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-accent mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                        <span class="font-bold text-sm text-primary">No, I rent / lease</span>
                    </button>
                </div>
            </div>

            <!-- STEP 4: Project Timeline -->
            <div id="step-4" class="funnel-step hidden-step">
                <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary mb-4 text-center md:text-left">
                    When would you like to start?
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mt-2">
                    <button
                        class="select-card p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-center flex flex-col items-center"
                        onclick="selectTimeline('Within 1 Week', this)">
                        <i class="fa-solid fa-bolt text-xl text-accent mb-2"></i>
                        <span class="font-bold text-sm text-primary">Within 1 Week</span>
                    </button>
                    <button
                        class="select-card p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-center flex flex-col items-center"
                        onclick="selectTimeline('1-2 Weeks', this)">
                        <i class="fa-solid fa-calendar-week text-xl text-accent mb-2"></i>
                        <span class="font-bold text-sm text-primary">1-2 Weeks</span>
                    </button>
                    <button
                        class="select-card p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-center flex flex-col items-center"
                        onclick="selectTimeline('More than 2 Weeks', this)">
                        <i class="fa-solid fa-calendar-days text-xl text-accent mb-2"></i>
                        <span class="font-bold text-sm text-primary">More than 2 Weeks</span>
                    </button>
                    <button
                        class="select-card p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-center flex flex-col items-center"
                        onclick="selectTimeline('Flexible', this)">
                        <i class="fa-solid fa-clock-rotate-left text-xl text-accent mb-2"></i>
                        <span class="font-bold text-sm text-primary">Flexible</span>
                    </button>
                </div>
            </div>

            <!-- STEP 5: Hiring Status -->
            <div id="step-5" class="funnel-step hidden-step">
                <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary mb-4 text-center md:text-left">
                    What is your hiring status?
                </h2>
                <div class="grid grid-cols-1 gap-3.5 mt-2">
                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectHiringStatus('Ready To Hire', this)">
                        <div
                            class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center text-success mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-primary">Ready to hire</span>
                            <p class="text-xs text-slate-400 mt-0.5">Looking to book a contractor immediately</p>
                        </div>
                    </button>
                    <button
                        class="select-card flex items-center p-4 border border-slate-200/80 rounded-2xl bg-white hover:border-accent text-left"
                        onclick="selectHiringStatus('Planning & Budgeting', this)">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-accent mr-3.5 flex-shrink-0">
                            <i class="fa-solid fa-magnifying-glass-chart"></i>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-primary">Planning & budgeting</span>
                            <p class="text-xs text-slate-400 mt-0.5">Gathering estimates and evaluating options</p>
                        </div>
                    </button>
                </div>
            </div>

            <!-- STEP 6: ZIP Code -->
            <div id="step-6" class="funnel-step hidden-step">
                <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary mb-4">
                    Enter your ZIP code
                </h2>
                <p class="text-xs text-slate-400 mb-4">We'll find certified contractors matching availability in your
                    direct neighborhood.</p>
                <div class="mt-2 relative">
                    <input type="text" id="zip_code" maxlength="5" pattern="[0-9]*" inputmode="numeric"
                        placeholder="e.g. 90210"
                        class="w-full text-center text-3xl font-extrabold py-3 border-2 border-slate-200 rounded-2xl focus:border-accent focus:ring-1 focus:ring-accent tracking-widest uppercase">
                    <div id="zipError" class="text-red-500 text-xs mt-2 hidden text-center">Please enter a valid 5-digit
                        US ZIP Code.</div>
                </div>
            </div>

            <!-- STEP 7: Project Description -->
            <div id="step-7" class="funnel-step hidden-step">
                <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary mb-4">
                    Tell us about your project (optional)
                </h2>
                <p class="text-xs text-slate-400 mb-4">Adding details like sizing or repair specifics helps matches send
                    more accurate estimates.</p>
                <div class="mt-2">
                    <textarea id="description" rows="4" placeholder="Describe what you want done..."
                        class="w-full p-4 border border-slate-200 rounded-2xl focus:border-accent focus:ring-1 focus:ring-accent text-sm"></textarea>
                </div>
            </div>

            <!-- Fallback Rejection Screen (shown if Ping fails) -->
            <div id="step-ping-failed" class="funnel-step hidden-step">
                <div class="text-center py-6">
                    <div
                        class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center text-red-500 mx-auto mb-4">
                        <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                    </div>
                    <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary">No Matching Contractors</h2>
                    <p id="pingRejectReason" class="text-sm text-slate-500 mt-2 max-w-sm mx-auto">
                        Unfortunately, we don't have matching providers available in your area right now.
                    </p>
                    <button
                        class="mt-6 px-6 py-3 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 transition-colors text-sm shadow-md"
                        onclick="resetFunnel()">
                        Start Over
                    </button>
                </div>
            </div>

            <!-- STEP 8: Contact Information -->
            <div id="step-8" class="funnel-step hidden-step">
                <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary mb-4">
                    Who should quotes be sent to?
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">First Name</label>
                        <input type="text" id="first_name" placeholder="John"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Last Name</label>
                        <input type="text" id="last_name" placeholder="Doe"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Phone Number</label>
                        <input type="tel" id="phone" placeholder="(555) 000-0000"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Email Address</label>
                        <input type="email" id="email" placeholder="john.doe@example.com"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm">
                    </div>
                </div>
                <div id="contactError" class="text-red-500 text-xs mt-3.5 hidden">Please check that all fields are
                    correct.</div>
            </div>

            <!-- STEP 9: Address -->
            <div id="step-9" class="funnel-step hidden-step">
                <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary mb-4">
                    What is the project address?
                </h2>
                <div class="space-y-3.5 mt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Street Address</label>
                        <input type="text" id="street_address" placeholder="123 Main St"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">City</label>
                            <input type="text" id="city" placeholder="Los Angeles"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">State</label>
                            <input type="text" id="state" placeholder="CA"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-accent focus:ring-1 focus:ring-accent text-sm">
                        </div>
                    </div>
                </div>
                <div id="addressError" class="text-red-500 text-xs mt-3.5 hidden">Please verify the address is filled
                    correctly.</div>
            </div>

            <!-- STEP 10: TCPA Consent & Final Submission -->
            <div id="step-10" class="funnel-step hidden-step">
                <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary mb-4">
                    Review and Confirm
                </h2>
                <p class="text-xs text-slate-400 mb-4">You have successfully qualified. Select partner communication
                    consents below to finish.</p>

                <div class="space-y-4 mt-2">

                    <!-- Dynamic Provider Checkboxes container -->
                    <div class="border border-slate-200/80 rounded-2xl bg-white p-4">
                        <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Dynamic
                            Matching Providers</label>
                        <div id="dynamicProvidersCheckboxes" class="space-y-2 max-h-[120px] overflow-y-auto pr-1">
                            <!-- Populate dynamically via JS -->
                        </div>
                    </div>

                    <!-- TCPA Language Panel -->
                    <div
                        class="text-[10px] text-slate-500 leading-relaxed max-h-[110px] overflow-y-auto border border-slate-100 bg-slate-50 rounded-xl p-3">
                        <p class="mb-1">
                            By checking the box below and clicking "Get My Quotes", I authorize Home Benefitts, the
                            matching network providers selected above, and their partners to contact me about home
                            services at the telephone number (including SMS/MMS) and email address provided.
                        </p>
                        <p>
                            I consent to receive auto-dialed calls, pre-recorded messages, and texts. I understand that
                            consent is not a condition of purchase and message/data rates may apply.
                        </p>
                    </div>

                    <!-- Consent Checkbox -->
                    <label class="flex items-start space-x-2.5 cursor-pointer">
                        <input type="checkbox" id="tcpa_consent"
                            class="mt-0.5 w-4.5 h-4.5 border-slate-300 rounded text-accent focus:ring-accent">
                        <span class="text-[11px] font-semibold text-slate-600 select-none leading-normal">
                            I agree to the communications terms and consent to be contacted by selected providers.
                        </span>
                    </label>

                </div>

                <div id="submitError" class="text-red-500 text-xs mt-3.5 hidden"></div>
            </div>

        </div>

        <!-- Navigation bar at bottom -->
        <div id="navigationBar" class="flex justify-between items-center border-t border-slate-200/60 pt-5 mt-6">
            <button id="btnBack"
                class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 hover:text-primary transition-all invisible"
                onclick="navigateStep(-1)">
                <i class="fa-solid fa-arrow-left mr-1.5 text-xs"></i> Back
            </button>

            <button id="btnNext"
                class="px-6 py-2.5 rounded-xl bg-accent text-white hover:bg-blue-700 transition-all font-semibold text-sm shadow-md shadow-accent/15 flex items-center gap-1.5"
                onclick="navigateStep(1)">
                <span>Continue</span> <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>

    </div>

</div>

<!-- Funnel script inclusion -->
<script src="<?php echo htmlspecialchars($baseDir); ?>/assets/js/funnel.js?v=<?php echo filemtime(dirname(__DIR__) . '/assets/js/funnel.js'); ?>"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>