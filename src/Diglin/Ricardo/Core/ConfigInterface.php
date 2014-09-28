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
namespace Diglin\Ricardo\Core;

interface ConfigInterface
{
    /**
     * Get the Ricardo host configuration
     *
     * @return mixed
     */
    public function getHost();

    /**
     * Get the Partnership ID configuration
     *
     * @return mixed
     */
    public function getPartnershipId();

    /**
     * Get the Partnership Password configuration
     *
     * @return mixed
     */
    public function getPartnershipPasswd();
}