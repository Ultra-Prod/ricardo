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
namespace Diglin\Ricardo\Managers;

use Diglin\Ricardo\Core\Helper;
use Diglin\Ricardo\Managers\Sell\Parameter\CloseArticleParameter;
use Diglin\Ricardo\Managers\Sell\Parameter\DeletePlannedArticleParameter;
use Diglin\Ricardo\Managers\Sell\Parameter\DeletePlannedArticlesParameter;
use Diglin\Ricardo\Managers\Sell\Parameter\GetArticleFeeParameter;
use Diglin\Ricardo\Managers\Sell\Parameter\GetArticlesFeeParameter;
use Diglin\Ricardo\Managers\Sell\Parameter\InsertArticleParameter;
use Diglin\Ricardo\Managers\Sell\Parameter\InsertArticlesParameter;
use Diglin\Ricardo\Managers\Sell\Parameter\UpdateArticleBuyNowQuantityParameter;

class SellTest extends TestAbstract
{
    /**
     * @var Sell
     */
    protected $_sellManager;

    protected function setUp()
    {
        $this->_sellManager = new Sell($this->getServiceManager());
        parent::setUp();
    }

    public function testInsertPlannedArticle()
    {
        $insertArticleParameter = $this->getArticle(true, true, false);

        try {
            $result = $this->_sellManager->createArticle($insertArticleParameter);
        } catch (\Exception $e) {
            $this->getLastApiDebug(true, false, true);
            throw $e;
        }

        $this->assertArrayHasKey('PlannedArticleId', $result, 'Article does not have an article ID');
        $this->assertArrayHasKey('ArticleFee', $result, 'Article does not have any article fee');

        $this->outputContent($result, 'Insert Planned Article: ', true);

        $this->outputContent('Memory Get Max Usage' . memory_get_peak_usage());
    }

    public function testInsertBuyNowArticle()
    {
        $insertArticleParameter = $this->getArticle(false, true, false);

        try {
            $result = $this->_sellManager->createArticle($insertArticleParameter);
        } catch (\Exception $e) {
            $this->getLastApiDebug(true, false, true);
            throw $e;
        }

        $this->assertArrayHasKey('PlannedArticleId', $result, 'Article does not have an article ID');
        $this->assertArrayHasKey('ArticleFee', $result, 'Article does not have any article fee');

        $this->outputContent($result, 'Insert Buy Now Article: ', true);

        return $result;
    }

    /**
     * @depends testInsertBuyNowArticle
     */
    public function testUpdateArticleBuyNowQuantity(array $item)
    {
        $updateParameter = new UpdateArticleBuyNowQuantityParameter();
        $updateParameter
            ->setAntiforgeryToken($this->_serviceManager->getSecurityManager()->getAntiforgeryToken())
            ->setQuantity(123)
            ->setArticleId($item['ArticleId']);

        try {
            $result = $this->_sellManager->updateArticleBuyNowQuantity($updateParameter);
        } catch (\Exception $e) {
            $this->getLastApiDebug(true, false, true);
            throw $e;
        }

        $this->outputContent($result, 'Update Article Buy Now Quantity: ', true);
    }

    /**
     * @depends testInsertPlannedArticle
     */
    public function testDeletePlannedArticle()
    {
        $insertArticleParameter = $this->getArticle(true, true, false);
        $result = $this->_sellManager->createArticle($insertArticleParameter);

        $articleId = $result['PlannedArticleId'];

        $deleteParameter = new DeletePlannedArticleParameter();
        $deleteParameter
            ->setAntiforgeryToken($this->_serviceManager->getSecurityManager()->getAntiforgeryToken())
            ->setPlannedArticleId($articleId);

        $result = $this->_sellManager->deletePlannedArticle($deleteParameter);

        $this->assertArrayHasKey('PlannedArticleId', $result, 'No PlannedArticleId found');
        $this->assertArrayHasKey('IsClosed', $result, 'No IsClosed found');
        $this->assertArrayHasKey('PlannedIndex', $result, 'No PlannedIndex found');
//        $this->assertEquals($articleId, $result['PlannedArticleId'], 'Article ID returned not equal'); //@fixme the Ricardo API doesn't provide any information if an article is deleted or not with this method

        $this->outputContent($result, 'Delete Planned Article: ');
    }

    public function testInsertPlannedArticles()
    {
        $insertArticles = new InsertArticlesParameter();

        try {
            $flush = false;
            for ($j = 0; $j < 2; $j++) { // Insert 5 x 5 articles

                $totalInserted = 5; // to limit memory usage

                for ($i = 0; $i < $totalInserted; $i++) {
                    $insertArticles->setArticles($this->getBaseInsertArticleWithTracking(), $flush);
                    $flush = false;

                    if ($i == ($totalInserted - 1)) {
                        $insertArticles->setAntiforgeryToken($this->_serviceManager->getSecurityManager()->getAntiforgeryToken());
                        $result = $this->_sellManager->createArticles($insertArticles);
                        $flush = true;

                        $this->assertCount($totalInserted, $result, $totalInserted . ' inserted articles were expected.');
                        $this->assertArrayHasKey('PlannedArticleId', $result[0], 'Article does not have an article ID');
                        $this->assertArrayHasKey('ArticleFee', $result[0], 'Article does not have any article fee');

                        $this->outputContent($result, 'Insert Planned Articles: ');
                    }
                }
            }
        } catch (\Exception $e) {
            $this->getLastApiDebug(true, false, true);
            throw $e;
        }

        return array(
            $result[0]['PlannedArticleId'],
            $result[1]['PlannedArticleId']
        );
    }

    /**
     * @depends testInsertPlannedArticles
     */
    public function testDeletePlannedArticles($articles)
    {
        $deleteParameter = new DeletePlannedArticlesParameter();
        $deleteParameter
            ->setAntiforgeryToken($this->_serviceManager->getSecurityManager()->getAntiforgeryToken());

        $article = new DeletePlannedArticleParameter();
        $article->setPlannedArticleId($articles[0]);
        $deleteParameter->setArticles($article);

        $article = new DeletePlannedArticleParameter();
        $article->setPlannedArticleId($articles[1]);
        $deleteParameter->setArticles($article);

        $result = $this->_sellManager->deletePlannedArticles($deleteParameter);

        $this->outputContent($result, 'Delete Planned Articles: ', true);

        $this->assertArrayHasKey('PlannedArticleId', $result[0], 'No PlannedArticleId found');
        $this->assertArrayHasKey('IsClosed', $result[0], 'No IsClosed found');
        $this->assertArrayHasKey('PlannedIndex', $result[0], 'No PlannedIndex found');
        $this->assertEquals($articles[0], $result[0]['PlannedArticleId'], 'Article ID returned not equal');
    }

    public function testCloseArticle()
    {
        $insertedArticle = $this->_sellManager->createArticle($this->getArticle(true, true, true));

        $this->outputContent($insertedArticle, 'Inserted Buy Now Article with start now: ', true);

        $articleId = $insertedArticle['ArticleId'];

        $closeParameter = new CloseArticleParameter();
        $closeParameter
            ->setAntiforgeryToken($this->_serviceManager->getSecurityManager()->getAntiforgeryToken())
            ->setArticleId($articleId);

        $result = $this->_sellManager->closeArticle($closeParameter);

        $this->outputContent($result, 'Close Article: ');

        // ricardo API return some empty values so it means that everything is fine. In case, not, an exception will be thrown
        $this->assertArrayHasKey('ArticleNr', $result, 'No article ID returned');
        $this->assertArrayHasKey('IsClosed', $result, 'Result does not have IsClosed Key');
    }

    public function testGetArticlesFee()
    {
        $articleFeeParameter = new GetArticleFeeParameter();
        $articleFeeParameter
            ->setArticleCondition(1)
            ->setBuyNowPrice(400.00)
            ->setCategoryId(69995)
            ->setExcludeListingFees(true)
            ->setInitialQuantity(4)
            ->setPictureCount(0)
            ->setPromotionIds(array(256))
            ->setStartDate(Helper::getJsonDate(time() + 60*60))
            ->setStartPrice(230);

        $articlesFeeParameter = new GetArticlesFeeParameter();
        $articlesFeeParameter->setArticlesDetails($articleFeeParameter);
        $articlesFeeParameter->setArticlesDetails($articleFeeParameter);

        $articlesFee = $this->_sellManager->getArticlesFee($articlesFeeParameter);
        $this->outputContent($articlesFee, 'Inserted Buy Now Article with start now: ', true);
    }
}
