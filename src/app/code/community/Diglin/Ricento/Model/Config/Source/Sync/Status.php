<?php

/**
 * Diglin GmbH - Switzerland
 *
 * @author Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Diglin_Ricento
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Diglin_Ricento_Model_Config_Source_Sync_Status extends Diglin_Ricento_Model_Config_Source_Abstract
{
    public function toOptionHash()
    {
        $helper = Mage::helper('diglin_ricento');

        return array(
            Diglin_Ricento_Model_Sync_Job::STATUS_NOTICE => $helper->__('Notice'),
            Diglin_Ricento_Model_Sync_Job::STATUS_SUCCESS => $helper->__('Success'),
            Diglin_Ricento_Model_Sync_Job::STATUS_ERROR => $helper->__('Error'),
            Diglin_Ricento_Model_Sync_Job::STATUS_WARNING => $helper->__('Warning')
        );
    }
}