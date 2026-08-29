<?php
/**
 * TEMPORARY debug v3 — bootstraps the REAL app config, then calls the
 * AdEngine API with the app's actual runtime constants and a generous
 * timeout. Upload to project root, open in browser, then DELETE.
 */

require __DIR__ . '/config/config.php';

header('Content-Type: text/plain');

echo "=== 1. Runtime constants (as loaded by config.php) ===\n";
echo "ADS_API_BASE: " . ADS_API_BASE . "\n";
echo "ADS_API_KEY (first 12 chars): " . substr(ADS_API_KEY, 0, 12) . "... (" . strlen(ADS_API_KEY) . " chars total)\n";
echo "ADS_PLACEMENT_HOME_TOP: " . ADS_PLACEMENT_HOME_TOP . "\n";

echo "\n=== 2. Direct request with generous timeout (10s connect / 20s total) ===\n";
$url = rtrim(ADS_API_BASE, '/') . '/ads/serve?placement=' . urlencode(ADS_PLACEMENT_HOME_TOP);
echo "URL: $url\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . ADS_API_KEY]);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$start = microtime(true);
$response = curl_exec($ch);
$elapsed = round(microtime(true) - $start, 2);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$errno = curl_errno($ch);
curl_close($ch);

echo "Took: {$elapsed}s\n";
echo "HTTP status: $status\n";
echo "curl errno: $errno\n";
echo "curl error: " . ($error ?: '(none)') . "\n";

echo "\n=== 3. Raw response body ===\n";
echo $response . "\n";

echo "\n=== 4. Decoded ===\n";
$decoded = json_decode($response, true);
var_dump($decoded);

echo "\n=== 5. What AdEngine::getAd() would return ===\n";
var_dump(AdEngine::getAd(ADS_PLACEMENT_HOME_TOP));

echo "\n=== 6. Verdict ===\n";
if ($errno !== 0) {
    echo "Still fails even with 20s timeout -> confirms a real, non-timeout network problem (not just 'too slow'). Re-check firewall/antivirus.\n";
} elseif ($status >= 200 && $status < 300) {
    if (!empty($decoded['success']) && isset($decoded['data']['ad'])) {
        if ($decoded['data']['ad'] === null) {
            echo "API call succeeded but returned ad: null -> no ACTIVE ad is currently matched to this exact placement code for this app on ads.skoolyst.com. Double-check the placement in Admin -> Connected Apps, and that an ad is created + active + in date range for it.\n";
        } else {
            echo "SUCCESS - real ad data returned. If step 5 above still shows NULL, there's a bug in AdEngine::getAd() itself worth re-checking (e.g. stale cache file) - clear sys_get_temp_dir() adengine_*.json files and retry.\n";
        }
    } else {
        echo "2xx response but missing 'success'/'data.ad' keys - response shape differs from what AdEngine.php expects. Compare step 3's raw body against api-docs.php.\n";
    }
} else {
    echo "Non-2xx status - check raw body in step 3 for the API's error message.\n";
}