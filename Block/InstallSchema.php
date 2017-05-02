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
namespace Blackbird\InstallSchemaGenerator\Block;

use Magento\Framework\View\Element\Template;
use Blackbird\InstallSchemaGenerator\Block\InstallSchema\TableDeclaration;

/**
 * Layout block of the InstallSchema class
 */
class InstallSchema extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_InstallSchemaGenerator::install-schema.phtml';
    
    /**
     * Retrieve the template block html
     * 
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }

    /**
     * Retrieve the table definition
     * 
     * @param array $tableName
     * @param array $columns
     * @return string
     */
    public function getTableDeclarationHtml($tableName, array $columns)
    {
        return $this->getLayout()
            ->createBlock(TableDeclaration::class)
            ->setTableName($tableName)
            ->setColumns($columns)
            ->toHtml();
    }

    public function toHtml()
    {
        return parent::toHtml();
    }
}
