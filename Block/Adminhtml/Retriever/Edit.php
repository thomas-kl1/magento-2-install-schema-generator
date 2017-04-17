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
 * @copyright       Copyright (c) 2017 Blackbird (https://black.bird.eu)
 * @author          Blackbird Team
 * @license         https://www.store.bird.eu/license/
 */
namespace Blackbird\InstallSchemaGenerator\Block\Adminhtml\Retriever;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Retrieve InstallSchema class from tables form block
 */
class Edit extends Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Blackbird_InstallSchemaGenerator';
        $this->_controller = 'adminhtml_retriever';

        parent::_construct();
        
        $this->removeButton('save');
        $this->removeButton('reset');
        $this->removeButton('back');
        $this->addButton(
            'retrieve',
            [
                'label' => __('Generate and download file'),
                'class' => 'retrieve primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
                ]
            ],
            1
        );
    }
    
    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Retrieve Install Schema');
    }
}
