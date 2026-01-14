<?php

namespace App\Service;

class TwoFactorAuthService
{
    /**
     * Génère un secret aléatoire pour TOTP (32 caractères en base32)
     */
    public function generateSecret(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Base32
        $secret = '';
        for ($i = 0; $i < 32; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $secret;
    }

    /**
     * Génère l'URL pour le QR Code (compatible Google Authenticator)
     */
    public function getQRCodeUrl(string $secret, string $email, string $issuer = 'BlogMinouverse'): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            urlencode($issuer),
            urlencode($email),
            $secret,
            urlencode($issuer)
        );
    }

    /**
     * Vérifie un code TOTP
     */
    public function verifyCode(?string $secret, string $code): bool
    {
        if (empty($secret)) {
            return false;
        }

        // Vérifier le code actuel et les 2 précédents/suivants (pour tolérer le décalage)
        $currentTime = time();
        for ($i = -2; $i <= 2; $i++) {
            $timeSlice = floor($currentTime / 30) + $i;
            if ($this->generateCode($secret, $timeSlice) === $code) {
                return true;
            }
        }
        return false;
    }

    /**
     * Génère un code TOTP à partir du secret et du time slice
     */
    private function generateCode(string $secret, int $timeSlice): string
    {
        $key = $this->base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $key, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;
        return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Décode une chaîne base32
     */
    private function base32Decode(string $secret): string
    {
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32charsFlipped = array_flip(str_split($base32chars));

        $paddingCharCount = substr_count($secret, '=');
        $allowedValues = [6, 4, 3, 1, 0];
        if (!in_array($paddingCharCount, $allowedValues)) {
            return '';
        }

        for ($i = 0; $i < 4; $i++) {
            if ($paddingCharCount === $allowedValues[$i] &&
                substr($secret, -($allowedValues[$i])) !== str_repeat('=', $allowedValues[$i])) {
                return '';
            }
        }

        $secret = str_replace('=', '', $secret);
        $secret = str_split($secret);
        $binaryString = '';

        foreach ($secret as $char) {
            if (!isset($base32charsFlipped[$char])) {
                return '';
            }
            $binaryString .= str_pad(decbin($base32charsFlipped[$char]), 5, '0', STR_PAD_LEFT);
        }

        $eightBits = str_split($binaryString, 8);
        $decoded = '';

        foreach ($eightBits as $bits) {
            if (strlen($bits) === 8) {
                $decoded .= chr(bindec($bits));
            }
        }

        return $decoded;
    }

    /**
     * Génère des codes de récupération
     */
    public function generateBackupCodes(int $count = 10): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        }
        return $codes;
    }

    /**
     * Hash un code de récupération pour le stockage sécurisé
     */
    public function hashBackupCode(string $code): string
    {
        return password_hash($code, PASSWORD_BCRYPT);
    }

    /**
     * Vérifie un code de récupération
     */
    public function verifyBackupCode(string $code, string $hashedCode): bool
    {
        return password_verify($code, $hashedCode);
    }
}
