<?php
/**
 * Blackbird Install Schema Generator Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package		Blackbird_InstallSchemaGenerator
 * @copyright           Copyright (c) 2015 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Blackbird\InstallSchemaGenerator\Block\Adminhtml\Retriever\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Retrieve InstallSchema class from tables form block
 */
class Tables extends Generic implements TabInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface 
     */
    protected $_objectManager;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_objectManager = $objectManager;
    }
    
    public function getTabLabel()
    {
        return __('Tables');
    }
    
    public function getTabTitle()
    {
        return __('Tables');
    }
    
    public function canShowTab() 
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }
    
    public function _prepareForm()
    {
        $retriever = $this->_objectManager->create('\Blackbird\InstallSchemaGenerator\Model\ResourceModel\SchemaRetriever');
        
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('isg_');
        
        $fieldset = $form->addFieldset(
            'tables_fieldset',
            ['legend' => __('Retreive Install Schema from table(s)')]
        );
        
        $fieldset->addField(
            'tables',
            'multiselect',
            [
                'name' => 'tables',
                'label' => __('Tables'),
                'title' => __('Tables'),
                'note' => __('Select table(s) to retrieves the schema and make the install setup class.'),
                'required' => true,
                'values' => $retriever->getTablesOptions()
            ]
        );
        
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
