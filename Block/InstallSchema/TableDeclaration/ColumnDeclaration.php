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
 * Layout block of the column declaration
 */
class ColumnDeclaration extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_InstallSchemaGenerator::InstallSchema/TableDeclaration/column-declaration.phtml';
    
    /**
     * Retrieve the column name
     * 
     * @return string
     */
    public function getColumnName()
    {
        $column = $this->getColumnData();
        
        return $column['COLUMN_NAME'];
    }
    
    /**
     * Retrieve the column type
     * 
     * @todo refactor
     * @return string
     */
    public function getColumnType()
    {
        $typesize = $this->getTypeAndSize();
        
        return $typesize['type'];
    }
    
    /**
     * Retrieve the column size
     * 
     * @todo refactor
     * @return string
     */
    public function getColumnSize()
    {
        $typesize = $this->getTypeAndSize();
        
        return $typesize['size'];
    }
    
    /**
     * Retrieve the column comment
     * 
     * @return string
     */
    public function getColumnComment()
    {
        $column = $this->getColumnData();
        
        return $column['COLUMN_COMMENT'] ?: 'null';
    }
    
    /**
     * Retrieve the column precision
     * 
     * @return string
     */
    public function getColumnPrecision()
    {
        $column = $this->getColumnData();
        $precision = null;
        
        if (!is_null($column['NUMERIC_PRECISION'])) {
            $precision = $column['NUMERIC_PRECISION'];
        } elseif (!is_null($column['DATETIME_PRECISION'])) {
            $precision = $column['DATETIME_PRECISION'];
        }
        
        return $precision;
    }
    
    /**
     * Retrieve the column unsigned
     * 
     * @todo refactor
     * @return string
     */
    public function getColumnUnsigned()
    {
        $typesize = $this->getTypeAndSize();
        
        return $typesize['unsigned'];
    }
    
    /**
     * Retrieve the column options
     * 
     * @return array
     */
    public function getOptions()
    {
        $column  = $this->getColumnData();
        $options = [];
        
        if (!empty($column['COLUMN_DEFAULT']) || $column['COLUMN_DEFAULT'] == '0') {
            $options['default'] = $column['COLUMN_DEFAULT'];
        }
        if ($column['IS_NULLABLE'] === 'NO') {
            $options['nullable'] = 'false';
        } else {
            $options['nullable'] = 'true';
        }
        if (!empty($column['NUMERIC_SCALE'])) {
            $options['scale'] = $column['NUMERIC_SCALE'];
        }
        if (!empty($this->getColumnPrecision())) {
            $options['precision'] = $this->getColumnPrecision();
        }
        if ($this->getColumnUnsigned()) {
            $options['unsigned'] = 'true';
        }
        if (!empty($column['EXTRA'])) {
            if (in_array($column['EXTRA'], ['auto_increment', 'on update CURRENT_TIMESTAMP'])) {
                $options[$column['EXTRA']] = 'true';
            }
        }
        if (!empty($column['COLUMN_KEY'])) {
            // PRI, (UNI, MUL are processed in addIndex method in Magento 2)
            if ($column['COLUMN_KEY'] === 'PRI') {
                $options['primary'] = 'true';
            }
        }
        
        return $options;
    }
    
    /**
     * Return the type and the size of the field
     *
     * @param string $type
     * @return array
     */
    private function getTypeAndSize()
    {
        $column = $this->getColumnData();
        $type = $column['COLUMN_TYPE'];
        $unsigned = stripos($type, 'unsigned') === false ? '' : 'true';
        
        $matches = [];
        preg_match('#(.*)[(](.+)[)].*#', $type, $matches);
        
        $match = (!empty($matches[1])) ? strtolower($matches[1]) : $type;
        $size = (!empty($matches[2])) ? $matches[2] : 'null';
        
        // formalize type for magento 2
        $type = $this->getRealType($match);
        $type = $type['type'];
        $size = (isset($type['size'])) ? $type['size'] : $size;
                
        return ['type' => $type, 'size' => $size, 'unsigned' => $unsigned];
    }
    
    /**
     * Returns the type and size by mysql type
     * 
     * @return array
     */
    private function getRealType($type)
    {
        $result = ['type' => strtoupper($type)];
        
        $types = [
            'char' => [
                'type' => 'TEXT',
            ],
            'varchar' => [
                'type' => 'TEXT',
            ],
            'text' => [
                'type' => 'TEXT',
                'size' => '16000',
            ],
            'tinytext' => [
                'type' => 'TEXT',
                'size' => '255',
            ],
            'mediumtext' => [
                'type' => 'TEXT',
                'size' => '16000000',
            ],
            'longtext' => [
                'type' => 'TEXT',
                'size' => '16000000000',
            ],
            'int' => [
                'type' => 'INTEGER',
            ],
            'tinyint' => [
                'type' => 'SMALLINT',
            ],                
            'mediumint' => [
                'type' => 'INTEGER',
            ],
            'double' => [
                'type' => 'FLOAT',
            ],
            'real' => [
                'type' => 'FLOAT',
            ],
            'time' => [
                'type' => 'TIMESTAMP',
            ],
            'tinyblob' => [
                'type' => 'BLOB',
            ],
            'mediumblob' => [
                'type' => 'BLOB',
            ],
            'longblob' => [
                'type' => 'BLOB',
            ],
            'binary' => [
                'type' => 'VARBINARY',
            ]
        ];
            
        if (isset($types[$type])) {
            $result = $types[$type];
        }
        
        return $result;
    }
}
