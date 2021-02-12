<?php

namespace pear\Message;

/**
 * Wrapper class for HMAC signature calculation and validation using the SHA256 algorithm
 * @author  Jesus M. Castagnetto
 * @version 0.6
 * @access  public
 * @package Message
 */
class HMAC_SHA256 extends HMAC_Common
{
    /**
     * Constructor for the class Message_HMAC_SHA256
     *
     * @param string $key The key to be used for HMAC digest generation
     * @param optional $ser Serialization mode, one of 'none', 'serialize' or 'wddx'
     * @param optional $enc Encoding mode of output, one of 'raw', 'hex' or 'base64'
     * @return object Message_HMAC_SHA256
     * @access public
     */
	function __construct($key, $ser = '', $enc = '')
    {
		parent::__construct('MHASH_SHA256', $key, $ser, $enc);
	}
}