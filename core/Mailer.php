<?php

/**
 * Mailer
 * Minimal SMTP client (no Composer/PHPMailer dependency) so the project
 * stays dependency-free, matching the rest of this framework.
 * Supports STARTTLS + AUTH LOGIN, which covers Gmail, Mailgun SMTP,
 * SendGrid SMTP, Postmark SMTP, SES SMTP, etc.
 */
class Mailer
{
    /**
     * Send an HTML email (with plain-text fallback).
     *
     * @param string $to
     * @param string $subject
     * @param string $htmlBody
     * @param string|null $textBody
     * @return bool
     */
    public static function send(string $to, string $subject, string $htmlBody, ?string $textBody = null): bool
    {
        try {
            self::deliver($to, $subject, $htmlBody, $textBody);
            return true;
        } catch (\Throwable $e) {
            error_log('[Mailer] Failed to send to ' . $to . ': ' . $e->getMessage());
            return false;
        }
    }

    private static function deliver(string $to, string $subject, string $htmlBody, ?string $textBody): void
    {
        $host       = MAIL_HOST;
        $port       = MAIL_PORT;
        $username   = MAIL_USERNAME;
        $password   = MAIL_PASSWORD;
        $encryption = MAIL_ENCRYPTION; // 'tls' | 'ssl' | ''
        $fromEmail  = MAIL_FROM_ADDRESS;
        $fromName   = MAIL_FROM_NAME;

        $transport = ($encryption === 'ssl') ? 'ssl://' : '';
        $socket = @stream_socket_client(
            $transport . $host . ':' . $port,
            $errno,
            $errstr,
            15,
            STREAM_CLIENT_CONNECT
        );

        if (!$socket) {
            throw new \RuntimeException("Could not connect to SMTP host ($errno): $errstr");
        }

        stream_set_timeout($socket, 15);

        self::expect($socket, 220);
        self::command($socket, "EHLO " . self::heloDomain(), 250);

        if ($encryption === 'tls') {
            self::command($socket, "STARTTLS", 220);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new \RuntimeException('STARTTLS negotiation failed.');
            }
            // Must re-EHLO after upgrading the connection.
            self::command($socket, "EHLO " . self::heloDomain(), 250);
        }

        self::command($socket, "AUTH LOGIN", 334);
        self::command($socket, base64_encode($username), 334);
        self::command($socket, base64_encode($password), 235);

        self::command($socket, "MAIL FROM:<{$fromEmail}>", 250);
        self::command($socket, "RCPT TO:<{$to}>", [250, 251]);
        self::command($socket, "DATA", 354);

        $boundary = 'skoolyst-' . bin2hex(random_bytes(8));
        $textBody = $textBody ?: trim(strip_tags($htmlBody));

        $headers = [];
        $headers[] = "From: {$fromName} <{$fromEmail}>";
        $headers[] = "To: <{$to}>";
        $headers[] = "Subject: " . self::encodeHeader($subject);
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";
        $headers[] = "Date: " . date('r');
        $headers[] = "Message-ID: <" . bin2hex(random_bytes(16)) . "@" . self::heloDomain() . ">";

        $body = "";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $textBody . "\r\n\r\n";

        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $htmlBody . "\r\n\r\n";

        $body .= "--{$boundary}--\r\n";

        $message = implode("\r\n", $headers) . "\r\n\r\n" . $body;
        // Dot-stuffing per RFC 5321: lines starting with '.' get an extra '.'
        $message = preg_replace('/^\./m', '..', $message);

        fwrite($socket, $message . "\r\n.\r\n");
        self::expect($socket, 250);

        self::command($socket, "QUIT", 221);
        fclose($socket);
    }

    private static function heloDomain(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return preg_replace('/[^a-zA-Z0-9\.\-]/', '', $host) ?: 'localhost';
    }

    private static function encodeHeader(string $value): string
    {
        // Encode subject as UTF-8 base64 per RFC 2047 (safe for any characters)
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    /**
     * @param resource $socket
     * @param int|int[] $expectedCode
     */
    private static function command($socket, string $line, $expectedCode): string
    {
        fwrite($socket, $line . "\r\n");
        return self::expect($socket, $expectedCode);
    }

    /**
     * @param resource $socket
     * @param int|int[] $expectedCode
     */
    private static function expect($socket, $expectedCode): string
    {
        $response = '';
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            // Multi-line SMTP responses use "-" after the code except on the last line.
            if (isset($str[3]) && $str[3] === ' ') {
                break;
            }
        }

        $code = (int) substr($response, 0, 3);
        $expected = is_array($expectedCode) ? $expectedCode : [$expectedCode];

        if (!in_array($code, $expected, true)) {
            throw new \RuntimeException("Unexpected SMTP response (expected " . implode('/', $expected) . "): {$response}");
        }

        return $response;
    }
}
