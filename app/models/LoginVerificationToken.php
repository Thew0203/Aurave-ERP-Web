<?php
namespace App\Models;

use App\Core\Model;

class LoginVerificationToken extends Model
{
    protected string $table = 'login_verification_tokens';

    public function __construct()
    {
        parent::__construct();
        $this->tenantId = null;
    }

    public function createToken(int $loginEventId, string $action, string $token, string $expiresAt): int
    {
        return $this->create([
            'login_event_id' => $loginEventId,
            'token' => $token,
            'action' => $action,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findByToken(string $token): ?array
    {
        return $this->fetchOne("SELECT * FROM {$this->table} WHERE token = ?", [$token]);
    }

    public function markClicked(int $id, ?string $ip): bool
    {
        return $this->update($id, [
            'clicked_at' => date('Y-m-d H:i:s'),
            'click_ip' => $ip,
        ]);
    }
}
