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
 * Class InsertArticleParameter
 * @package Diglin\Ricardo\Managers\Sell
 */
class InsertArticleParameter extends ParameterAbstract
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
    protected $_carDealerArticleId; // optional

    /**
     * @var boolean
     */
    protected $_isUpdateArticle; // Set to true if the preview is created for an update of live article without bid

    /**
     * @var array
     */
    protected $_pictures = array(); // required

    protected $_requiredProperties = array(
        'antiforgeryToken',
        'articleInformation',
        'descriptions',
        'pictures',
    );

    protected $_optionalProperties = array(
        'brandingArticleDetails',
        'isUpdateArticle',
        'carDealerArticleId'
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
     * @param int $carDealerArticleId
     * @return $this
     */
    public function setCarDealerArticleId($carDealerArticleId)
    {
        $this->_carDealerArticleId = $carDealerArticleId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCarDealerArticleId()
    {
        return $this->_carDealerArticleId;
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

    /**
     * @param boolean $isUpdateArticle
     * @return $this
     */
    public function setIsUpdateArticle($isUpdateArticle)
    {
        $this->_isUpdateArticle = $isUpdateArticle;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsUpdateArticle()
    {
        return $this->_isUpdateArticle;
    }

    /**
     * @param \Diglin\Ricardo\Managers\Sell\Parameter\ArticlePictureParameter $pictures
     * @param bool $clear
     * @return $this
     */
    public function setPictures(ArticlePictureParameter $pictures = null, $clear = false)
    {
        if ($clear) {
            $this->_pictures = array();
        }

        if (is_null($pictures)) {
            return $this;
        }

        $this->_pictures[] = $pictures;
        return $this;
    }

    /**
     * @return array of \Diglin\Ricardo\Managers\Sell\Parameter\ArticlePictureParameter
     */
    public function getPictures()
    {
        return $this->_pictures;
    }
}
