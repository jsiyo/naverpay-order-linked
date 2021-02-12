<?php

namespace pear;

class Message
{
    /**
     * Factory method to create an object instance that can 
     * calculate the hash value of data using a given algorithm.
     *
     * @param	string	$hash_name	name of the hashing algorithm to use
     * @param	optional	string	$ser	data serialization method
     * @param	optional	string	$enc	data encoding method
     * @return	object	a child class of Message_Hash_Common on success, a PEAR::Error object otherwise
     * @access	public
     */
    function &hash($hash_name, $ser = '', $enc = '')
    {
        if (!function_exists('mhash')) {
            if (!Message::_inFallback($hash_name)) {
                die('Could not find the mhash extension, and '.
                'could not fallback to PHP implementation');
            } else {
                $hash_name = strtoupper($hash_name);
                include_once "Message/Hash/{$hash_name}_Fallback.php";
                $hash_class = "Message_Hash_{$hash_name}_Fallback";
                return new $hash_class($ser, $enc);
            }
        } else {
            // mangle hash name to compare to mhash's constants
            list($hash, $hash_name) = Message::_mangle($hash_name);
            if (!defined($hash)) {
                die("Unsupported hash: $hash_name");
            } else {
                $hash   = __NAMESPACE__."\Message\HASH_{$hash_name}";
                $instance   = new $hash($ser, $enc);
                return $instance;
            }
        }
    }

    /**
     * Alias of Message::hash()
     *
     * @access	public
     * @see		Message::hash()
     */
    function &createHash($hash_name, $ser = '', $enc = '')
    {
        return Message::hash($hash_name, $ser, $enc);
    }

    /**
     * Factory method to create and object instance that can
     * calculate the HMAC digest value of data using a given algorithm.
     *
     * @param	string	$hash_name	name of the hashing algorithm to use
     * @param	string	$key	the secret key used in the HMAC function	
     * @param	optional	string	$ser	data serialization method
     * @param	optional	string	$enc	data encoding method
     * @return	object	a child class of Message_Hash_Common on success, a PEAR::Error object otherwise
     * @access	public
     */
    static function &hmac($hash_name, $key, $ser = '', $enc = '')
    {
        $cannot_hmac = array('CRC32', 'GOST', 'CRC32B', 'ADLER32');
        if (!function_exists('mhash')) {
            if (!Message::_inFallback($hash_name)) {
                die('Could not find the mhash extension, and '.
            'could not fallback to PHP implementation');
            } else {
                list($hash, $hash_name) = Message::_mangle($hash_name);
                if (in_array($hash_name, $cannot_hmac)) {
                    die("Unsupported hmac: $hash_name");
                } else {
                    include_once "Message/HMAC/{$hash_name}_Fallback.php";
                    $hmac = "Message_HMAC_{$hash_name}_Fallback";
                    return new $hmac($key, $ser, $enc);
                }
            }
        } else {
            // mangle hash name to compare to mhash's constants
            list($hash, $hash_name) = Message::_mangle($hash_name);
            if (!defined($hash) || in_array($hash_name, $cannot_hmac)) {
                die("Unsupported hmac: $hash_name");
            } else {
                $hamc   = __NAMESPACE__."\Message\HMAC_{$hash_name}";
                $instance   = new $hamc($key, $ser, $enc);
                return $instance;
            }
        }
    }
    
    /**
     * Alias of Message::hmac()
     *
     * @access	public
     * @see		Message::hmac()
     */
    static function &createHMAC($hash_name, $key, $ser = '', $enc = '')
    {
        return Message::hmac($hash_name, $key, $ser, $enc);
    }

    /**
     * Static method to calculate the hash value of data using the given
     * algorithm.
     *
     * @param	string	$hash_name	name of the hashing algorithm to use
     * @param	string	$data	the input data
     * @param	optional	string	$ser	data serialization method
     * @param	optional	string	$enc	data encoding method
     * @return	mixed	the hash on success, a PEAR::Error object otherwise
     * @access	public
     */
    function calcHash($hash_name, $data, $ser = 'none', $enc = 'hex')
    {
        if (!function_exists('mhash')) {
            die('Could not find the mhash extension');
        } else {
            $hash =& Message::hash($hash_name, $ser, $enc);
            return $hash->calc($data);
        }
    }

    /**
     * Static method to calculate the HMAC digest value of data using the given
     * algorithm.
     *
     * @param	string	$hash_name	name of the hashing algorithm to use
     * @param	string	$data	the input data
     * @param	string	$key	the secret key used in the HMAC function	
     * @param	optional	string	$ser	data serialization method
     * @param	optional	string	$enc	data encoding method
     * @return	mixed	the hash on success, a PEAR::Error object otherwise
     * @access	public
     */
    static function calcHMAC($hash_name, $data, $key, $ser = 'none', $enc = 'hex')
    {
        if (!function_exists('mhash')) {
            return die('Could not find the mhash extension');
        } else {
            $hmac =& Message::hmac($hash_name, $key, $ser, $enc);
            return $hmac->calc($data);
        }
    }

    /**
     * Static method to verify the HMAC digest value of data using the given
     * algorithm.
     *
     * @param	string	$hash_name	name of the hashing algorithm to use
     * @param	string	$data	the input data
     * @param	string	$signature	the input digest (signature) value
     * @param	string	$key	the secret key used in the HMAC function	
     * @param	optional	string	$ser	data serialization method
     * @param	optional	string	$enc	data encoding method
     * @return	mixed	True/False on success, a PEAR::Error object otherwise
     * @access	public
     */
    function validateHMAC($hash_name, $data, $signature, $key, $ser = '', $enc = '')
    {
        $hmac = Message::calcHMAC($hash_name, $data, $key, $ser, $enc);
        return (boolean) ($hmac == $signature);
    }

    /**
     * Private method to force the algorithm name into a value that
     * matches libmhash's constants
     *
     * @param	string	$hash_name	name of the hashing algorithm
     * @return	string	a string the matches libmhash's constants pattern
     * @access	private
     */
    static function _mangle($hash_name)
    {
        if (preg_match('/^MHASH_/', $hash_name)) {
            $hash = $hash_name;
        } else {
            $hash = 'MHASH_'.$hash_name;
        }
        list(,$hash_name) = explode('_', $hash);
        return array($hash, $hash_name);
    }

    static function _inFallback($hash_name)
    {
        return in_array(strtolower($hash_name), array('md5', 'sha1'));
    }    
}