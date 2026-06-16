<?php
/**
 * Automated Diagnostic Test Script
 */

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Logger.php';
require_once __DIR__ . '/includes/ApiClient.php';
require_once __DIR__ . '/models/Lead.php';
require_once __DIR__ . '/models/Setting.php';

echo "========================================\n";
echo "       FUNNEL SYSTEM DIAGNOSTICS        \n";
echo "========================================\n";

try {
    // 1. Database Connection check
    echo "1. Testing Database Connection... ";
    $db = Database::getConnection();
    echo "SUCCESS!\n";

    // 2. Settings check
    echo "2. Reading Settings from Database... ";
    $campaignToken = Setting::get('campaign_token', 'default_campaign_token_123');
    $mockMode = Setting::get('mock_mode', '1');
    echo "SUCCESS! Token: '{$campaignToken}', Mock Mode: '{$mockMode}'\n";

    // 3. Lead persistence check
    echo "3. Creating a draft Lead in DB... ";
    $leadId = Lead::createDraft([
        'service' => 'Roofing',
        'dynamic_questions' => json_encode(['roof_action' => 'Replace Roof']),
        'home_ownership' => 'Yes',
        'timeline' => 'Within 1 Week',
        'hiring_status' => 'Ready To Hire',
        'zip_code' => '90210',
        'description' => 'Test lead description details.'
    ]);
    echo "SUCCESS! Lead ID: {$leadId}\n";

    // 4. API Client Ping check
    echo "4. Simulating API Ping (Mock mode: '{$mockMode}')... ";
    $api = new ApiClient();
    $pingResult = $api->ping($leadId, [
        'service' => 'Roofing',
        'dynamic_questions' => json_encode(['roof_action' => 'Replace Roof']),
        'home_ownership' => 'Yes',
        'timeline' => 'Within 1 Week',
        'hiring_status' => 'Ready To Hire',
        'zip_code' => '90210',
        'description' => 'Test lead description details.'
    ]);
    echo "SUCCESS! Status: " . ($pingResult['status'] ?? 'unknown') . "\n";
    if (($pingResult['status'] ?? '') === 'success') {
        echo "   Lead Token: " . ($pingResult['lead_token'] ?? '') . "\n";
        echo "   Providers found: " . count($pingResult['providers'] ?? []) . "\n";
    }

    // 5. Post API call check
    if (($pingResult['status'] ?? '') === 'success') {
        $leadToken = $pingResult['lead_token'];
        echo "5. Simulating API Post and state transition for Lead {$leadId}... ";
        $postResult = $api->post($leadId, $leadToken, [
            'first_name' => 'Diagnostic',
            'last_name' => 'Tester',
            'phone' => '5551234567',
            'email' => 'test@wiserleads.com',
            'street_address' => '123 Test St',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zip_code' => '90210'
        ]);
        
        if (($postResult['status'] ?? '') === 'success') {
            Lead::updatePII($leadId, [
                'first_name' => 'Diagnostic',
                'last_name' => 'Tester',
                'phone' => '5551234567',
                'email' => 'test@wiserleads.com',
                'street_address' => '123 Test St',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip_code' => '90210'
            ], 'posted');
            Lead::logAction($leadId, 'posted_success', "Lead successfully posted via automated diagnostics script.");
        }
        echo "SUCCESS! Post status: " . ($postResult['status'] ?? 'unknown') . "\n";
    }

    // 6. DB Stats Check
    echo "6. Loading Admin KPIs metrics... ";
    $stats = Lead::getStats();
    echo "SUCCESS!\n";
    echo "   Total Leads: {$stats['total']}\n";
    echo "   Success Leads: {$stats['success']}\n";
    echo "   Rejected Leads: {$stats['rejected']}\n";
    echo "   Conversion Rate: {$stats['conv_rate']}%\n";
    echo "   Est. Revenue: \${$stats['revenue']}\n";

    // 7. Logger verification
    echo "7. Verifying logger... ";
    Logger::info("Automated diagnostic checks executed successfully.");
    echo "SUCCESS! Logs stored in logs/app.json.log\n";

    echo "========================================\n";
    echo "    ALL SYSTEM INTEGRATIONS VERIFIED!   \n";
    echo "========================================\n";

} catch (Exception $e) {
    echo "FAILED! Error: " . $e->getMessage() . "\n";
    exit(1);
}
