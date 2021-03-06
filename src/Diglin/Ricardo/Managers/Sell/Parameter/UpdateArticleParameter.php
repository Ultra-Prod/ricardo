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
namespace Diglin\Ricardo\Managers\Sell\Parameter;

use Diglin\Ricardo\Managers\ParameterAbstract;

/**
 * Class UpdateArticleParameter
 * @package Diglin\Ricardo\Managers\Sell\Parameter
 */
class UpdateArticleParameter extends ParameterAbstract
{
    /**
     * @var string
     */
    protected $_antiforgeryToken; // required

    /**
     * @var ArticleInformationParameter
     */
    protected $_articleInformation; // required

    /**
     * @var array
     */
    protected $_descriptions = array(); // required

    /**
     * @var null
     */
    protected $_brandingArticleDetails = null; // optional

    /**
     * @var int
     */
    protected $_articleId;

    /**
     * @var array
     */
    protected $_requiredProperties = array(
        'antiforgeryToken',
        'articleInformation',
        'descriptions',
        'articleId'
    );

    protected $_optionalProperties = array(
        'brandingArticleDetails'
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
     * @param int $articleId
     * @return $this
     */
    public function setArticleId($articleId)
    {
        $this->_articleId = $articleId;
        return $this;
    }

    /**
     * @return int
     */
    public function getArticleId()
    {
        return $this->_articleId;
    }

    /**
     * @param \Diglin\Ricardo\Managers\Sell\Parameter\ArticleInformationParameter $articleInformation
     * @return $this
     */
    public function setArticleInformation(ArticleInformationParameter $articleInformation)
    {
        $this->_articleInformation = $articleInformation;
        return $this;
    }

    /**
     * @return \Diglin\Ricardo\Managers\Sell\Parameter\ArticleInformationParameter
     */
    public function getArticleInformation()
    {
        return $this->_articleInformation;
    }

    /**
     * @param null $brandingArticleDetails
     * @return $this
     */
    public function setBrandingArticleDetails($brandingArticleDetails)
    {
        $this->_brandingArticleDetails = $brandingArticleDetails;
        return $this;
    }

    /**
     * @return null
     */
    public function getBrandingArticleDetails()
    {
        return $this->_brandingArticleDetails;
    }

    /**
     * @param \Diglin\Ricardo\Managers\Sell\Parameter\ArticleDescriptionParameter $descriptions
     * @param bool $clear
     * @return $this
     */
    public function setDescriptions(ArticleDescriptionParameter $descriptions, $clear = false)
    {
        if ($clear) {
            $this->_descriptions = array();
        }

        $this->_descriptions[] = $descriptions;
        return $this;
    }

    /**
     * @return array of \Diglin\Ricardo\Managers\Sell\Parameter\ArticleDescriptionParameter
     */
    public function getDescriptions()
    {
        return $this->_descriptions;
    }
}
