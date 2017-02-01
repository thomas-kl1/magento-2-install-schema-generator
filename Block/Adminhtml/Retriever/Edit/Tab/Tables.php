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
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_objectManager = $objectManager;
    }
    
    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Tables');
    }
    
    /**
     * @return string
     */
    public function getTabTitle()
    {
        return __('Tables');
    }
    
    /**
     * @return boolean
     */
    public function canShowTab() 
    {
        return true;
    }
    
    /**
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
    
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    public function _prepareForm()
    {
        $retriever = $this->_objectManager->create('\Blackbird\InstallSchemaGenerator\Model\ResourceModel\SchemaRetriever');
        
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('isg_');
        
        $fieldset = $form->addFieldset(
            'tables_fieldset',
            ['legend' => __('Generate InstallSchema.php file from table(s)')]
        );
        
        $fieldset->addField(
            'tables',
            'multiselect',
            [
                'name' => 'tables',
                'label' => __('Tables'),
                'title' => __('Tables'),
                'note' => __('Select the table(s) to generate the InstallSchema.php file.'),
                'required' => true,
                'values' => $retriever->getTablesOptions()
            ]
        );
        
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
