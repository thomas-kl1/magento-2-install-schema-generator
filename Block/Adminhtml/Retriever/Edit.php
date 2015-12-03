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
namespace Blackbird\InstallSchemaGenerator\Block\Adminhtml\Retriever;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Retrieve InstallSchema class from tables form block
 */
class Edit extends Container
{
    /**
     * Initialize form
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Blackbird_InstallSchemaGenerator';
        $this->_controller = 'adminhtml_retriever';

        parent::_construct();
        
        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('back');
        $this->buttonList->add(
            'retrieve',
            [
                'class' => 'retrieve primary',
                'label' => __('Retrieve Install Schema'),
                'data_attribute' => [
                    'mage_init' => [
                        'button' => [
                            'event' => 'retrieve',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ]
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
