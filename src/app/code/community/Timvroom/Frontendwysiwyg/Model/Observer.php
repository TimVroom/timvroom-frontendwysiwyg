<?php
/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Frontendwysiwyg_Model_Observer
{
    static $_helper;

    public function __construct(){
        self::$_helper = Mage::helper('timvroom_frontendwysiwyg');
    }
    public function prepareWysiwyg($observer)
    {
        if ($admin = self::$_helper->switchAdminSession()) {
            self::$_helper->restoreFrontendSession();
            if ($admin->isLoggedIn() && $admin->isAllowed('cms/page/save')){
                $action = $observer->getEvent()->getControllerAction();
                $action->getLayout()->getUpdate()->addHandle('cms_page_admin_edit');
            }
        }

    }

}