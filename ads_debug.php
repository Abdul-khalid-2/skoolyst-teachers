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






# Skoolyst Ad-Engine Integration — Reusable Blueprint Prompt

// Is prompt ko kisi bhi naye Skoolyst-family project (ya kisi bhi PHP app) mein
// `ads.skoolyst.com` ka ad slot lagane ke liye copy-paste kar ke use karo. Sirf
// project-specific naam/paths change karne hain — logic same rehta hai.


// Mujhe is project mein shared Skoolyst ad platform (ads.skoolyst.com) ka
// ek ad slot integrate karna hai — same pattern jo teachers.skoolyst.com aur
// skoolyst-blog-management-system mein already use ho chuka hai. Server-side
// only integration chahiye (API key kabhi browser tak na jaye).

// Requirements:

// 1. config/ads.php — .env se base_url, api_key, cache_ttl, aur
//    placements (friendly slot name => ads.skoolyst.com placement code) load kare.

// 2. AdService.php (app/Services/):
//    - getAd(string $placementCode): ?array
//      - disk par cache kare: sys_get_temp_dir() . '/skoolyst_ad_' . md5($placementCode) . '.json'
//      - cache_ttl ke andar cached value use kare
//      - IMPORTANT: transport-level failure (timeout/DNS/curl error) ko kabhi
//        cache NA kare — sirf successful response (chahe ad null ho ya real ho)
//        cache ho.
//      - curl timeout generous rakhna (CONNECTTIMEOUT ~10s, TIMEOUT ~20s) —
//        ad server kabhi kabhi 1-7+ seconds le leta hai, chota timeout false
//        "no ad" de dega.
//    - placementCode(string $slot): friendly slot name ko config se resolve kare
//    - trackImpression() / trackClick() — apne app ke apne endpoint ke zariye
//      relay karein, browser directly ads.skoolyst.com ko hit na kare
//    - imageUrl(?string $path) — relative image_path ko ad app ke document
//      root se resolve kare (api/vN path strip kar ke)

// 3. resources/views/components/ad-slot.php:
//    - $placement (friendly name) le kar AdService se ad fetch kare
//    - agar $ad null ho to kuch bhi render na kare (silent return)
//    - koi var_dump/debug output PRODUCTION view mein na chhodna

// 4. Debug/diagnostic route (temporary, controller mein):
//    - raw config dump
//    - direct curl call with generous timeout + full raw response + decoded array
//    - CACHE FILE KO CLEAR KAR KE getAd() dobara call kare (taake purana
//      cached null result confuse na kare)
//    - verdict: config missing / curl error / non-2xx / ad:null / success
//    - is route ko permanent nahi rakhna — testing ke baad hata dena

// 5. .env mein add karna:
//    ADS_API_BASE=
//    ADS_API_KEY=
//    ADS_CACHE_TTL=30
//    ADS_PLACEMENT_<SLOT_NAME>=

// Batao kaunse placement(s) chahiye aur kis page(s) par lagane hain, phir
// step-by-step implement karo — pehle config, phir AdService, phir component,
// phir landing page integration, phir debug route se live test.


## Common gotchas (isko dhyan mein rakhna — bar bar yehi masla aata hai)

// 1. **Stale cached `null`** — agar pehle ad inactive/key galat thi, us waqt ka
//    `null` disk par cache ho chuka hoga. `cache_ttl` guzarne tak wahi null
//    milta rahega. Debug route hamesha cache file clear kar ke test kare,
//    warna "debug kaam kar raha hai but live page NULL de raha hai" wala
//    confusion hoga.

// 2. **Timeout mismatch** — `AdService`'s apna internal curl timeout debug
//    route ke manual curl se chota na ho, warna live traffic mein intermittent
//    null aayega jab debug hamesha pass hoga.

// 3. **APP_URL / actual serving path mismatch** — `.env` ka `APP_URL` check
//    karo ke wo wahi URL hai jahan browser mein test kar rahe ho. Alag path
//    test karne se "not working" wala false alarm milta hai.

// 4. **var_dump() production mein reh jana** — debug ke liye dala gaya
//    `var_dump($__ad)` component file mein delete karna mat bhoolna.

// 5. **Malformed `click_url` from API** — kabhi kabhi ad data mein double-URL
//    jaisa `click_url` aa sakta hai (e.g. `https://x.inhttps://y.com`) — is
//    par bharosa mat karo, render se pehle sanity-check zaroor karo.

// 6. **Server-side flakiness** — ads.skoolyst.com kabhi kabhi ek hi placement
//    ke liye back-to-back calls mein alag result de sakta hai (frequency
//    capping / fill rate). Ye is app ka bug nahi — bas note kar lo, panic mat
//    karo.