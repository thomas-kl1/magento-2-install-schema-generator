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
namespace Blackbird\InstallSchemaGenerator\Block\Adminhtml;

use Magento\Framework\View\Element\Template;

/**
 * Retrieve InstallSchema class from tables form block
 */
class RetrieverForm extends Template
{
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context, array $data = [], \Magento\Framework\ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $data);
        $this->_objectManager = $objectManager;
        $this->_isScopePrivate = true;
    }
    
    public function getTables()
    {
        $retriever = $this->_objectManager->create('\Blackbird\InstallSchemaGenerator\Model\Resource\SchemaRetriever');
        return $retriever->getTables();
    }
}
