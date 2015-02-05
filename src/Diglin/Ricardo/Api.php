<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Ricardo
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Diglin\Ricardo;

use Diglin\Ricardo\Core\ApiInterface;
use Diglin\Ricardo\Core\ConfigInterface;
use Diglin\Ricardo\Exceptions\CurlException;

/**
 * Class Api
 *
 * Prepare HTTP headers, send params, connect and get response from the API
 *
 * @package Diglin\Ricardo
 */
class Api implements ApiInterface
{
    /**
     * @var Core\ConfigInterface
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_username;

    /**
     * @var string
     */
    protected $_partnerKey;

    /**
     * @var bool
     */
    protected $_debug = false;

    /**
     * @var array
     */
    protected $_lastDebug = array();

    /**
     * @var bool
     */
    protected $_shouldSetPass = true;

    public function __construct(ConfigInterface $config)
    {
        $this->_config = $config;
        $this->_username = $this->_partnerKey = $config->getPartnershipKey();

        if ($config->get('debug')) {
            $this->_debug = $config->get('debug');
        }
    }

    /**
     * Connect to the Ricardo API depending of the service and method to be used
     *
     * @param string $service Ricardo API Service
     * @param string $method Ricardo API Method
     * @param array|string $params API Parameters
     * @return mixed $result
     * @throws \Exception
     */
    public function connect($service, $method, $params)
    {
        if (!$this->getConfig()->getHost()) {
            throw new \Exception('Configuration Host not set.');
        }

        $curlOptions = array(
            CURLOPT_URL => 'https://' . $this->getConfig()->getHost() . '/ricardoapi/' . $service . '.Json.svc/' . $method,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => ((is_array($params)) ? json_encode($params) : $params),
            CURLOPT_HTTPHEADER => $this->_addHeaders(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_CIPHER_LIST => 'TLSv1'
        );

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $return = json_decode(curl_exec($ch), true);

        if ($this->_debug) {
            if (isset($curlOptions[CURLOPT_HTTPHEADER])) {
                $anonHeaders = array();
                foreach ($curlOptions[CURLOPT_HTTPHEADER] as $header) {
                    if (strpos($header, 'Ricardo-') !== false) {
                        $sub = strpos($header, ':');
                        $anonHeaders[] = substr($header, 0, $sub) . ': XXXXXXXX';
                    } else {
                        $anonHeaders[] = $header;
                    }
                }
                $curlOptions[CURLOPT_HTTPHEADER] = $anonHeaders;
            }

            // It may take too much memory here as some parameter are pictures bytes
            unset($curlOptions[CURLOPT_POSTFIELDS]);
            $this->removePictureBytesData($params);
            $this->_lastDebug = json_encode(array(
                'curl_options'  => $curlOptions,
                'params'        => $params,
                'return'        => $return
            ));

            if ($this->getConfig()->getLogFilePath()) {
                $dir = dirname($this->getConfig()->getLogFilePath());
                @mkdir($dir, 0775);
                if (is_writable($dir . DIRECTORY_SEPARATOR)) {
                    file_put_contents($this->getConfig()->getLogFilePath(), print_r($this->_lastDebug, true), FILE_APPEND);
                }
            }
        }

        if (curl_errno($ch)) {
            throw new CurlException('Error while trying to connect with the API - Curl Error Number: ' . curl_errno($ch) . ' - Curl Error Message: ' . curl_error($ch), curl_errno($ch));
        }

        curl_close($ch);

        unset($params);

        return $return;
    }

    /**
     * Set correct HTTP headers for the API
     *
     * @return array
     */
    protected function _addHeaders()
    {
        $headers = array(
            'Content-Type: application/json',
            'Host: ' . $this->getConfig()->getHost(),
            'Ricardo-Username: ' . $this->getUsername()
        );

        if ($this->getShouldSetPass()) {
            $headers[] = 'Ricardo-Password: ' . $this->getConfig()->getPartnershipPasswd();
        }

        return $headers;
    }

    /**
     * Get the configuration class of the API
     *
     * @return \Diglin\Ricardo\Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Username is in fact the API key but we call it username
     * to be homogeneous with the header naming convention
     *
     * @param mixed $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }

    /**
     * Get the username
     *
     * @return mixed
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Set if the pass should be send or not
     *
     * @param boolean $boolean
     * @return $this
     */
    public function setShouldSetPass($boolean)
    {
        $this->_shouldSetPass = $boolean;
        return $this;
    }

    /**
     * Get if the pass should be send or not
     *
     * @return boolean
     */
    public function getShouldSetPass()
    {
        return $this->_shouldSetPass;
    }

    /**
     * Get the content of the headers and content of one or more API calls
     *
     * @param bool $flush
     * @return array
     */
    public function getLastDebug($flush = false)
    {
        $return = $this->_lastDebug;

        if ($flush) {
            $this->_lastDebug = array();
        }
        return $return;
    }

    /**
     * The username can be a token or the partner ID.
     * In some cases it is needed to get back the partner ID
     *
     * @return string
     */
    public function revertUsername()
    {
        $this->_username = $this->_partnerKey;
        return $this;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function removePictureBytesData(&$params)
    {
        $srchvalue = 'PictureBytes';
        $foundkey = false;

        if (is_array($params) && count($params) > 0) {
            $foundkey = array_key_exists($srchvalue, $params);
            if ($foundkey === false) {
                foreach ($params as $key => &$value) {
                    if (is_array($value) && count($value) > 0) {
                        $this->removePictureBytesData($value);
                    }
                }
            } else {
                $params[$srchvalue] = array('Picture bytes deleted for debug');

            }
        }

        return $foundkey;
    }
}
