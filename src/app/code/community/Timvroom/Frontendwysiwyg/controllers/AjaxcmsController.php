<?php
/**
 * NicheCommerce
 * 
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Frontendwysiwyg_AjaxcmsController extends Mage_Core_Controller_Front_Action
{
    private $_helper;

    public function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('timvroom_frontendwysiwyg');
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_helper = Mage::helper('timvroom_frontendwysiwyg');
        if ($admin = $this->_helper->switchAdminSession()) {
            $this->_helper->restoreFrontendSession();
            if ($admin->isLoggedIn() && $admin->isAllowed('cms/page/save')){
                return true;
            }
        }
        // this is not allow so stop working!
        $this->getRequest()->setDispatched(true);
    }

    protected function loadModel() {
        $url = $this->getRequest()->getParam('url');
        if ($url === '/') {
            $url = Mage::getStoreConfig('web/default/cms_home_page');
        }
        $url = trim($url, '/');

        $model = Mage::getModel('cms/page')->load($url, 'identifier');
        return $model;
    }

    public function getAction()
    {
        $model = $this->loadModel();
        $editable = ($this->getRequest()->getPost('editable', false) === 'true');

        if ($editable) {
            $content = $model->getContent();
            echo $this->_helper->cleanupContent($content);
        } else {
            $helper = Mage::helper('cms');
            $processor = $helper->getPageTemplateProcessor();
            $html = $processor->filter($model->getContent());
            echo $html;
        }
    }

    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            //init model and set data
            $model = $this->loadModel();

            if (!$model->getId()) {
                echo json_encode('Failed to save');
                return;
            }
            $model->setContent($data['content']);

            Mage::dispatchEvent('cms_page_prepare_save', array('page' => $model, 'request' => $this->getRequest()));
            // try to save it
            try {
                // save the data
                $model->save();
                echo 'Page saved';
            } catch (Mage_Core_Exception $e) {
                echo $e->getMessage();
                return;
            }
        }
    }

    /**
     * @todo implement get widget logic
     */
    public function getWidget()
    {

    }
}