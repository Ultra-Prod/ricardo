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
namespace Diglin\Ricardo\Managers\Sell\Parameter;

use Diglin\Ricardo\Managers\ParameterAbstract;

/**
 * Class CloseArticlesParameter
 * @package Diglin\Ricardo\Managers\Sell\Parameter
 */
class CloseArticlesParameter extends ParameterAbstract
{
    /**
     * @var string
     */
    protected $_antiforgeryToken; // required

    /**
     * @var array
     */
    protected $_articleIds = array(); // required

    /**
     * @var array
     */
    protected $_requiredProperties = array(
        'antiforgeryToken',
        'articleIds',
    );

    /**
     * @param string $antiforgeryToken
     * @return $this
     */
    public function setAntiforgeryToken($antiforgeryToken)
    {
        $this->_antiforgeryToken = $antiforgeryToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getAntiforgeryToken()
    {
        return $this->_antiforgeryToken;
    }

    /**
     * @param CloseArticleParameter $articleIds
     * @param bool $clear
     * @return $this
     */
    public function setArticleIds(CloseArticleParameter $articleIds = null, $clear = false)
    {
        if ($clear) {
            $this->_articleIds = array();
        }

        if (is_null($articleIds)) {
            return $this;
        }

        $this->_articleIds[] = $articleIds;
        return $this;
    }

    /**
     * @return array
     */
    public function getArticleIds()
    {
        return $this->_articleIds;
    }
}
