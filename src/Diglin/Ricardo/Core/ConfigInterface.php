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
namespace Diglin\Ricardo\Core;

/**
 * Interface ConfigInterface
 * @package Diglin\Ricardo\Core
 */
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
    public function getPartnershipKey();

    /**
     * Get the Partnership Password configuration
     *
     * @return mixed
     */
    public function getPartnershipPasswd();
}
