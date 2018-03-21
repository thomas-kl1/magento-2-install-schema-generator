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
    protected $_template = 'Blackbird_InstallSchemaGenerator::install-schema/table-declaration/index-declaration.phtml';

    /**
     * Retrieve the index type to html
     * 
     * @param string $indexType
     * @return string
     */
    public function getIndexTypeArrayFormat($indexType)
    {
        return (!empty($indexType)) ? '[\'type\' => ' . $indexType . ']' : '[]';
    }

    /**
     * Retrieve the table indexes
     * 
     * @return array
     */
    public function getIndexes()
    {
        if (!$this->hasData('indexes')) {
            $indexes = [];
        
            // Sort columns which are part of the same index
            foreach ($this->getColumns() as $column) {
                foreach ($column['CONSTRAINTS'] as $constraint) {
                    $indexName = $constraint['INDEX_NAME'];
                    
                    // We only add the index other than the primary or foreign keys
                    if ($this->isIndex($constraint)) {
                        // Init data of the index
                        if (!isset($indexes[$indexName])) {
                            $indexes[$indexName] = [
                                'type' => $this->getIndexType($constraint),
                                'columns' => []
                            ];
                        }
                       
                        $indexes[$indexName]['columns'][] = $column['COLUMN_NAME'];
                    }
                }
            }
            
            $this->setData('indexes', $indexes);
        }
        
        return $this->getData('indexes');
    }
    
    /**
     * Retrieve the index type from a constraint
     * 
     * @param array $constraint
     * @return string
     */
    private function getIndexType(array $constraint)
    {
        $indexType = $constraint['CONSTRAINT_TYPE'];
        
        // Is unique, fulltext or simple index
        if (empty($indexType)) {
            if ($constraint['INDEX_TYPE'] === 'FULLTEXT') {
                $indexType = $constraint['INDEX_TYPE'];
            } else {
                $indexType = '';
            }
        }
        
        if (!empty($indexType)) {
            $indexType = 'AdapterInterface::INDEX_TYPE_' . $indexType;
        }
        
        return $indexType;
    }
    
    /**
     * Check if a given constraint is an index
     * 
     * @param array $constraint
     * @return bool
     */
    private function isIndex(array $constraint)
    {
        return (
            !empty($constraint['INDEX_TYPE']) &&
            !empty($constraint['INDEX_NAME']) &&
            empty($constraint['REFERENCED_TABLE_NAME']) && 
            empty($constraint['REFERENCED_COLUMN_NAME']) &&
            $constraint['INDEX_NAME'] !== 'PRIMARY' &&
            !in_array(
                $constraint['CONSTRAINT_TYPE'],
                ['PRIMARY KEY', 'FOREIGN KEY'],
                true
            )
        );
    }
}
