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
 * Layout block of the foreign key declaration
 */
class ForeignKey extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_InstallSchemaGenerator::install-schema/table-declaration/foreign-key-declaration.phtml';
        
    /**
     * Check if it's a foreign key or not
     * 
     * @return bool
     */
    public function isForeignKey()
    {        
        return (!empty($this->getDeleteRule()) && 
                $this->getFkName() && 
                $this->getRefFkTableName() && 
                $this->getRefFkColumnName());
    }
    
    /**
     * Retrieve the constraint delete rule
     * 
     * @return string
     */
    public function getDeleteRule()
    {
        $constraint = $this->getConstraintData();
        
        return str_replace(' ', '_', strtoupper($constraint['DELETE_RULE']));
    }
    
    /**
     * Retrieve the foreign key name
     * 
     * @return string
     */     
    public function getFkName()
    {
        $constraint = $this->getConstraintData();
        
        return $constraint['CONSTRAINT_NAME'];
    }
    
    /**
     * Retrieve the foreign key table name
     * 
     * @return string
     */
    public function getFkTableName()
    {
        return $this->getTableName();
    }
    
    /**
     * Retrieve the foreign key column name
     * 
     * @return string
     */
    public function getFkColumnName()
    {
        return $this->getColumnName();
    }
    
    /**
     * Retrieve the referenced table name 
     * 
     * @return string
     */
    public function getRefFkTableName()
    {
        $constraint = $this->getConstraintData();
        
        return $constraint['REFERENCED_TABLE_NAME'];
    }
    
    /**
     * Retrieve the referenced column name
     * 
     * @return string
     */
    public function getRefFkColumnName()
    {
        $constraint = $this->getConstraintData();
        
        return $constraint['REFERENCED_COLUMN_NAME'];
    }
}
