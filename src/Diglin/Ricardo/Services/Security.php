<?php
/**
 * Diglin GmbH - Switzerland
 *
 * This file is part of a Diglin GmbH module.
 *
 * This Diglin GmbH module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Ricardo
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
namespace Diglin\Ricardo\Services;

/**
 * Class Security
 *
 * Ricardo SecurityService API
 * Manage Token generation
 *
 * @package Diglin\Ricardo\Services
 * @link https://ws.ricardo.ch/RicardoApi/documentation/html/Methods_T_Ricardo_Contracts_ISecurityService.htm
 */
class Security extends ServiceAbstract
{
    const VALIDATION_SAVE_PATH = '/apiconnect/login/saveinfo/saveinfo';

    /**
     * @var string
     */
    protected $_service = 'SecurityService';

    /**
     * @var string
     */
    protected $_typeOfToken = self::TOKEN_TYPE_DEFAULT;

    /**
     * Some Ricardo API Services don't need to have a registered token like
     * SystemService, ArticleService, SearchService, BrandingService
     * but they need an anonymous token
     *
     * @return array
     */
    public function getAnonymousTokenCredential()
    {
        return array(
            'method' => 'GetAnonymousTokenCredential',
            'params' =>  array('getAnonymousTokenCredentialParameter' => array())
        );
    }

    /**
     * Get the result fo the API call to get the anonymous token
     *
     * The Ricardo API returns:
     * <pre>
     * {
     *     "GetAnonymousTokenCredentialResult": {
     *      "TokenCredential": {
     *       "SessionDuration":0,
     *       "TokenCredentialKey":"[ANONYMOUS_TOKEN]",
     *       "TokenExpirationDate":"\/Date(3453314340000+0200)\/"
     *      }
     *     }
     *  }
     * </pre>
     *
     * Array returned:
     * <pre>
     * array(
     *     'SessionDuration',
     *     'TokenCredentialKey',
     *     'TokenExpirationDate'
     * );
     * </pre>
     *
     * @param array $data
     * @return array
     */
    public function getAnonymousTokenCredentialResult(array $data)
    {
        if (isset($data['GetAnonymousTokenCredentialResult']) && isset($data['GetAnonymousTokenCredentialResult']['TokenCredential'])) {
            return $data['GetAnonymousTokenCredentialResult']['TokenCredential'];
        }

        return array();
    }

    /**
     * Ask for temporary credential for very first time use. Return a validationUrl where to redirect a user
     * to autorize the application and Temporary Key.
     *
     * @return array
     */
    public function getTemporaryCredential()
    {
        return array(
            'method' => 'CreateTemporaryCredential',
            'params' => array('createTemporaryCredentialParameter' => array())
        );
    }

    /**
     * Get the result of the temporary credential.
     * Take care here, the user will have to be redirected to validate it thanks to the validationUrl variable
     *
     *
     * The Ricardo API returns:
     * <pre>
     * {
     *     "CreateTemporaryCredentialResult": {
     *       "TemporaryCredential": {
     *         "ExpirationDate": "\/Date(1385462160000+0100)\/",
     *         "TemporaryCredentialKey": "[TEMPORARY_TOKEN]",
     *         "ValidationUrl": "http://www.ch.betaqxl.com/ApiConnect/Login/Index?token=XXXXX-XXXX-XXXX-XXXX-XXXXXXXXXX&countryId=2&partnershipId=XXXX&partnerurl=http://www.myshop.com/mypage/"
     *       }
     *     }
     *   }
     * </pre>
     *
     * Array returned:
     * <pre>
     * array(
     *     'ExpirationDate',
     *     'TemporaryCredentialKey',
     *     'ValidationUrl'
     * );
     * </pre>
     *
     * @param array $data
     * @return array
     */
    public function getTemporaryCredentialResult($data)
    {
        if (isset($data['CreateTemporaryCredentialResult']) && isset($data['CreateTemporaryCredentialResult']['TemporaryCredential'])) {
            return $data['CreateTemporaryCredentialResult']['TemporaryCredential'];
        }

        return array();
    }

    /**
     * Ask for the "real" token, providing the [TEMPORARY_TOKEN] received from the method createTemporaryCredential
     * and also as a get parameter when user is returning from the validationURl.
     *
     * @param string $temporaryCredentialKey
     * @return array
     */
    public function getTokenCredential($temporaryCredentialKey)
    {
        return array(
            'method' => 'CreateTokenCredential',
            'params' => array('createTokenCredentialParameter' => array('TemporaryCredentialKey' => $temporaryCredentialKey))
        );
    }

    /**
     * Get the result of the token credential
     *
     * The Ricardo API returns:
     * <pre>
     * {
     *     "CreateTokenCredentialResult": {
     *       "TokenCredential": {
     *         "SessionDuration": 30,
     *         "TokenCredentialKey": "[REAL_TOKEN]",
     *         "TokenExpirationDate": "\/Date(1386664920000+0100)\/"
     *       }
     *     }
     *   }
     * </pre>
     *
     * Array returned:
     * <pre>
     * array(
     *     'SessionDuration',
     *     'TokenCredentialKey',
     *     'TokenExpirationDate'
     * );
     * </pre>
     *
     * @param array $data
     * @return array
     */
    public function getTokenCredentialResult($data)
    {
        if (isset($data['CreateTokenCredentialResult']) && isset($data['CreateTokenCredentialResult']['TokenCredential'])) {
            return $data['CreateTokenCredentialResult']['TokenCredential'];
        }

        return array();
    }

    /**
     * After the SessionDuration timeout, the token need to be refreshed
     * You will get a new token credential in return. If TokenExpirationDate is above
     * of the current date, you will have to create again a temporary credential (sic!)
     *
     * @param string $tokenCredentialKey
     * @return array
     */
    public function getRefreshTokenCredential($tokenCredentialKey)
    {
        return array(
            'method' => 'RefreshTokenCredential',
            'params' => array('refreshTokenCredentialParameter' => array('TokenCredentialKey' => $tokenCredentialKey))
        );
    }

    /**
     * Get the refreshed the token
     *
     * The Ricardo API returns:
     * <pre>
     * {
     *     "RefreshTokenCredentialResult": {
     *       "TokenCredential": {
     *         "SessionDuration": 30,
     *         "TokenCredentialKey": "[REAL_TOKEN]",
     *         "TokenExpirationDate": "\/Date(1386664920000+0100)\/"
     *       }
     *     }
     *   }
     * </pre>
     *
     * Array returned:
     * <pre>
     * array(
     *     'TokenCredentialKey'
     * );
     * </pre>
     *
     * @param array $data
     * @return array
     */
    public function getRefreshTokenCredentialResult(array $data)
    {
        if (isset($data['RefreshTokenCredentialResult']) && isset($data['RefreshTokenCredentialResult']['TokenCredential'])) {
            return $data['RefreshTokenCredentialResult']['TokenCredential'];
        }

        return array();
    }

    /**
     * Some API methods needs an antiforgery token to prevent Man-In-The-Middle attack
     *
     * @return array
     */
    public function getAntiforgeryToken()
    {
        return array(
            'method' => 'CreateAntiforgeryToken',
            'params' => array('createAntiforgeryTokenParameter' => array())
        );
    }

    /**
     * Get the antiforgery token
     *
     * The Ricardo API returns:
     * <pre>
     * {
     *     "CreateAntiforgeryTokenResult": {
     *         "AntiforgeryTokenKey": "[REAL_TOKEN]",
     *         "TokenExpirationDate": "\/Date(1386664920000+0100)\/"
     *     }
     *   }
     * </pre>
     *
     * Array returned:
     * <pre>
     * array(
     *     'AntiforgeryTokenKey'
     *     'TokenExpirationDate'
     * );
     * </pre>
     *
     * @param array $data
     * @return array
     */
    public function getAntiforgeryTokenResult(array $data)
    {
        if (isset($data['CreateAntiforgeryTokenResult'])) {
            return $data['CreateAntiforgeryTokenResult'];
        }

        return array();
    }
}
