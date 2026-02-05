<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SecurityMailer
{
    private static function isEnabled(): bool
    {
        return filter_var(getenv('MAILER_ENABLED'), FILTER_VALIDATE_BOOLEAN);
    }

    private static function getConfig(): array
    {
        return [
            'host' => getenv('SMTP_HOST') ?: 'smtp.example.com',
            'port' => (int) (getenv('SMTP_PORT') ?: 587),
            'user' => getenv('SMTP_USER') ?: '',
            'pass' => getenv('SMTP_PASS') ?: '',
            'from_email' => getenv('MAILER_FROM_EMAIL') ?: 'noreply@aurave.local',
            'from_name' => getenv('MAILER_FROM_NAME') ?: 'Aurave System',
        ];
    }

    /**
     * Send login security alert to the user who just logged in.
     * Recipient = logged-in user's email (from DB). Sender = Aurave (from .env). No hardcoding.
     */
    public static function sendLoginAlert(array $user, array $loginEvent, array $tokens): bool
    {
        if (!self::isEnabled()) {
            return false;
        }
        $toEmail = trim((string) ($user['email'] ?? ''));
        if ($toEmail === '' || $toEmail === 'superadmin@system.local') {
            return false;
        }

        $cfg = self::getConfig();
        if ($cfg['user'] === '' || $cfg['pass'] === '') {
            error_log('SecurityMailer: SMTP_USER or SMTP_PASS missing in .env');
            return false;
        }

        $config = require (defined('APP_PATH') ? APP_PATH : dirname(__DIR__)) . '/config/app.php';
        $baseUrl = rtrim($config['url'], '/');

        $ip = $loginEvent['ip_address'] ?? 'Unknown';
        $ua = $loginEvent['user_agent'] ?? 'Unknown';
        $time = $loginEvent['created_at'] ?? date('Y-m-d H:i:s');
        $deviceInfo = self::parseUserAgent($ua);

        $yesUrl = $baseUrl . '/auth/verify-login?token=' . urlencode($tokens['yes'] ?? '') . '&action=yes';
        $noUrl = $baseUrl . '/auth/verify-login?token=' . urlencode($tokens['no'] ?? '') . '&action=no';

        $html = self::buildEmailHtml($user['name'] ?? 'User', $time, $ip, $deviceInfo, $yesUrl, $noUrl);

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $cfg['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $cfg['user'];
            $mail->Password = $cfg['pass'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $cfg['port'];
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAutoTLS = true;

            if (!empty($config['debug'])) {
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = static function ($str) {
                    error_log('SecurityMailer SMTP: ' . trim($str));
                };
            }

            $mail->setFrom($cfg['from_email'], $cfg['from_name']);
            $mail->addAddress($toEmail, $user['name'] ?? '');
            $mail->Subject = 'Aurave: You logged in. Is this you?';
            $mail->isHTML(true);
            $mail->Body = $html;
            $mail->AltBody = "You logged in to Aurave at {$time} from IP {$ip}.\n\n"
                . "Yes, this was me: {$yesUrl}\n"
                . "No, this wasn't me: {$noUrl}\n\n"
                . "Links expire in 10 minutes.";

            $mail->send();
            return true;
        } catch (\Throwable $e) {
            error_log('SecurityMailer: ' . $e->getMessage());
            return false;
        }
    }

    private static function parseUserAgent(string $ua): string
    {
        if ($ua === '' || $ua === 'Unknown') return 'Unknown';
        if (preg_match('/Firefox/i', $ua)) return 'Firefox';
        if (preg_match('/Chrome/i', $ua)) return 'Chrome';
        if (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) return 'Safari';
        if (preg_match('/Edge/i', $ua)) return 'Edge';
        if (preg_match('/MSIE|Trident/i', $ua)) return 'Internet Explorer';
        return 'Browser';
    }

    private static function buildEmailHtml(string $name, string $time, string $ip, string $device, string $yesUrl, string $noUrl): string
    {
        return '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Aurave Security</title></head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 500px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #2563eb;">Aurave Login Alert</h2>
    <p>Hi ' . htmlspecialchars($name) . ',</p>
    <p><strong>You logged in to Aurave.</strong> Is this you?</p>
    <table style="background: #f8fafc; border-radius: 8px; padding: 16px; margin: 16px 0;">
        <tr><td><strong>Time</strong></td><td>' . htmlspecialchars($time) . '</td></tr>
        <tr><td><strong>IP</strong></td><td>' . htmlspecialchars($ip) . '</td></tr>
        <tr><td><strong>Device</strong></td><td>' . htmlspecialchars($device) . '</td></tr>
    </table>
    <p>If you recognize this login:</p>
    <p>
        <a href="' . htmlspecialchars($yesUrl) . '" style="display: inline-block; background: #22c55e; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-right: 8px;">Yes, this was me</a>
        <a href="' . htmlspecialchars($noUrl) . '" style="display: inline-block; background: #ef4444; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">No, this wasn\'t me</a>
    </p>
    <p style="color: #64748b; font-size: 12px;">Links expire in 10 minutes. If you did not log in, click "No" and consider changing your password.</p>
    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
    <p style="color: #94a3b8; font-size: 11px;">Aurave ERP — Security Notification</p>
</body>
</html>';
    }
}
