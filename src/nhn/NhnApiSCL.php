<?php

namespace nhn;

use pear\Message;

class NhnApiSCL
{
    // AES128 암호알고리즘의 블록사이즈
    const NHNAPISCL_BLOCK_SIZE  = 16;

    // AES128 암호알고리즘에 사용할 고정 Initial Vector
    const NHNAPISCL_IV  = 'c437b4792a856233c183994afe10a6b2';

    /**
     * HMAC-SHA256 서명 생성
     * @param data 서명할 데이터(UTF-8)
     * @param key 서명에 사용할 서명키
     * @return base64 인코딩한 서명값 또는 Error
     */
    function generateSign($data, $key)
    {
        if (strlen($data) == 0) {
            die('invalid data');
        }

        if (strlen($key) == 0) {
            die('invalid key');
        }

        $crypt = Message::createHMAC('SHA256', $key);

        return $crypt->calc($data, 'none', 'base64');
    }

    /**
     * AES128 암호알고리즘에 사용할 암호키 생성
     * @param timestamp 암호키생성에 사용할 데이터
     * @param key 암호키생성에 사용할 secret
     * @return hex 인코딩한 암호키 또는 Error
     */
    function generateKey($timestamp, $key)
    {
        if (strlen($timestamp) == 0) {
            die('invalid timestamp');
        }

        if (strlen($key) == 0) {
            die('invalid key');
        }

        $crypt = Message::createHMAC('SHA256', $key);

        $hmac = $crypt->calc($timestamp, 'none', 'raw');
        $secretkey  = '';
        for($i = 0; $i < 16; $i++) {
            $secretkey .= substr($hmac, $i, 1) ^ substr($hmac, $i+16, 1);
        }

        return bin2hex($secretkey);
    }

    /**
     * NHN API에 사용되는 타임스탬프 생성
     * @return 포맷에 맞춘 타임스탬프
     */
    function getTimestamp()
    {
        $timestamp  = date('Y-m-d\TH:i:s', strtotime("-9 hour"));

        $microtime  = substr(microtime(), 2, 3);

        return $timestamp.".".$microtime."Z".rand(1000, 9999);
    }

    /**
     * PKCS7 패딩생성
     * @param data 패딩할 데이터
     * @param block 암호생성기의 블록사이즈
     * @return pkcs7 패딩을 추가한 데이터
     */
    function p7padding($data, $block)
    {
        $len = strlen($data);
        $padding = $block - ($len % $block);

        return $data . str_repeat(chr($padding), $padding);
    }

    /**
     * PKCS7 패딩제거
     * @param text 패딩제거할 데이터
     * @return pkcs7 패딩을 제거한 데이터 또는 Error
     */
    function p7unpadding($text)
    {
        $pad = ord($text[strlen($text)-1]);

        if ($pad > strlen($text)) {
            die('invalid padding');
        }

        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            die('invalid padding');
        }

        return substr($text, 0, -1 * $pad);
    }

    /**
     * AES128 암호화
     * @param secret hex인코딩한 암호키
     * @param text 암호화할 평문(UTF-8)
     * @return base64 인코딩한 암호값 또는 Error
     */
    function encrypt($secret, $text)
    {
        if (strlen($secret) == 0 ) {
            die('invalid secret');
        }

        if (strlen($text) == 0) {
            die('invalid text');
        }

        $padded = $this->p7padding($text, self::NHNAPISCL_BLOCK_SIZE);
        
        $iv     = pack('H*', self::NHNAPISCL_IV);
        $key    = pack('H*', $secret);
        if (!function_exists('mcrypt_encrypt')) {
            $ctext  = openssl_encrypt($padded, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        } else {
            $ctext  = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $padded, MCRYPT_MODE_CBC, $iv);
        }

        return base64_encode($ctext);
    }

    /**
     * AES128 복호화
     * @param secret hex인코딩한 암호키
     * @param text base64인코딩한 암호값
     * @return 복호화된 평문(UTF-8) 또는 Error
     */
    function decrypt($secret, $text)
    {
        if (strlen($secret) == 0) {
            die('invalid secret');
        }

        if (strlen($text) == 0) {
            die('invalid text');
        }

        $ctext = base64_decode($text);

        $iv     = pack('H*', self::NHNAPISCL_IV);
        $key    = pack('H*',$secret);
        if (!function_exists('mcrypt_decrypt')) {
            $dtext  = openssl_decrypt($ctext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        } else {
            $dtext = $this->p7unpadding(
                mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ctext, MCRYPT_MODE_CBC, $iv)
            );
        }
        
        return $dtext;
    }

    /**
     * SHA256 해쉬 생성
     * @param data 해쉬할 데이터(UTF-8)
     * @return hex 인코딩한 해쉬값 또는 Error
     */
    function sha256($data)
    {
        $crypt = Message::createHash('SHA256');
        return $crypt->calc($data, 'none', 'hex');
    }

}