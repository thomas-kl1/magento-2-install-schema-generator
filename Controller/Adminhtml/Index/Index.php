<?php
/**
 * Blackbird InstallSchemaGenerator Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category        Blackbird
 * @package         Blackbird_InstallSchemaGenerator
 * @copyright       Copyright (c) 2017 Blackbird (http://black.bird.eu)
 * @author          Blackbird Team
 * @license         https://www.store.bird.eu/license/
 */
namespace Blackbird\InstallSchemaGenerator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Index extends Action
{    
    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Blackbird_InstallSchemaGenerator::main_menu')->_addBreadcrumb(__('Install Schema Generator'), __('Install Schema Generator'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Install Schema Generator'));
        $this->_view->renderLayout();
    }
}
