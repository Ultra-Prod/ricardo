<?php
class Diglin_Ricento_Adminhtml_Products_Listing_ItemController extends Diglin_Ricento_Controller_Adminhtml_Products_Listing
{
    protected $_productIds = array();
    /**
     * @return Diglin_Ricento_Model_Resource_Products_Listing_Item_Collection
     */
    protected function _initItems()
    {
        /* @var $itemCollection Diglin_Ricento_Model_Resource_Products_Listing_Item_Collection */
        $itemCollection = Mage::getModel('diglin_ricento/products_listing_item')->getCollection();
        if ($this->getRequest()->isPost()) {
            $this->_productIds = array_map('intval', (array) $this->getRequest()->getPost('product', array()));
        } else {
            $this->_productIds = array_map('intval', (array) $this->getRequest()->getParam('product', array()));
        }
        if ($this->_productIds) {
            $itemCollection->addFieldToFilter('products_listing_id', $this->_getListing()->getId())
                ->addFieldToFilter('product_id', array('in' => $this->_productIds));
        } else {
            $itemCollection->addFieldToFilter('item_id', array('in' => explode(',', $this->getRequest()->getPost('item_ids'))));
            //TODO preserve product ids and listing ids for urls
        }
        Mage::register('selected_items', $itemCollection->load());

        return $itemCollection;
    }
    /**
     * Returns items that are selected to be configured
     *
     * @return Diglin_Ricento_Model_Resource_Products_Listing_Item_Collection
     */
    public function getSelectedItems()
    {
        return Mage::registry('selected_items');
    }

    public function configureAction()
    {
        if (!$this->_initListing()) {
            $this->_redirect('*/products_listing/index');
            return;
        }
        if ($this->_initItems()->count() == 0) {
            $this->_getSession()->addError($this->__('No products selected.'));
            $this->_redirect('*/products_listing/edit', array('id' => $this->_getListing()->getId()));
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    public function configurePopupAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (!$this->_initListing()) {
                $this->_redirect('*/products_listing/index');
                return;
            }
            $this->_initItems();
            try {
                if ($this->saveConfiguration($data)) {
                    if (!isset($data['sales_options']['use_products_list_settings'])) {
                        foreach ($this->getSelectedItems() as $item) {
                            /* @var $item Diglin_Ricento_Model_Products_Listing_Item */
                            $item->setSalesOptionsId($item->getSalesOptions()->getId());
                            $item->save();
                        }
                    }
                    $this->_getSession()->addSuccess($this->__('The configuration has been saved.'));
                }
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addException($e, $this->__('An error occurred while saving the listing.'));
            }
        }
        $this->_redirectUrl($this->_getIndexUrl());

    }

    protected function _savingAllowed()
    {
        foreach ($this->getSelectedItems() as $item) {
            /* @var $item Diglin_Ricento_Model_Products_Listing_Item */
            if ($item->getStatus() === Diglin_Ricento_Helper_Data::STATUS_LISTED) {
                return false;
            }
        }
        return true;
    }

    protected function _getSalesOptions()
    {
        if (!$this->_salesOptionsCollection) {
            $this->_salesOptionsCollection = new Varien_Data_Collection();
            foreach ($this->getSelectedItems() as $item) {
                /* @var $item Diglin_Ricento_Model_Products_Listing_Item */
                $this->_salesOptionsCollection->addItem($item->getSalesOptions());
            }
        }
        return $this->_salesOptionsCollection;
    }

    protected function _getEditUrl()
    {
        return $this->getUrl('*/*/configure', array('id' => $this->getRequest()->getParam('id'), 'products' => $this->_productIds()));
    }

    protected function _getIndexUrl()
    {
        return $this->getUrl('*/products_listing/edit', array('id' => $this->getRequest()->getParam('id')));
    }

}