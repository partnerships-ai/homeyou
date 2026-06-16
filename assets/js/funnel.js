/**
 * Lead Funnel Wizard Controller (Vanilla JS)
 */

// Wizard Global State
const funnelState = {
    currentStep: 1,
    service: '',
    dynamic_questions: {},
    home_ownership: '',
    timeline: '',
    hiring_status: '',
    zip_code: '',
    description: '',
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    street_address: '',
    city: '',
    state: '',
    lead_id: null,
    lead_token: '',
    providers: []
};

// Total count of wizard steps
const TOTAL_STEPS = 10;

// Dynamic question generator maps based on Selected Service
const dynamicQuestions = {
    'Roofing': [
        {
            id: 'roof_action',
            title: 'What type of roofing service is required?',
            type: 'radio',
            options: ['Replace Roof', 'Repair Roof']
        }
    ],
    'Windows': [
        {
            id: 'window_count',
            title: 'How many windows are involved?',
            type: 'radio',
            options: ['1-2 Windows', '3-5 Windows', '6-10 Windows', '10+ Windows']
        },
        {
            id: 'window_material',
            title: 'Preferred Window Material',
            type: 'radio',
            options: ['Vinyl', 'Wood', 'Aluminum', 'Fiberglass']
        },
        {
            id: 'window_action',
            title: 'Project scope',
            type: 'radio',
            options: ['Install New Windows', 'Repair or Replace Windows']
        }
    ],
    'HVAC': [
        {
            id: 'hvac_action',
            title: 'What HVAC service is needed?',
            type: 'radio',
            options: ['Install Central AC', 'Repair Central AC']
        }
    ],
    'Siding': [
        {
            id: 'siding_action',
            title: 'What is the siding scope?',
            type: 'radio',
            options: ['Install or Replace Siding', 'Repair Siding']
        }
    ],
    'Interior Painting': [
        {
            id: 'interior_paint_scope',
            title: 'Select scope size',
            type: 'radio',
            options: ['Whole House', 'Multiple Rooms', 'Single Room', 'Accent Wall']
        },
        {
            id: 'paint_brand',
            title: 'Paint Quality Preference',
            type: 'radio',
            options: ['Premium (Sherwin-Williams, Benjamin Moore)', 'Standard Quality', 'Budget Friendly']
        }
    ],
    'Exterior Painting': [
        {
            id: 'exterior_paint_scope',
            title: 'Select scope size',
            type: 'radio',
            options: ['Whole House Exterior', 'Trim Only', 'Fence or Deck Only']
        }
    ]
};

// Initialize listeners on page load
document.addEventListener('DOMContentLoaded', () => {
    setupPhoneMask();
    setupZipAutoAdvance();
});

// Setup phone input formatting (e.g. (555) 000-0000)
function setupPhoneMask() {
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', (e) => {
            const cleanValue = e.target.value.replace(/\D/g, '');
            const length = cleanValue.length;

            if (length < 4) {
                e.target.value = cleanValue;
            } else if (length < 7) {
                e.target.value = `(${cleanValue.slice(0, 3)}) ${cleanValue.slice(3)}`;
            } else {
                e.target.value = `(${cleanValue.slice(0, 3)}) ${cleanValue.slice(3, 6)}-${cleanValue.slice(6, 10)}`;
            }
        });
    }
}

// Automatically advance to description step if valid ZIP entered
function setupZipAutoAdvance() {
    const zipInput = document.getElementById('zip_code');
    const zipError = document.getElementById('zipError');
    if (zipInput) {
        zipInput.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            if (val.length === 5) {
                if (/^\d{5}$/.test(val)) {
                    zipError.classList.add('hidden');
                    funnelState.zip_code = val;
                    // Autofill city/state based on mock lookup
                    lookupZip(val);
                    setTimeout(() => navigateStep(1), 300);
                } else {
                    zipError.classList.remove('hidden');
                }
            }
        });
    }
}

// Simple lookup to set city/state values automatically
function lookupZip(zip) {
    // Standard mock cities
    const cityInput = document.getElementById('city');
    const stateInput = document.getElementById('state');
    if (zip.startsWith('9')) {
        cityInput.value = 'Los Angeles';
        stateInput.value = 'CA';
    } else if (zip.startsWith('1')) {
        cityInput.value = 'New York';
        stateInput.value = 'NY';
    } else if (zip.startsWith('6')) {
        cityInput.value = 'Chicago';
        stateInput.value = 'IL';
    } else {
        cityInput.value = 'Austin';
        stateInput.value = 'TX';
    }
}

// Select Service handler (Step 1)
function selectService(service, el) {
    funnelState.service = service;

    // Deactivate active service cards UI
    document.querySelectorAll('#step-1 .select-card').forEach(el => el.classList.remove('active-card'));

    // Set clicked card active
    el.classList.add('active-card');

    // Build dynamic questions for Step 2
    buildDynamicQuestions(service);

    // Auto-advance
    setTimeout(() => navigateStep(1), 300);
}

// Build conditional elements inside step-2 container
function buildDynamicQuestions(service) {
    const container = document.getElementById('dynamicQuestionsContainer');
    container.innerHTML = '';

    const questions = dynamicQuestions[service] || [];

    // Set title based on service
    document.getElementById('step2Title').textContent = `Help us understand your ${service} needs`;

    if (questions.length === 0) {
        // Fallback for services without custom questions (e.g. Electrical)
        container.innerHTML = `
            <div class="p-4 border border-slate-200/80 rounded-2xl bg-white">
                <span class="block text-sm font-semibold text-slate-500 mb-2.5">What is the project scale?</span>
                <div class="space-y-2">
                    <button class="select-card w-full p-3.5 border border-slate-200 rounded-xl bg-white hover:border-accent text-left text-sm font-bold" onclick="selectDynamicOption('scope', 'Residential Installation', 0, this)">
                        Residential Installation
                    </button>
                    <button class="select-card w-full p-3.5 border border-slate-200 rounded-xl bg-white hover:border-accent text-left text-sm font-bold" onclick="selectDynamicOption('scope', 'Residential Repair', 0, this)">
                        Residential Repair
                    </button>
                    <button class="select-card w-full p-3.5 border border-slate-200 rounded-xl bg-white hover:border-accent text-left text-sm font-bold" onclick="selectDynamicOption('scope', 'Commercial Service', 0, this)">
                        Commercial Service
                    </button>
                </div>
            </div>
        `;
        return;
    }

    questions.forEach((q, qIndex) => {
        let questionHtml = `
            <div class="p-4 border border-slate-200/80 rounded-2xl bg-white" data-question-id="${q.id}">
                <span class="block text-sm font-semibold text-slate-500 mb-2.5">${q.title}</span>
                <div class="space-y-2">
        `;

        q.options.forEach(opt => {
            questionHtml += `
                <button class="select-card w-full p-3.5 border border-slate-200 rounded-xl bg-white hover:border-accent text-left text-sm font-bold" onclick="selectDynamicOption('${q.id}', '${opt}', ${qIndex}, this)">
                    ${opt}
                </button>
            `;
        });

        questionHtml += `
                </div>
            </div>
        `;
        container.innerHTML += questionHtml;
    });
}

// Select dynamic answer event handler
function selectDynamicOption(questionId, value, index, el) {
    funnelState.dynamic_questions[questionId] = value;

    // Toggle active card styles inside this specific question block
    const parentContainer = el.parentElement;
    parentContainer.querySelectorAll('.select-card').forEach(item => item.classList.remove('active-card'));
    el.classList.add('active-card');

    // If it's the last dynamic question block, auto-advance to step 3
    const totalQuestions = dynamicQuestions[funnelState.service] ? dynamicQuestions[funnelState.service].length : 1;
    if (Object.keys(funnelState.dynamic_questions).length >= totalQuestions) {
        setTimeout(() => navigateStep(1), 350);
    }
}

// Select Ownership handler (Step 3)
function selectOwnership(ownership, el) {
    funnelState.home_ownership = ownership;
    document.querySelectorAll('#step-3 .select-card').forEach(item => item.classList.remove('active-card'));
    el.classList.add('active-card');
    setTimeout(() => navigateStep(1), 300);
}

// Select Timeline handler (Step 4)
function selectTimeline(timeline, el) {
    funnelState.timeline = timeline;
    document.querySelectorAll('#step-4 .select-card').forEach(item => item.classList.remove('active-card'));
    el.classList.add('active-card');
    setTimeout(() => navigateStep(1), 300);
}

// Select Hiring Status handler (Step 5)
function selectHiringStatus(status, el) {
    funnelState.hiring_status = status;
    document.querySelectorAll('#step-5 .select-card').forEach(item => item.classList.remove('active-card'));
    el.classList.add('active-card');
    setTimeout(() => navigateStep(1), 300);
}

// Execute the Ping API call (qualify details before gathering contact information)
async function runPing() {
    toggleLoading(true, "Matching local service providers...", "Finding active contractors in your area.");

    const csrf = document.getElementById('csrfToken').value;

    try {
        const payload = {
            csrf_token: csrf,
            service: funnelState.service,
            dynamic_questions: JSON.stringify(funnelState.dynamic_questions),
            home_ownership: funnelState.home_ownership,
            timeline: funnelState.timeline,
            hiring_status: funnelState.hiring_status,
            zip_code: funnelState.zip_code,
            description: funnelState.description
        };

        const baseDir = getBaseDir();
        const response = await fetch(`${baseDir}/api/ping`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        toggleLoading(false);

        if (response.ok && data.status === 'success') {
            funnelState.lead_id = data.lead_id;
            funnelState.lead_token = data.lead_token;
            funnelState.providers = data.providers || [];

            // Build consent checkboxes list for Step 10
            buildProvidersCheckboxes(funnelState.providers);

            // Advance to Contact details step (Step 8)
            setStep(8);
        } else {
            // Rejection or failure state
            const reason = data.reason || (data.errors ? data.errors.join(', ') : 'No matched partners.');
            document.getElementById('pingRejectReason').textContent = reason;
            setStep('ping-failed');
        }
    } catch (err) {
        toggleLoading(false);
        alert("An error occurred connecting to matching services. Please try again.");
    }
}

// Build provider checkboxes inside Step 10 list
function buildProvidersCheckboxes(providers) {
    const container = document.getElementById('dynamicProvidersCheckboxes');
    container.innerHTML = '';

    if (providers.length === 0) {
        container.innerHTML = '<span class="text-xs text-slate-400">Home Benefitts Partner Networks</span>';
        return;
    }

    providers.forEach(p => {
        container.innerHTML += `
            <label class="flex items-center space-x-2.5 cursor-pointer p-1.5 hover:bg-slate-50 rounded-lg transition-colors">
                <input type="checkbox" checked value="${p.id}" class="w-4 h-4 text-accent border-slate-300 rounded focus:ring-accent provider-checkbox">
                <span class="text-xs font-semibold text-slate-700 select-none">${p.name}</span>
            </label>
        `;
    });
}

// Execute Final POST lead details submission
async function runPost() {
    const submitError = document.getElementById('submitError');
    submitError.classList.add('hidden');

    const tcpaChecked = document.getElementById('tcpa_consent').checked;
    if (!tcpaChecked) {
        submitError.textContent = "Please agree to the TCPA communications terms.";
        submitError.classList.remove('hidden');
        return;
    }

    toggleLoading(true, "Finalizing your quotes...", "Transmitting lead details securely to contractors.");

    const csrf = document.getElementById('csrfToken').value;

    try {
        const payload = {
            csrf_token: csrf,
            lead_id: funnelState.lead_id,
            lead_token: funnelState.lead_token,
            first_name: funnelState.first_name,
            last_name: funnelState.last_name,
            phone: funnelState.phone,
            email: funnelState.email,
            street_address: funnelState.street_address,
            city: funnelState.city,
            state: funnelState.state,
            zip_code: funnelState.zip_code,
            tcpa_consent: tcpaChecked
        };

        const baseDir = getBaseDir();
        const response = await fetch(`${baseDir}/api/post`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        toggleLoading(false);

        if (response.ok && data.status === 'success') {
            const redirect = data.redirect_url || `${baseDir}/success`;
            window.location.href = redirect;
        } else {
            // Handle post errors
            const errs = data.errors || ['Failed to post details.'];
            submitError.innerHTML = errs.join('<br>');
            submitError.classList.remove('hidden');
        }
    } catch (err) {
        toggleLoading(false);
        submitError.textContent = "A network error occurred. Please try again.";
        submitError.classList.remove('hidden');
    }
}

// Step Navigation Handler
function navigateStep(direction) {
    const current = funnelState.currentStep;

    // Client-side step validation checks before going forward
    if (direction === 1) {
        if (current === 1 && !funnelState.service) return;

        if (current === 2) {
            const totalQuestions = dynamicQuestions[funnelState.service] ? dynamicQuestions[funnelState.service].length : 1;
            if (Object.keys(funnelState.dynamic_questions).length < totalQuestions) return;
        }

        if (current === 3 && !funnelState.home_ownership) return;
        if (current === 4 && !funnelState.timeline) return;
        if (current === 5 && !funnelState.hiring_status) return;

        if (current === 6) {
            const zip = document.getElementById('zip_code').value.trim();
            if (!/^\d{5}$/.test(zip)) {
                document.getElementById('zipError').classList.remove('hidden');
                return;
            }
            funnelState.zip_code = zip;
        }

        if (current === 7) {
            funnelState.description = document.getElementById('description').value.trim();
            // End of qualification non-PII details. Trigger PING before showing step 8.
            runPing();
            return;
        }

        if (current === 8) {
            const fn = document.getElementById('first_name').value.trim();
            const ln = document.getElementById('last_name').value.trim();
            const ph = document.getElementById('phone').value.trim();
            const em = document.getElementById('email').value.trim();
            const errEl = document.getElementById('contactError');
            errEl.classList.add('hidden');

            if (!fn || !ln || ph.replace(/\D/g, '').length < 10 || !validateEmail(em)) {
                errEl.classList.remove('hidden');
                return;
            }

            funnelState.first_name = fn;
            funnelState.last_name = ln;
            funnelState.phone = ph;
            funnelState.email = em;
        }

        if (current === 9) {
            const street = document.getElementById('street_address').value.trim();
            const city = document.getElementById('city').value.trim();
            const state = document.getElementById('state').value.trim();
            const errEl = document.getElementById('addressError');
            errEl.classList.add('hidden');

            if (!street || !city || !state) {
                errEl.classList.remove('hidden');
                return;
            }

            funnelState.street_address = street;
            funnelState.city = city;
            funnelState.state = state;
        }

        if (current === 10) {
            // Final submission step
            runPost();
            return;
        }
    }

    setStep(current + direction);
}

// Reset entire funnel UI to start over (on rejection/back tracking)
function resetFunnel() {
    funnelState.currentStep = 1;
    funnelState.service = '';
    funnelState.dynamic_questions = {};
    funnelState.home_ownership = '';
    funnelState.timeline = '';
    funnelState.hiring_status = '';
    funnelState.zip_code = '';
    funnelState.description = '';
    funnelState.lead_id = null;
    funnelState.lead_token = '';

    document.querySelectorAll('.select-card').forEach(el => el.classList.remove('active-card'));
    document.getElementById('zip_code').value = '';
    document.getElementById('description').value = '';
    document.getElementById('first_name').value = '';
    document.getElementById('last_name').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('email').value = '';
    document.getElementById('street_address').value = '';

    setStep(1);
}

// Switch UI step display
function setStep(stepNum) {
    // Hide all steps
    document.querySelectorAll('.funnel-step').forEach(el => {
        el.classList.add('hidden-step');
        el.classList.remove('active-step');
    });

    const isNumericStep = typeof stepNum === 'number';

    if (isNumericStep) {
        funnelState.currentStep = stepNum;

        // Show current step panel
        const targetStep = document.getElementById(`step-${stepNum}`);
        if (targetStep) {
            targetStep.classList.remove('hidden-step');
            targetStep.classList.add('active-step');
        }

        // Update progress indicators
        updateProgress();

        // Manage button visibility
        const backBtn = document.getElementById('btnBack');
        const nextBtn = document.getElementById('btnNext');
        const wizardHeader = document.getElementById('wizardHeader');
        const navigationBar = document.getElementById('navigationBar');

        wizardHeader.classList.remove('hidden');
        navigationBar.classList.remove('hidden');

        // Back button is invisible on Step 1
        if (stepNum === 1) {
            backBtn.classList.add('invisible');
        } else {
            backBtn.classList.remove('invisible');
        }

        // Modify Next button text/styles on final submission step
        if (stepNum === 10) {
            nextBtn.innerHTML = '<span>Get My Quotes</span> <i class="fa-solid fa-circle-check"></i>';
            nextBtn.classList.remove('bg-accent');
            nextBtn.classList.add('bg-success', 'hover:bg-green-600');
        } else {
            nextBtn.innerHTML = '<span>Continue</span> <i class="fa-solid fa-arrow-right text-xs"></i>';
            nextBtn.classList.remove('bg-success', 'hover:bg-green-600');
            nextBtn.classList.add('bg-accent', 'hover:bg-blue-700');
        }
    } else {
        // Shown for fallback failure steps (e.g. step-ping-failed)
        const targetStep = document.getElementById(stepNum);
        if (targetStep) {
            targetStep.classList.remove('hidden-step');
            targetStep.classList.add('active-step');
        }

        // Hide normal navigation and headers on failure screen
        document.getElementById('wizardHeader').classList.add('hidden');
        document.getElementById('navigationBar').classList.add('hidden');
    }
}

// Update Top Progress bar percentage details
function updateProgress() {
    const pct = Math.round((funnelState.currentStep / TOTAL_STEPS) * 100);
    document.getElementById('progressBar').style.width = `${pct}%`;
    document.getElementById('stepPercentText').textContent = `${pct}% Complete`;

    // Human-readable titles for steps
    const stepTitles = [
        "", // index 0 unused
        "Select Service",
        "Dynamic Questions",
        "Home Ownership",
        "Project Timeline",
        "Hiring Status",
        "ZIP Code",
        "Project Description",
        "Contact Information",
        "Project Address",
        "Consent & Confirm"
    ];
    document.getElementById('stepTitleText').textContent = `Step ${funnelState.currentStep} of ${TOTAL_STEPS}: ${stepTitles[funnelState.currentStep]}`;
}

// Show/Hide Loading Screen
function toggleLoading(show, title = '', subtitle = '') {
    const el = document.getElementById('funnelLoading');
    if (show) {
        document.getElementById('loadingTitle').textContent = title;
        document.getElementById('loadingSubtitle').textContent = subtitle;
        el.style.display = 'flex';
    } else {
        el.style.display = 'none';
    }
}

// Helper function to validate email
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Get project base folder path dynamically to prefix endpoints
function getBaseDir() {
    let path = window.location.pathname;
    path = path.replace(/\/index\.php$/, '');
    
    const knownRoutes = ['/api/ping', '/api/post', '/success', '/terms', '/privacy'];
    for (let route of knownRoutes) {
        if (path.endsWith(route)) {
            return path.substring(0, path.length - route.length);
        }
    }
    
    if (path.endsWith('/')) {
        path = path.substring(0, path.length - 1);
    }
    
    return path;
}
