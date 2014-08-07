<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author Sylvain Rayé <sylvain.raye at diglin.com>
 * @category    Ricento
 * @package     Diglin_Ricardo
 * @copyright   Copyright (c) 2011-2014 Diglin (http://www.diglin.com)
 */
use Diglin\Ricardo\Services\ServiceAbstract;

class Diglin_Ricento_Adminhtml_ApiController extends Mage_Adminhtml_Controller_Action
{
    public function confirmationAction()
    {
        $success = (int) $this->getRequest()->getParam('success', 0);

        if ($success) {
            $temporaryToken = $this->getRequest()->getParam('temporarytoken');
            $websiteId = (int) $this->getRequest()->getParam('website');

            if (!is_numeric($websiteId) || is_null($websiteId)) {
                $this->_getSession()->addError($this->__('The website code returned from Ricardo is not correct! Your authorization has not been saved on our side.'));
                $this->_redirect('adminhtml');
                return;
            }

            $securityService = Mage::getSingleton('diglin_ricento/api_services_security');
            $securityServiceModel = $securityService->getServiceModel($websiteId);

            try {
                // Save the temporary token
                $apiToken = Mage::getModel('diglin_ricento/api_token')->loadByWebsiteAndTokenType(ServiceAbstract::TOKEN_TYPE_TEMPORARY, $websiteId);
                $apiToken
                    ->setWebsiteId($websiteId)
                    ->setToken($temporaryToken)
                    ->setTokenType(ServiceAbstract::TOKEN_TYPE_TEMPORARY)
                    ->save();

                $expirationDate = new DateTime($apiToken->getExpirationDate());

                // Initialize the Security Model with the required variable
                $securityServiceModel
                    ->setTemporaryTokenExpirationDate($expirationDate->getTimestamp())
                    ->setTemporaryToken($temporaryToken);

                // Save the credential token for future use
                $apiToken = Mage::getModel('diglin_ricento/api_token');
                $apiToken
                    ->setWebsiteId($websiteId)
                    ->setToken($securityServiceModel->getTokenCredential())
                    ->setTokenType(ServiceAbstract::TOKEN_TYPE_IDENTIFIED)
                    ->setExpirationDate($securityServiceModel->getTokenCredentialExpirationDate())
                    ->setSessionDuration($securityServiceModel->getTokenCredentialSessionDuration())
                    ->setSessionExpirationDate(
                        Mage::helper('diglin_ricento/api')->calculateSessionExpirationDate($securityServiceModel->getTokenCredentialSessionDuration(), $securityServiceModel->getTokenCredentialSessionStart()))
                    ->save();

            } catch (Exception $e) {
                Mage::logException($e);
                Mage::log($securityService->getLastApiDebug($websiteId), Zend_Log::DEBUG);
                $this->_getSession()->addError($this->__('An error occurred while saving the token. Please, check your exception log.'));
            }
        } else {
            $this->_getSession()->addError($this->__('Authorization was not successful on Ricardo side. Please, contact Ricardo to find out the reason.'));
        }
        $this->_redirect('adminhtml');
    }
}