<?php
/**
 * NicheCommerce
 * 
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */

class Timvroom_Frontendwysiwyg_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ACTIVE = '';

    protected $switchSessionName  = 'adminhtml';
    protected $_isAdminActive = false;
    protected $currentSessionId;
    protected $currentSessionName;

    public function isActive()
    {
        return Mage::getStoreConfig(self::XML_PATH_ACTIVE);
    }

    public function switchAdminSession()
    {
        $this->currentSessionId   = Mage::getSingleton('core/session')->getSessionId();
        $this->currentSessionName = Mage::getSingleton('core/session')->getSessionName();
        if ($this->currentSessionId && $this->currentSessionName && isset($_COOKIE[$this->currentSessionName])) {
            $switchSessionId = $_COOKIE[$this->switchSessionName];
            $this->_switchSession($this->switchSessionName, $switchSessionId);
            $this->_isAdminActive = true;
            return Mage::getSingleton('admin/session');
        }
        return false;
    }

    public function restoreFrontendSession()
    {
        if ($this->currentSessionId && $this->currentSessionName) {
            $this->_switchSession($this->currentSessionName, $this->currentSessionId);
            $this->_isAdminActive = false;
        }
    }

    protected function _switchSession($namespace, $id = null)
    {
        session_write_close();
        $GLOBALS['_SESSION'] = null;
        $session             = Mage::getSingleton('core/session');
        if ($id) {
            $session->setSessionId($id);
        }
        $session->start($namespace);
    }

    public function isAdminActive()
    {
        return $this->_isAdminActive;
    }

    public function cleanupContent($content)
    {
        $content_processed = preg_replace_callback(
            '#{{(.+?)}}#s', function($matches){
                return "{{".str_replace('"',"'", $matches[1])."}}";
            },
            $content
        );
        return $content_processed;
    }
}