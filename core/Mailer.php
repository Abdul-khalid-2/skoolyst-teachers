<?php

/**
 * Mailer
 * Sends email through the centralised Skoolyst Email API
 * (POST {EMAIL_API_BASE}/email/send). That service picks a sender account,
 * delivers over SMTP, and logs the message, so this app holds no SMTP
 * credentials of its own. The API accepts plain-text bodies only.
 */
class Mailer
{
    /**
     * Send a plain-text email. Returns true on success (HTTP 201).
     */
    public static function send(string $to, string $subject, string $body): bool
    {
        if (EMAIL_API_KEY === '') {
            error_log('[Mailer] EMAIL_API_KEY is not set; cannot send to ' . $to);
            return false;
        }

        $ch = curl_init(rtrim(EMAIL_API_BASE, '/') . '/email/send');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . EMAIL_API_KEY,
            ],
            CURLOPT_POSTFIELDS     => json_encode([
                'source_app' => EMAIL_SOURCE_APP,
                'to'         => $to,
                'subject'    => mb_substr($subject, 0, 255),
                'body'       => $body,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 20,
        ]);

        $raw    = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($status === 201) {
            return true;
        }

        if ($raw === false) {
            error_log('[Mailer] Email API request failed for ' . $to . ': ' . $curlError);
            return false;
        }

        // 503 (all_accounts_exhausted / send_failed) is temporary; the rest
        // (401/403/422) mean a config or request problem.
        $response = json_decode((string) $raw, true);
        $code     = $response['error']['code'] ?? 'unknown';
        $message  = $response['error']['message'] ?? (string) $raw;
        error_log("[Mailer] Email API error for {$to} (HTTP {$status}, {$code}): {$message}");
        return false;
    }
}
