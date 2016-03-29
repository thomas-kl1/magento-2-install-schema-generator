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
class Infos extends Generic implements TabInterface
{
    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Namespace Module');
    }
    
    /**
     * @return string
     */
    public function getTabTitle()
    {
        return __('Namespace Module');
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
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('isg_');
        
        $fieldset = $form->addFieldset(
            'infos_fieldset',
            ['legend' => __('Informations about your namespace\'s module')]
        );
        
        $fieldset->addField(
            'vendor',
            'text',
            [
                'name' => 'vendor',
                'label' => __("Vendor's Name"),
                'title' => __("Vendor's Name"),
                'note' => __("Vendor's name is generally the name of the company and is the first element of your namespace: &lt;Vendor&gt;\&ltModule&gt;")
            ]
        );
        
        $fieldset->addField(
            'module',
            'text',
            [
                'name' => 'module',
                'label' => __('Module Name'),
                'title' => __('Module Name'),
                'note' => __("It's the name of your module : &lt;Vendor&gt;\&ltModule&gt;")
            ]
        );
        
        $data = [
            'vendor' => 'Vendor',
            'module' => 'Area'
        ];
        
        $this->setValues($data);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
