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
 * @copyright       Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author          Blackbird Team
 * @license         MIT LICENSE
 */
namespace Blackbird\InstallSchemaGenerator\Block\Adminhtml\Retriever\Edit\Tab;

use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Blackbird\InstallSchemaGenerator\Model\DB\SchemaRetriever;

/**
 * Retrieve InstallSchema class from tables form block
 */
class Tables extends Generic implements TabInterface
{
    /**
     * @var SchemaRetriever
     */
    private $schemaSource;
    
    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param SchemaRetriever $schemaRetriever
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        SchemaRetriever $schemaRetriever,
        array $data = []
    ) {
        $this->schemaSource = $schemaRetriever;
        parent::__construct($context, $registry, $formFactory, $data);
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
     * {@inheritdoc}
     */
    public function _prepareForm()
    {
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
                'values' => $this->schemaSource->getTablesOptions()
            ]
        );
        
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
