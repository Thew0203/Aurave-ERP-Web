<?php
/**
 * Login security email template — variables passed when rendering
 * $name, $time, $ip, $device, $yesUrl, $noUrl
 * Used by SecurityMailer::buildEmailHtml() — kept for reference/customization.
 */
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Aurave Security</title></head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Aurave Login Alert</h2>
    <p>Hi <?= htmlspecialchars($name ?? 'User') ?>,</p>
    <p><strong>You logged in to Aurave.</strong> Is this you?</p>
    <p>Time: <?= htmlspecialchars($time ?? '') ?></p>
    <p>IP: <?= htmlspecialchars($ip ?? '') ?></p>
    <p>Device: <?= htmlspecialchars($device ?? '') ?></p>
    <p>
        <a href="<?= htmlspecialchars($yesUrl ?? '#') ?>">Yes, this was me</a> |
        <a href="<?= htmlspecialchars($noUrl ?? '#') ?>">No, this wasn't me</a>
    </p>
</body>
</html>
