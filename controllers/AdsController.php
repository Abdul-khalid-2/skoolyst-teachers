<?php

/**
 * Thin proxy between the browser and AdEngine, so the ADS_API_KEY never
 * has to be exposed in frontend JS. Called via navigator.sendBeacon()
 * from the ad-slot tracking script.
 */
class AdsController extends Controller
{
    public function impression(): void
    {
        $adId = (int) $this->input('ad_id', 0);
        if ($adId > 0) {
            AdEngine::trackImpression($adId);
        }
        $this->json(['success' => true]);
    }

    public function click(): void
    {
        $adId = (int) $this->input('ad_id', 0);
        if ($adId > 0) {
            AdEngine::trackClick($adId);
        }
        $this->json(['success' => true]);
    }
}
