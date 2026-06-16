<?php
/**
 * Admin Leads Grid View
 */
$title = "Lead Management | Home Benefitts";
$path = '/admin/leads';
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
            class="px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all bg-accent/10 text-accent shadow-sm shadow-accent/5">
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

    <!-- Header section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-primary">Lead Management</h1>
            <p class="text-xs text-slate-400 mt-1">Review lead logs, qualification states, and outbound API payloads.
            </p>
        </div>
        <button onclick="window.location.reload()"
            class="px-3 py-2 border border-slate-200/80 hover:bg-slate-50 rounded-xl font-semibold text-xs text-slate-600 transition-all flex items-center gap-1.5 hover:shadow-sm active:scale-95">
            <i class="fa-solid fa-arrows-rotate text-[10px] text-slate-400"></i> Refresh Grid
        </button>
    </div>

    <!-- Leads Grid Table -->
    <div class="overflow-x-auto border border-slate-200/60 rounded-2xl bg-white shadow-sm">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200/60 text-slate-500 uppercase tracking-wider font-bold">
                    <th class="p-4">ID</th>
                    <th class="p-4">Service</th>
                    <th class="p-4">ZIP Code</th>
                    <th class="p-4">Customer Name</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Submitted Date</th>
                    <th class="p-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">
                            <i class="fa-solid fa-folder-open text-2xl mb-2 block"></i>
                            No lead captures found. Complete the funnel questionnaire to populate records.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leads as $l): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-bold text-primary">#<?php echo $l['id']; ?></td>
                            <td class="p-4">
                                <span
                                    class="bg-slate-100 text-slate-700 px-2 py-1 rounded-lg text-[10px] font-bold border border-slate-200/50">
                                    <?php echo htmlspecialchars($l['service']); ?>
                                </span>
                            </td>
                            <td class="p-4 font-mono"><?php echo htmlspecialchars($l['zip_code']); ?></td>
                            <td class="p-4">
                                <?php
                                if ($l['first_name'] || $l['last_name']) {
                                    echo htmlspecialchars($l['first_name'] . ' ' . $l['last_name']);
                                } else {
                                    echo '<span class="text-slate-400 italic">No PII details collected</span>';
                                }
                                ?>
                            </td>
                            <td class="p-4">
                                <?php
                                $status = $l['status'];
                                if ($status === 'draft') {
                                    echo '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200/50"><i class="fa-regular fa-file-lines mr-1 text-[9px]"></i> Draft</span>';
                                } elseif ($status === 'ping_success') {
                                    echo '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-accent border border-blue-200/50"><i class="fa-solid fa-satellite-dish mr-1 text-[9px] animate-pulse"></i> Qualified</span>';
                                } elseif ($status === 'ping_failed') {
                                    echo '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/50"><i class="fa-solid fa-triangle-exclamation mr-1 text-[9px]"></i> Rejected</span>';
                                } elseif ($status === 'posted') {
                                    echo '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/50"><i class="fa-solid fa-cloud-arrow-up mr-1 text-[9px]"></i> Syndicated</span>';
                                } elseif ($status === 'post_failed') {
                                    echo '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/50"><i class="fa-solid fa-ban mr-1 text-[9px]"></i> Send Failed</span>';
                                }
                                ?>
                            </td>
                            <td class="p-4 text-slate-400 font-mono">
                                <?php echo date('Y-m-d H:i:s', strtotime($l['created_at'])); ?></td>
                            <td class="p-4 text-right">
                                <button onclick="openLeadModal(<?php echo $l['id']; ?>)"
                                    class="px-3 py-1.5 bg-slate-900 hover:bg-accent text-white rounded-xl transition-all duration-300 font-semibold text-[11px] shadow-sm hover:shadow flex items-center gap-1 ml-auto">
                                    <i class="fa-regular fa-folder-open text-[10px]"></i> View Details
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Lead details modal backdrop overlay -->
    <div id="detailsModal" style="display: none;"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fade-in">

        <!-- Modal Card container -->
        <div
            class="bg-white rounded-3xl w-full max-w-3xl max-h-[85vh] overflow-y-auto shadow-2xl p-6 relative border border-slate-100 animate-slide-in flex flex-col justify-between">

            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5 mb-4">
                <div>
                    <h3 class="text-base font-extrabold text-primary" id="modalLeadTitle">Lead Details #1001</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5" id="modalLeadDate">Submitted on 2026-06-16</p>
                </div>
                <button onclick="closeLeadModal()"
                    class="w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50 hover:text-primary transition-all">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Content tabs and view containers -->
            <div class="flex-grow space-y-4">

                <!-- Tab togglers -->
                <div
                    class="flex p-1 bg-slate-100 rounded-xl text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2 gap-1">
                    <button id="tabBtnDetails"
                        class="flex-1 py-2 rounded-lg text-center transition-all bg-white text-accent shadow-sm"
                        onclick="switchTab('details')">Information</button>
                    <button id="tabBtnLogs" class="flex-1 py-2 rounded-lg text-center transition-all hover:text-primary"
                        onclick="switchTab('logs')">Processing Logs</button>
                </div>

                <!-- TAB VIEW: Details Info -->
                <div id="tabDetails" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Left column: Qualification Data -->
                        <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50 space-y-2.5">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Qualifications
                            </h4>
                            <div class="text-[11px] space-y-2">
                                <div class="flex justify-between"><span class="text-slate-400">Service
                                        Needed:</span><span class="font-bold" id="detService">Roofing</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Timeline:</span><span
                                        class="font-bold" id="detTimeline">1-2 Weeks</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Home
                                        Ownership:</span><span class="font-bold" id="detOwnership">Yes</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Hiring
                                        Status:</span><span class="font-bold" id="detHiring">Ready to hire</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">ZIP Code:</span><span
                                        class="font-bold" id="detZip">90210</span></div>
                                <div class="flex justify-between items-center pt-1 border-t border-slate-200/40 mt-1">
                                    <span class="text-slate-400">Lead Status:</span><span id="detStatus"
                                        class="font-bold"></span></div>
                            </div>
                        </div>

                        <!-- Right column: Customer PII Data -->
                        <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50 space-y-2.5">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Contact Details
                            </h4>
                            <div class="text-[11px] space-y-2">
                                <div class="flex justify-between"><span class="text-slate-400">Name:</span><span
                                        class="font-bold" id="detName">John Doe</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Phone:</span><span
                                        class="font-bold" id="detPhone">(555) 000-0000</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Email:</span><span
                                        class="font-bold text-accent" id="detEmail">john@test.com</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Street
                                        Address:</span><span class="font-bold text-right" id="detStreet">123
                                        Street</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">City/State:</span><span
                                        class="font-bold" id="detCityState">LA, CA</span></div>
                            </div>
                        </div>
                    </div>



                    <!-- Project description -->
                    <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Project Description
                        </h4>
                        <p class="text-[11px] text-slate-600 italic leading-relaxed" id="detDescription">No extra
                            description provided by customer.</p>
                    </div>
                </div>

                <!-- TAB VIEW: Processing Logs -->
                <div id="tabLogs" class="hidden border border-slate-100 rounded-2xl p-4 bg-slate-50/50">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-[11px] border-collapse">
                            <thead>
                                <tr class="text-slate-400 font-bold uppercase border-b border-slate-200/60 pb-1">
                                    <th class="pb-2 pr-4">Timestamp</th>
                                    <th class="pb-2 pr-4">Action</th>
                                    <th class="pb-2">Details</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody" class="divide-y divide-slate-100 text-slate-600 font-medium">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Outbound API Payloads view removed per request -->

            </div>

            <!-- Modal Footer -->
            <div class="border-t border-slate-100 pt-4 mt-6 flex justify-end">
                <button onclick="closeLeadModal()"
                    class="px-5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl font-semibold text-xs text-slate-600 transition-colors">
                    Close Details
                </button>
            </div>

        </div>

    </div>

</div>

<!-- Admin management helper logic -->
<script>
    // Modal states
    let activeTab = 'details';

    function openLeadModal(leadId) {
        const baseDir = '<?php echo htmlspecialchars($baseDir); ?>';

        // Fetch details
        fetch(`${baseDir}/admin/lead-details?id=${leadId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                bindLeadData(data);

                // Show modal
                document.getElementById('detailsModal').style.display = 'flex';
            })
            .catch(err => {
                console.error(err);
                alert("Error retrieving lead data details.");
            });
    }

    function closeLeadModal() {
        document.getElementById('detailsModal').style.display = 'none';
    }

    function switchTab(tabName) {
        activeTab = tabName;

        // Base style for buttons: flex-1 py-2 rounded-lg text-center transition-all hover:text-primary
        const baseClass = 'flex-1 py-2 rounded-lg text-center transition-all hover:text-primary text-slate-500';
        const activeClass = 'flex-1 py-2 rounded-lg text-center transition-all bg-white text-accent shadow-sm';

        document.getElementById('tabBtnDetails').className = baseClass;
        document.getElementById('tabBtnLogs').className = baseClass;

        // Hide tabs
        document.getElementById('tabDetails').classList.add('hidden');
        document.getElementById('tabLogs').classList.add('hidden');

        // Activate selected
        if (tabName === 'details') {
            document.getElementById('tabBtnDetails').className = activeClass;
            document.getElementById('tabDetails').classList.remove('hidden');
        } else if (tabName === 'logs') {
            document.getElementById('tabBtnLogs').className = activeClass;
            document.getElementById('tabLogs').classList.remove('hidden');
        }
    }

    function bindLeadData(data) {
        const lead = data.lead;
        const logs = data.logs;

        // Set title and dates
        document.getElementById('modalLeadTitle').textContent = `Lead Details #${lead.id} [${lead.service}]`;
        document.getElementById('modalLeadDate').textContent = `Generated on ${lead.created_at}`;

        // Fill qualification
        document.getElementById('detService').textContent = lead.service;
        document.getElementById('detTimeline').textContent = lead.timeline;
        document.getElementById('detOwnership').textContent = lead.home_ownership;
        document.getElementById('detHiring').textContent = lead.hiring_status;
        document.getElementById('detZip').textContent = lead.zip_code;

        // Set status badge
        const detStatus = document.getElementById('detStatus');
        let statusBadge = '';
        if (lead.status === 'draft') {
            statusBadge = '<span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-200/50"><i class="fa-regular fa-file-lines mr-1 text-[9px]"></i> Draft</span>';
        } else if (lead.status === 'ping_success') {
            statusBadge = '<span class="bg-blue-50 text-accent px-2.5 py-1 rounded-lg text-[10px] font-bold border border-blue-200/50"><i class="fa-solid fa-satellite-dish mr-1 text-[9px]"></i> Qualified</span>';
        } else if (lead.status === 'ping_failed') {
            statusBadge = '<span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-lg text-[10px] font-bold border border-amber-200/50"><i class="fa-solid fa-triangle-exclamation mr-1 text-[9px]"></i> Rejected</span>';
        } else if (lead.status === 'posted') {
            statusBadge = '<span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg text-[10px] font-bold border border-emerald-200/50"><i class="fa-solid fa-cloud-arrow-up mr-1 text-[9px]"></i> Posted Success</span>';
        } else if (lead.status === 'post_failed') {
            statusBadge = '<span class="bg-rose-50 text-rose-700 px-2.5 py-1 rounded-lg text-[10px] font-bold border border-rose-200/50"><i class="fa-solid fa-ban mr-1 text-[9px]"></i> Post Failed</span>';
        }
        detStatus.innerHTML = statusBadge;

        // Fill contact details
        document.getElementById('detName').textContent = (lead.first_name || lead.last_name)
            ? `${lead.first_name} ${lead.last_name}`
            : 'Not completed';
        document.getElementById('detPhone').textContent = lead.phone || 'N/A';
        document.getElementById('detEmail').textContent = lead.email || 'N/A';
        document.getElementById('detStreet').textContent = lead.street_address || 'N/A';
        document.getElementById('detCityState').textContent = (lead.city || lead.state)
            ? `${lead.city}, ${lead.state}`
            : 'N/A';

        // Describe project
        document.getElementById('detDescription').textContent = lead.description
            ? lead.description
            : 'No description provided by client.';



        // Bind logs
        const logsTableBody = document.getElementById('logsTableBody');
        logsTableBody.innerHTML = '';
        if (logs.length === 0) {
            logsTableBody.innerHTML = '<tr><td colspan="3" class="p-3 text-center text-slate-400">No lifecycle logs recorded.</td></tr>';
        } else {
            logs.forEach(log => {
                let actionBadge = `<span class="bg-slate-100 px-2 py-0.5 rounded-lg text-[9px] font-bold text-slate-600 border border-slate-200/50">${log.action}</span>`;
                if (log.action.includes('success') || log.action === 'posted') {
                    actionBadge = `<span class="bg-emerald-50 px-2 py-0.5 rounded-lg text-[9px] font-bold text-emerald-700 border border-emerald-100">${log.action}</span>`;
                } else if (log.action.includes('fail') || log.action.includes('reject') || log.action.includes('error')) {
                    actionBadge = `<span class="bg-rose-50 px-2 py-0.5 rounded-lg text-[9px] font-bold text-rose-700 border border-rose-100">${log.action}</span>`;
                }
                logsTableBody.innerHTML += `
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 font-mono text-[10px] text-slate-400">${log.created_at}</td>
                    <td class="p-3">${actionBadge}</td>
                    <td class="p-3 text-slate-600 font-semibold text-[11px]">${log.message}</td>
                </tr>
            `;
            });
        }

        // Default to main details tab
        switchTab('details');
    }

    function escapeHtml(text) {
        if (typeof text !== 'string') return text;
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>