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
 * @copyright       Copyright (c) 2017 Blackbird (http://black.bird.eu)
 * @author          Blackbird Team
 * @license         https://www.store.bird.eu/license/
 */
namespace Blackbird\InstallSchemaGenerator\Model;

/**
 * Schema builder for setup schema installation
 */
class SchemaSetupBuilder
{
    /**
     * @var array
     */
    private $bool = ['YES' => 'true', 'NO' => 'false'];
    
    /**
     * Setup the InstallSchema php class for the given database schema
     * 
     * @param array $schema
     * @param string $namespace
     * @return string
     */
    public function getSetupBySchema(array $schema, $namespace = '')
    {
        if (!$this->isNamespace($namespace)) {
            $namespace = 'Vendor\Area';
        }
        $installSchema = $this->getHeader($namespace);
        
        foreach ($schema as $name => $table) {
            $installSchema .= $this->getNewTable($name, $table);
        }
        
        $installSchema .= $this->getFooter();
        
        return $installSchema;
    }
    
    /**
     * If pattern is a part of namespace, then return it, else return false
     * 
     * @param string $namespace
     * @return boolean|string
     */
    private function isNamespace($namespace)
    {
        $return = (!empty($namespace));
        
        if ($return) {
            $return = (count(explode("\\", $namespace)) === 2);
        }
        
        return $return;
    }
    
    /**
     * Return options 
     *
     * @param array $options
     * @return string
     */
    private function getOptions($options)
    {
        $ln = '';
        $nullable = $this->bool[$options['null']];
        
        $return = "\t\t\t\t[";
        
        if (!empty($options['default']) || $options['default'] === '0') {
            $return .= PHP_EOL . "\t\t\t\t\t'default' => '" . $options['default'] ."',";
            $ln = PHP_EOL . "\t\t\t\t";
        }
        if ($nullable === 'false') {
            $return .=  PHP_EOL . "\t\t\t\t\t'nullable' => false,";
            $ln = PHP_EOL . "\t\t\t\t";
        }
        if (!empty($options['scale'])) {
            $return .=  PHP_EOL . "\t\t\t\t\t'scale' => '" . $options['scale'] . "',";
            $ln = PHP_EOL . "\t\t\t\t";
        }
        if (!empty($options['precision'])) {
            $return .=  PHP_EOL . "\t\t\t\t\t'precision' => '" . $options['precision'] . "',";
            $ln = PHP_EOL . "\t\t\t\t";
        }
        if ($options['unsigned']) {
            $return .= PHP_EOL . "\t\t\t\t\t'unsigned' => true,";
            $ln = PHP_EOL . "\t\t\t\t";
        }
        if (!empty($options['extra'])) {
            $return .= PHP_EOL . "\t\t\t\t\t'" . $options['extra'] . "' => true,";
            $ln = PHP_EOL . "\t\t\t\t";
        }
        if (!empty($options['key'])) {
            $return .=  PHP_EOL . "\t\t\t\t\t'" . $options['key'] . "' => true,";
            $ln = PHP_EOL . "\t\t\t\t";
        }
        
        $return .= $ln . "]";
        
        return $return;
    }
    
    /**
     * Return the extra
     *
     * @param string $extra
     * @return string
     */
    private function getExtra($extra)
    {        
        // auto_increment, on update CURRENT_TIMESTAMP, (todo: virtual generated)
        if ($extra !== 'auto_increment') {
            if ($extra !== 'on update CURRENT_TIMESTAMP') {
                $extra = null;
            }
        }
        
        return $extra;
    }
    
    /**
     * Return the corresponding key
     *
     * @param string $key
     * @return string
     */
    private function getKey($key)
    {
        $key = null;
        
        // PRI, UNI, MUL
        if ($key === 'PRI') {
            $key = 'primary';
        }
        
        return $key;
    }
    
    /**
     * Return the type and the size of the field
     *
     * @param string $type
     * @return array
     */
    private function getTypeAndSize($type)
    {
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
    
    /**
     * Create the Add Index part
     *
     * @param array $columns
     * @return string
     */
    private function getAddIndexes($columns)
    {
        $return = '';
        $indexes = [];
        
        // Sort columns which are part of the same index
        foreach ($columns as $column) {
            foreach ($column['CONSTRAINTS'] as $constraint) {
                $idxname = $constraint['INDEX_NAME'];
                $constType = $constraint['CONSTRAINT_TYPE'];
                $indexType = $constraint['INDEX_TYPE'];
                // Primary and foreign are index, we only search unique, fulltext and simple index
                $isIndex = ($constType !== 'PRIMARY KEY' && $constType !== 'FOREIGN KEY' && $idxname !== 'PRIMARY' && !empty($indexType));
                
                // If it's an index
                if (!empty($idxname) && empty($constraint['REFERENCED_TABLE_NAME']) && empty($constraint['REFERENCED_COLUMN_NAME']) && $isIndex) {
                    
                    if (!isset($indexes[$idxname])) {
                        $indexes[$idxname] = [];
                    }
                    
                    // Is fulltext, else is simple index (== none by default)
                    $indexType = ($indexType === 'FULLTEXT') ? $indexType : '';
                    // Is unique, else is fulltext
                    $type = (!empty($constType)) ? $constType : $indexType ;
                    
                    $indexes[$idxname][] = [
                        'table' => $column['TABLE_NAME'],
                        'column' => $column['COLUMN_NAME'],
                        'type' => $type
                    ];
                }
            }
        }

        // Create the Add index parts
        foreach ($indexes as $idxname=>$index) {
            $tabName = '';
            $tabColumns = "[";
            foreach ($index as $col) {
                $tabColumns .= "'" . $col['column'] . "', ";
                $tabName = $col['table'];
                $type = $col['type'];
            }
            $tabColumns = substr($tabColumns, 0, -2) . "]";
            
            if (!empty($type)) {
                $type = "\\Magento\\Framework\\Db\\Adapter\\AdapterInterface::INDEX_TYPE_" . $type;
            } else {
                $type = '';
            }
            
            // Create the Add Index part
            $return .= "\t\t\t->addIndex(" . PHP_EOL;
            $return .= "\t\t\t\t\$installer->getIdxName(" . PHP_EOL;
            $return .= "\t\t\t\t\t'" . $tabName . "'," . PHP_EOL;
            $return .= "\t\t\t\t\t" . $tabColumns;
            if (!empty($type)) {
                $return .= "," . PHP_EOL . "\t\t\t\t\t" . $type;
            }
            $return .= PHP_EOL . "\t\t\t\t)," . PHP_EOL;
            $return .= "\t\t\t\t" . $tabColumns;
            if (!empty($type)) {
                $return .= "," . PHP_EOL . "\t\t\t\t['type' => " . $type . "]";
            }
            $return .= PHP_EOL . "\t\t\t)" . PHP_EOL;
        }
        
        return $return;
    }
    
    /**
     * Create the Add Foreign Key part
     *
     * @param array $column
     * @return string
     */
    private function getAddForeignKey($column)
    {
        $return = '';
        
        foreach ($column['CONSTRAINTS'] as $constraint) {
            $isFk = !empty($constraint['DELETE_RULE']);
            $fkName = $constraint['CONSTRAINT_NAME'];
            $fkTablename = $column['TABLE_NAME'];
            $fkColumnname = $column['COLUMN_NAME'];
            $rfTablename = $constraint['REFERENCED_TABLE_NAME'];
            $rfColumnname = $constraint['REFERENCED_COLUMN_NAME'];
            
            if (!empty($fkName) && !empty($rfTablename) && !empty($rfColumnname) && $isFk) {
                // Set the action for the delete rule
                $action = 'ACTION_' . str_replace(" ", "_", strtoupper($constraint['DELETE_RULE']));
                
                $return .= "\t\t\t->addForeignKey(" . PHP_EOL;
                $return .= "\t\t\t\t\$installer->getFkName(" . PHP_EOL;
                $return .= "\t\t\t\t\t'" . $fkTablename . "'," . PHP_EOL;
                $return .= "\t\t\t\t\t'" . $fkColumnname . "'," . PHP_EOL;
                $return .= "\t\t\t\t\t'" . $rfTablename . "'," . PHP_EOL;
                $return .= "\t\t\t\t\t'" . $rfColumnname . "'" . PHP_EOL;
                $return .= "\t\t\t\t)," . PHP_EOL;
                $return .= "\t\t\t\t'" . $fkColumnname . "'," . PHP_EOL;
                $return .= "\t\t\t\t\$installer->getTable('" . $rfTablename . "')," . PHP_EOL;
                $return .= "\t\t\t\t'" . $rfColumnname . "'," . PHP_EOL;
                $return .= "\t\t\t\t\\Magento\\Framework\\DB\Ddl\Table::" . $action . PHP_EOL;
                $return .= "\t\t\t)" . PHP_EOL;
            }
        }
        
        return $return;
    }
    
    /**
     * Create the Add Column part
     *
     * @param array $column
     * @return string
     */
    private function getAddColumn($column)
    {
        $typesize = $this->getTypeAndSize($column['COLUMN_TYPE']);
        
        // Type
        $type = $typesize['type'];
        
        // Size
        $size = $typesize['size'];
        $size = is_int(strpos($size, ',')) ? '[' . $size . ']' : $size;
        
        // Precision
        $precision = null;
        if (!is_null($column['NUMERIC_PRECISION'])) {
            $precision = $column['NUMERIC_PRECISION'];
        } elseif (!is_null($column['DATETIME_PRECISION'])) {
            $precision = $column['DATETIME_PRECISION'];
        }
        
        // Comment
        $comment = !empty($column['COLUMN_COMMENT']) ? "'" . $column['COLUMN_COMMENT'] . "'" : "null";
     
        // Add the options
        $options = [
            'unsigned' => $typesize['unsigned'],
            'default' => $column['COLUMN_DEFAULT'],
            'null' => $column['IS_NULLABLE'],
            'key' => $this->getKey($column['COLUMN_KEY']),
            'scale' => $column['NUMERIC_SCALE'],
            'precision' => $precision,
            'extra' => $this->getExtra($column['EXTRA'])
        ];
        $options = $this->getOptions($options);
        
        // Add a new column with their properties
        $return = "\t\t\t->addColumn(" . PHP_EOL;                    
        $return .= "\t\t\t\t'" . $column['COLUMN_NAME'] . "'," . PHP_EOL;
        $return .= "\t\t\t\t\Magento\Framework\DB\Ddl\Table::TYPE_" . $type . "," . PHP_EOL;
        $return .= "\t\t\t\t" . $size . "," . PHP_EOL;
        $return .= $options . "," . PHP_EOL;
        $return .= "\t\t\t\t" . $comment . PHP_EOL;
        $return .= "\t\t\t)" . PHP_EOL;
        
        return $return;
    }
    
    /**
     * Create the New Table part
     *
     * @param string $name
     * @param array $table
     * @return string
     */
    private function getNewTable($name, $table)
    {
        $return = "\t\t" . PHP_EOL;
        $return .= "\t\t/**" . PHP_EOL;
        $return .= "\t\t * Create table '" . $name . "'" . PHP_EOL;
        $return .= "\t\t */" . PHP_EOL;
        $return .= "\t\t\$table = \$installer->getConnection()" . PHP_EOL;
        $return .= "\t\t\t->newTable(\$installer->getTable('" . $name . "'))" . PHP_EOL;
        
        // Add the Columns
        foreach ($table as $column) {
            $return .= $this->getAddColumn($column);
            $comment = !empty($column['TABLE_COMMENT']) ? $column['TABLE_COMMENT'] : $column['TABLE_NAME'];
        }
        
        // Add the Indexes
        $return .= $this->getAddIndexes($table);
        
        // Add the Foreign Keys
        foreach ($table as $column) {
            $return .= $this->getAddForeignKey($column);
        }
        
        $return .= "\t\t\t->setComment('" . $comment . "');" . PHP_EOL;
        $return .= "\t\t\$installer->getConnection()->createTable(\$table);" . PHP_EOL;
        $return .= "\t\t" . PHP_EOL;
        
        return $return;
    }
    
    /**
     * Create the header of the InstallSchema script
     *
     * @param string $namespace
     * @return string
     */
    private function getHeader($namespace)
    {
        $return = "<?php" . PHP_EOL;
        $return .= "namespace " . $namespace . "\Setup;" . PHP_EOL;
        $return .= PHP_EOL;
        $return .= "use Magento\Framework\Setup\InstallSchemaInterface;" . PHP_EOL;
        $return .= "use Magento\Framework\Setup\ModuleContextInterface;" . PHP_EOL;
        $return .= "use Magento\Framework\Setup\SchemaSetupInterface;" . PHP_EOL;
        $return .= PHP_EOL;
        $return .= "class InstallSchema implements InstallSchemaInterface" . PHP_EOL;
        $return .= "{" . PHP_EOL;
        $return .= "\tpublic function install(SchemaSetupInterface \$setup, ModuleContextInterface \$context)" . PHP_EOL;
        $return .= "\t{" . PHP_EOL;
        $return .= "\t" . PHP_EOL;
        $return .= "\t\t\$installer = \$setup;" . PHP_EOL;
        $return .= "\t\t" . PHP_EOL;
        $return .= "\t\t\$installer->startSetup();" . PHP_EOL;
        $return .= "\t\t" . PHP_EOL;
        
        return $return;
    }
    
    /**
     * Create the footer of the InstallSchema script
     *
     * @return string
     */
    private function getFooter()
    {
        $return = "\t\t" . PHP_EOL;
        $return .= "\t\t\$installer->endSetup();" . PHP_EOL;
        $return .= "\t\t" . PHP_EOL;
        $return .= "\t}" . PHP_EOL;
        $return .= "}" . PHP_EOL;
        
        return $return;
    }
    
}
