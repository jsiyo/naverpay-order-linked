<?php

namespace pear\Message;

use pear\Message_Common;

/**
 * Class that implements the basic methods for the HMAC digest classes
 * @author  Jesus M. Castagnetto
 * @version 0.6
 * @access  public
 * @package Message
 */
class HMAC_Common extends Message_Common
{
    /**
     * Key to be used for HMAC digest generation
     *
     * @var	string
     * @access	private
     */
    var $key;

    /**
     * Constructor for base HMAC class
     *
     * @param string $hash_name Name of hashing function
     * @param string $key Key to be used for HMAC digest generation
     * @param optional string $ser Serialization mode, one of 'none', 'serialize' or 'wddx'
     * @param optional string $enc Encoding mode of output, one of 'raw', 'hex' or 'base64'
     * @return object Message_HMAC_Common
     * @access public
     */
    function __construct($hash_name, $key, $ser = '', $enc = '')
    {
        parent::__construct($hash_name, $ser, $enc);
        $this->setKey($key);
    }

    /**
     * Sets the key for HMAC digest generation
     *
     * @param string $key Key to be used for HMAC digest generation
     * @return void
     * @access public
     */
    function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Calculates HMAC digest from the input source, using the optional serialization and encoding
     * 
     * @param mixed $input a scalar or a resource from which the data will be read
     * @param optional string $ser Serialization mode, one of 'none', 'serialize' or 'wddx'
     * @param optional string $enc Encoding mode of output, one of 'raw', 'hex' or 'base64'
     * @return	mixed HMAC digest on success, PEAR_Error object otherwise
     * @access public
     */
    function calc($input, $ser = '', $enc = '')
    {
        if (!function_exists('mhash')) {
            return die('Extension mhash not found');
            } else {
                $data = $this->getData($input);
                if (!empty($ser))
                    $this->setSerialization($ser);
                if (!empty($enc))
                    $this->setEncoding($enc);

                $data = $this->serialize($data);
                $sig = mhash(constant($this->hash_name), $data, $this->key);
            return $this->encode($sig);
        }
    }

    /**
     * Validates an HMAC signature against the input source and the internal key, using the optional serialization and encoding

    * 
    * @param mixed $input a scalar or a resource from which the data will be read
    * @param string $signature HMAC signature to be validated
    * @param optional string $ser Serialization mode, one of 'none', 'serialize' or 'wddx'
    * @param optional string $enc Encoding mode of output, one of 'raw', 'hex' or 'base64'
    * @return	mixed True if signature is valid, False if invalid, PEAR_Error object if there was a problem reading the input source
    * @access public
    */
    function validate($input, $signature, $ser = '', $enc = '')
    {
        $data = $this->calc($input, $ser, $enc);
        return (boolean) ($data == $signature);
    }
}