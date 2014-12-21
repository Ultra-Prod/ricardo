<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author Sylvain Rayé <support at diglin.com>
 * @category    Ricento
 * @package     Ricento_
 * @copyright   Copyright (c) 2011-2015 Diglin (http://www.diglin.com)
 */ 
class Diglin_Ricento_Adminhtml_SupportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $supportLabel = $this->__('Ricento Extension Support');

        $block = $this->getLayout()->createBlock('core/template');

        $block
            ->setTemplate('ricento/support.phtml')
            ->setTitle($supportLabel);
//            ->setIframeUrl(Mage::helper('diglin_ricento')->getRicardoAssistantUrl());

        $this->_title($supportLabel);

        $this->loadLayout()
            ->_setActiveMenu('ricento/support')
            ->_addBreadcrumb($supportLabel, $supportLabel)
            ->_addContent($block)
            ->renderLayout();
    }
}