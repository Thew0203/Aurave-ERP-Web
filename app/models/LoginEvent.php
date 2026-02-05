<?php
namespace App\Models;

use App\Core\Model;

class LoginEvent extends Model
{
    protected string $table = 'login_events';

    public function __construct()
    {
        parent::__construct();
        $this->tenantId = null;
    }

    public function createEvent(int $userId, ?string $ip, ?string $userAgent, ?string $deviceInfo = null): int
    {
        return $this->create([
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'device_info' => $deviceInfo,
        ]);
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }
}
