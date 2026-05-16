<?php

namespace App\Core;

/**
 * Simple mailer using PHP mail() with HTML support.
 * For production on cPanel / Brevo SMTP, configure SMTP relay in cPanel's
 * "Email Routing" or use Brevo's SMTP relay (configure in php.ini / .user.ini).
 */
class Mailer
{
    /**
     * Send an HTML email.
     *
     * @param string $to      Recipient email address
     * @param string $subject Email subject
     * @param string $body    HTML body
     * @return bool
     */
    public static function send(string $to, string $subject, string $body): bool
    {
        $fromName  = defined('APP_NAME') ? APP_NAME : 'EZKIRA';
        $fromEmail = defined('MAIL_FROM') ? MAIL_FROM : 'noreply@ezkira.com';

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
        $headers .= "Reply-To: {$fromEmail}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        // Wrap body in minimal HTML structure if not already
        if (!str_contains($body, '<html')) {
            $body = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $body . '</body></html>';
        }

        return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
    }
}
