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

use \Diglin\Ricardo\Enums\Article\ArticlesTypes;
use \Diglin\Ricardo\Managers\SellerAccount\Parameter\OpenArticlesParameter;

/**
 * Class Diglin_Ricento_Model_Dispatcher_Sync_List
 */
class Diglin_Ricento_Model_Dispatcher_Sync_List extends Diglin_Ricento_Model_Dispatcher_Abstract
{
    /**
     * @var int
     */
    protected $_logType = Diglin_Ricento_Model_Products_Listing_Log::LOG_TYPE_SYNCLIST;

    /**
     * @var string
     */
    protected $_jobType = Diglin_Ricento_Model_Sync_Job::TYPE_SYNCLIST;

    /**
     * @var array
     */
    protected $_openedArticles;

    /**
     * @return $this
     */
    public function proceed()
    {
        $jobType = Diglin_Ricento_Model_Sync_Job::TYPE_SYNCLIST;

        $productsListingResource = Mage::getResourceModel('diglin_ricento/products_listing');
        $readListingConnection = $productsListingResource->getReadConnection();
        $select = $readListingConnection->select()
            ->from($productsListingResource->getTable('diglin_ricento/products_listing'), 'entity_id');

        $listingIds = $readListingConnection->fetchCol($select);

        foreach ($listingIds as $listingId)
        {
            /**
             * We want items listed and planned because we want to get the new ricardo_article_id
             */
            $itemResource = Mage::getResourceModel('diglin_ricento/products_listing_item');
            $readConnection = $itemResource->getReadConnection();
            $select = $readConnection->select()
                ->from($itemResource->getTable('diglin_ricento/products_listing_item'), 'item_id')
                ->where('products_listing_id = :id')
                ->where('is_planned = 1');

            $binds  = array('id' => $listingId);
            $countListedItems = count($readConnection->fetchAll($select, $binds));

            if ($countListedItems == 0) {
                continue;
            }

            $job = Mage::getModel('diglin_ricento/sync_job');
            $job
                ->setJobType($jobType)
                ->setProgress(Diglin_Ricento_Model_Sync_Job::PROGRESS_PENDING)
                ->setJobMessage(array($job->getJobMessage()))
                ->save();

            $jobListing = Mage::getModel('diglin_ricento/sync_job_listing');
            $jobListing
                ->setProductsListingId($listingId)
                ->setTotalCount($countListedItems)
                ->setTotalProceed(0)
                ->setJobId($job->getId())
                ->save();
        }

        return parent::proceed();
    }

    /**
     * We update the ricardo article ID because it changes when it's planned or opened
     *
     * @return $this
     */
    protected function _proceed()
    {
        $jobListing = $this->_currentJobListing;

        /**
         * Get the list of opened articles (not planned but already live)
         */
        if (empty($this->_openedArticles)) {
            /* @var $sellerAccount Diglin_Ricento_Model_Api_Services_SellerAccount */
            $sellerAccount = Mage::getSingleton('diglin_ricento/api_services_selleraccount');
            $sellerAccount->setCanUseCache(false);

            $this->_openedArticles = $sellerAccount->getOpenArticles(new OpenArticlesParameter());
        }

        $itemCollection = $this->_getItemCollection(array(Diglin_Ricento_Helper_Data::STATUS_LISTED), $jobListing->getLastItemId());
        $itemCollection->addFieldToFilter('is_planned', 1);

        /* @var $item Diglin_Ricento_Model_Products_Listing_Item */
        foreach ($itemCollection->getItems() as $item) {

            $startingDate = strtotime($item->getSalesOptions()->getScheduleDateStart());

            if (!empty($startingDate) && $startingDate < time()) {
                /**
                 * Get the new ricardo article id if the article was planned before
                 */
                $articleId = $this->lookupForRicardoArticleId($item->getInternalReference());

                if ($articleId) {
                    $item
                        ->setRicardoArticleId($articleId)
                        ->setIsPlanned(0)
                        ->save();
                }
            }

            $jobListing->saveCurrentJob(array(
                'total_proceed' => ++$this->_totalProceed,
                'last_item_id' => $item->getId()
            ));
        }

        return $this;
    }

    /**
     * Search the article ID from the internal reference
     * @todo maybe move it to an helper
     *
     * @param string $internalReference
     * @return bool
     */
    protected function lookupForRicardoArticleId($internalReference)
    {
        foreach ($this->_openedArticles as $openArticle) {
            if (isset($openArticle['ArticleInternalReferences']) && !empty($openArticle['ArticleInternalReferences'][0])) {
                if ($openArticle['ArticleInternalReferences'][0]['InternalReferenceValue'] == $internalReference) {
                    return $openArticle['ArticleId'];
                }
            }
        }
        return false;
    }
}