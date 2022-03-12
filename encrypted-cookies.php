<?php

class Cookie
{


    private static $key = 'cd19c8ee008909f078b2af45685340d8026e11b8a6e2a272d37d41382b72a81b';


    static public function set_encrypted($name, $data)
    {
        $encrypted_cookie_data = self::encrypt(json_encode($data));
        setcookie($name, $encrypted_cookie_data, time() + (24 * 60 * 60), '/', '.example.com');
    }

    static public function get_encrypted($name)
    {
        if (!isset($_COOKIE[$name])) {
            return false;
        }

        $encoded_result = $_COOKIE[$name];
        $data = self::decrypt($encoded_result);

        return $data;
    }

    static private function encrypt($data)
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $encrypted_result = sodium_crypto_secretbox($data, $nonce, sodium_hex2bin(self::$key));
        $encoded_result = base64_encode($nonce . $encrypted_result);
        return $encoded_result;
    }


    static private function decrypt($encoded_result)
    {
        try {
            $decoded_result = base64_decode($encoded_result);
            $nonce = mb_substr($decoded_result, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
            $encrypted_result = mb_substr($decoded_result, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
            $decrypted_result = sodium_crypto_secretbox_open($encrypted_result, $nonce, sodium_hex2bin(self::$key));

            return $decrypted_result;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
