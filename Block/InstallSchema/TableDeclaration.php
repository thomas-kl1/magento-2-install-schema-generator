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
namespace Blackbird\InstallSchemaGenerator\Block\InstallSchema;

use Magento\Framework\View\Element\Template;
use Blackbird\InstallSchemaGenerator\Block\InstallSchema\TableDeclaration\ColumnDeclaration;
use Blackbird\InstallSchemaGenerator\Block\InstallSchema\TableDeclaration\IndexDeclaration;
use Blackbird\InstallSchemaGenerator\Block\InstallSchema\TableDeclaration\ForeignKeyDeclaration;

/**
 * Layout block of the table declaration
 */
class TableDeclaration extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_InstallSchemaGenerator::InstallSchema/table-declaration.phtml';
    
    /**
     * Retrieve the column definition
     * 
     * @param array $column
     * @return string
     */
    public function getColumnDeclarationHtml(array $column)
    {
        return $this->getLayout()
            ->createBlock(ColumnDeclaration::class)
            ->setColumnData($column)
            ->toHtml();
    }
    
    /**
     * Retrieve the index definition
     * 
     * @param array $column
     * @return string
     */
    public function getIndexDeclarationHtml(array $column)
    {
        return $this->getLayout()
            ->createBlock(IndexDeclaration::class)
                //todo
            ->toHtml();
    }
    
    /**
     * Retrieve the foreign key definition
     * 
     * @param array $column
     * @return string
     */
    public function getForeignKeyDeclarationHtml(array $column)
    {
        return $this->getLayout()
            ->createBlock(ForeignKeyDeclaration::class)
            ->setTableName($column['TABLE_NAME'])
            ->setColumnName($column['COLUMN_NAME'])
            ->setConstraintData($column['CONSTRAINTS'])
            ->toHtml();
    }
    
    /**
     * Retrieve the table comment
     * 
     * @return string
     */
    public function getComment()
    {
        $column = $this->getColumn();
        $comment = $column['TABLE_COMMENT'];
        
        if (empty($comment)) {
            $comment = $column['TABLE_NAME'];
        }
        
        return $comment;
    }
}
