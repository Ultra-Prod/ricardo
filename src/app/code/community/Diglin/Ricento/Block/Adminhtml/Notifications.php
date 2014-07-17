<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author Sylvain Rayé <sylvain.raye at diglin.com>
 * @category    Ricento
 * @package     Diglin_Ricardo
 * @copyright   Copyright (c) 2011-2014 Diglin (http://www.diglin.com)
 */

/**
 * Class Diglin_Ricento_Block_Adminhtml_Notifications
 */
class Diglin_Ricento_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{
    /**
     * Disable the block caching for this block
     */
    protected function _construct()
    {
        $this->addData(array('cache_lifetime'=> null));
    }

    /**
     * Indicates if the extension is configured.
     *
     * @return bool
     */
    public function isConfigured()
    {
        return $this->helper('diglin_ricento')->isConfigured();
    }

    /**
     * Get the configuration url
     *
     * @return string URL for MageSetup form
     */
    public function getConfigurationUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit/section/ricento');
    }

    /**
     * ACL validation before html generation
     *
     * @return string Notification content
     */
    protected function _toHtml()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('system/ricento')) {
            return parent::_toHtml();
        }

        return '';
    }
}