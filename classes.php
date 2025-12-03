<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class LokasiKelas {
    public float $lat;
    public float $lon;
    public int $radius; // meter

    public function __construct(float $lat, float $lon, int $radiusMeters = 50) {
        $this->lat = $lat;
        $this->lon = $lon;
        $this->radius = $radiusMeters;
    }
}

class LokasiMahasiswa {
    public float $lat;
    public float $lon;
    public function __construct(float $lat = 0.0, float $lon = 0.0) {
        $this->lat = $lat;
        $this->lon = $lon;
    }
}

class HitungJarak {
    // Haversine formula -> returns meters
    public static function haversine(LokasiKelas|LokasiMahasiswa $a, LokasiMahasiswa|LokasiKelas $b): float {
        $R = 6371000; // Earth radius in meters
        $lat1 = deg2rad($a->lat);
        $lat2 = deg2rad($b->lat);
        $dLat = $lat2 - $lat1;
        $dLon = deg2rad($b->lon - $a->lon);

        $sinLat = sin($dLat / 2) * sin($dLat / 2);
        $sinLon = sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($sinLat + cos($lat1) * cos($lat2) * $sinLon), sqrt(1 - ($sinLat + cos($lat1) * cos($lat2) * $sinLon)));
        return $R * $c;
    }
}

class AbsensiQueue {
    private string $sessionKey = 'absensi_queue';
    public function __construct() {
        if (!isset($_SESSION[$this->sessionKey]) || !is_array($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
    }

    public function enqueue(array $data): void {
        $_SESSION[$this->sessionKey][] = $data;
    }

    public function getAll(): array {
        return $_SESSION[$this->sessionKey] ?? [];
    }

    public function clear(): void {
        $_SESSION[$this->sessionKey] = [];
    }
}


// helper sanitasi sederhana
function clean(string $s): string {
    return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
}