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
namespace Diglin\Ricardo\Managers\SellerAccount\Parameter;

use Diglin\Ricardo\Enums\Article\ArticlesTypes;
use Diglin\Ricardo\Enums\Customer\OpenArticlesSortBy;
use \Diglin\Ricardo\Managers\ParameterAbstract;

/**
 * Class OpenArticlesParameter
 * @package Diglin\Ricardo\Managers\SellerAccount\Parameter
 */
class OpenArticlesParameter extends ParameterAbstract
{
    /**
     * Required
     *
     * @var string
     */
    protected $_articleTitleFilter = null;

    /**
     * Required
     *
     * @var int
     */
    protected $_articleTypeFilter = ArticlesTypes::ALL;

    /**
     * Required
     *
     * @var string
     */
    protected $_internalReferenceFilter = null;

    /**
     * Required
     *
     * @var string
     */
    protected $_lastnameFilter = null;

    /**
     * Required
     *
     * @var string
     */
    protected $_nicknameFilter = null;

    /**
     * Optional
     *
     * @var array
     */
    protected $_articleIdsFilter = array();

    /**
     * Optional
     *
     * @var bool
     */
    protected $_ascendingSort = true;

    /**
     * Optional
     *
     * @var int
     */
    protected $_pageNumber;

    /**
     * Optional
     *
     * @var int
     */
    protected $_pageSize;

    /**
     * Optional
     *
     * @var int
     */
    protected $_sortBy;

    /**
     * @var array
     */
    protected $_requiredProperties = array(
        'articleTitleFilter',
        'articleTypeFilter',
        'internalReferenceFilter',
        'lastnameFilter',
        'nicknameFilter',
    );

    protected $_optionalProperties = array(
        'articleIdsFilter',
        'ascendingSort',
        'pageNumber',
        'pageSize',
        'sortBy',
    );

    /**
     * @param array $articleIdsFilter
     * @return $this
     */
    public function setArticleIdsFilter($articleIdsFilter)
    {
        $this->_articleIdsFilter = $articleIdsFilter;
        return $this;
    }

    /**
     * @return array
     */
    public function getArticleIdsFilter()
    {
        return (array) $this->_articleIdsFilter;
    }

    /**
     * @param mixed $articleTitleFilter
     * @return $this
     */
    public function setArticleTitleFilter($articleTitleFilter)
    {
        $this->_articleTitleFilter = $articleTitleFilter;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getArticleTitleFilter()
    {
        return $this->_articleTitleFilter;
    }

    /**
     * @param int $articleTypeFilter
     * @return $this
     */
    public function setArticleTypeFilter($articleTypeFilter)
    {
        $this->_articleTypeFilter = $articleTypeFilter;
        return $this;
    }

    /**
     * @return int
     */
    public function getArticleTypeFilter()
    {
        $types = new ArticlesTypes();
        if (!in_array($this->_articleTypeFilter, $types->getValues())) {
            $this->_articleTypeFilter = ArticlesTypes::ALL;
        }
        return $this->_articleTypeFilter;
    }

    /**
     * @param boolean $ascendingSort
     * @return $this
     */
    public function setAscendingSort($ascendingSort)
    {
        $this->_ascendingSort = (bool) $ascendingSort;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAscendingSort()
    {
        return (bool) $this->_ascendingSort;
    }

    /**
     * @param mixed $internalReferenceFilter
     * @return $this
     */
    public function setInternalReferenceFilter($internalReferenceFilter)
    {
        $this->_internalReferenceFilter = $internalReferenceFilter;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInternalReferenceFilter()
    {
        return $this->_internalReferenceFilter;
    }

    /**
     * @param mixed $lastnameFilter
     * @return $this
     */
    public function setLastnameFilter($lastnameFilter)
    {
        $this->_lastnameFilter = $lastnameFilter;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastnameFilter()
    {
        return $this->_lastnameFilter;
    }

    /**
     * @param mixed $nicknameFilter
     * @return $this
     */
    public function setNicknameFilter($nicknameFilter)
    {
        $this->_nicknameFilter = $nicknameFilter;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNicknameFilter()
    {
        return $this->_nicknameFilter;
    }

    /**
     * @param null $pageNumber
     * @return $this
     */
    public function setPageNumber($pageNumber)
    {
        $this->_pageNumber = $pageNumber;
        return $this;
    }

    /**
     * @return null
     */
    public function getPageNumber()
    {
        return $this->_pageNumber;
    }

    /**
     * @param null $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->_pageSize = (int) $pageSize;
        return $this;
    }

    /**
     * @return null
     */
    public function getPageSize()
    {
        return (int) $this->_pageSize;
    }

    /**
     * @param null $sortBy
     * @return $this
     */
    public function setSortBy($sortBy)
    {
        $this->_sortBy = (int) $sortBy;
        return $this;
    }

    /**
     * @return null
     */
    public function getSortBy()
    {
        return (int) $this->_sortBy;
    }
}