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
namespace Blackbird\InstallSchemaGenerator\Block\InstallSchema\TableDeclaration;

use Magento\Framework\View\Element\Template;

/**
 * Layout block of the index declaration
 */
class IndexDeclaration extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_InstallSchemaGenerator::InstallSchema/TableDeclaration/index-declaration.phtml';
}
