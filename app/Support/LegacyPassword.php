<?php

namespace App\Support;

use Illuminate\Support\Facades\Hash;

/**
 * Verificación de contraseñas del esquema legacy.
 *
 * El dump mezcla dos formatos en `register.password`:
 *  - bcrypt:  $2y$10$...  (password_verify)
 *  - AES-256-CBC legacy: base64(iv 16 bytes + ciphertext) con la clave
 *    sha256("HERMES") y OPENSSL_RAW_DATA (register/crud.php del legacy).
 */
class LegacyPassword
{
    private const LEGACY_KEY = 'HERMES';

    /** Comprueba una contraseña en cualquiera de los dos formatos. */
    public static function verify(string $password, ?string $hash): bool
    {
        if ($hash === null || $hash === '') {
            return false;
        }

        if (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$2a$')) {
            return Hash::check($password, $hash);
        }

        return self::verifyLegacyAes($password, $hash);
    }

    /** Descifra y compara el formato AES del legacy (clave "HERMES"). */
    public static function verifyLegacyAes(string $password, string $stored): bool
    {
        try {
            $raw = base64_decode($stored, true);
            if ($raw === false || strlen($raw) < 17) {
                return false;
            }

            $iv = substr($raw, 0, 16);
            $ciphertext = substr($raw, 16);
            $key = hash('sha256', self::LEGACY_KEY, true);

            $decrypted = openssl_decrypt(
                $ciphertext,
                'aes-256-cbc',
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );

            return is_string($decrypted) && hash_equals($decrypted, $password);
        } catch (\Throwable) {
            return false;
        }
    }
}