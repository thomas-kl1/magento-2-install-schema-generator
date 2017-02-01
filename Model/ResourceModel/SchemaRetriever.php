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
namespace Blackbird\InstallSchemaGenerator\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SchemaRetriever extends AbstractDb
{       
    /**
     * The default database name
     * 
     * @var string
     */
    protected $dbname;
    
    /**
     * @return void
     */
    public function _construct()
    {
        $dbConfig = $this->getConnection()->getConfig();
        $this->dbname = $dbConfig['dbname'];
    }

    /**
     * Retrieve all tables
     * 
     * @return array
     */
    public function getTablesOptions()
    {
        $options = [];
        
        foreach ($this->getConnection()->getTables() as $table) {
            $options[] = [
                'value' => $table,
                'label' => __($table)
            ];
        }
        
        return $options;
    }
    
    /**
     * Return the tables schema
     * 
     * @param array $tables
     * @return array
     */
    public function getSchema($tables = [])
    {
        // Select all informations about columns, indexes and foreign keys
        $sql = "SELECT T.TABLE_NAME, T.TABLE_COMMENT,
                       C.COLUMN_NAME, C.COLUMN_COMMENT, C.COLUMN_DEFAULT, C.COLUMN_TYPE, COLUMN_KEY,
                       C.IS_NULLABLE, C.NUMERIC_PRECISION, C.NUMERIC_SCALE, C.DATETIME_PRECISION, C.EXTRA,
                       S.INDEX_NAME, S.INDEX_TYPE, TC.CONSTRAINT_NAME, TC.CONSTRAINT_TYPE,
                       KCU.REFERENCED_TABLE_NAME, KCU.REFERENCED_COLUMN_NAME, RC.DELETE_RULE
                       
                FROM information_schema.TABLES T
                LEFT OUTER JOIN information_schema.COLUMNS AS C
                    ON T.TABLE_CATALOG = C.TABLE_CATALOG
                    AND T.TABLE_SCHEMA = C.TABLE_SCHEMA
                    AND T.TABLE_NAME = C.TABLE_NAME
                    
                LEFT OUTER JOIN information_schema.STATISTICS AS S
                    ON T.TABLE_CATALOG = S.TABLE_CATALOG
                    AND T.TABLE_SCHEMA = S.TABLE_SCHEMA
                    AND T.TABLE_NAME = S.TABLE_NAME
                    AND C.COLUMN_NAME = S.COLUMN_NAME
                    
                LEFT OUTER JOIN information_schema.TABLE_CONSTRAINTS AS TC
                    ON T.TABLE_SCHEMA = TC.TABLE_SCHEMA
                    AND T.TABLE_NAME = TC.TABLE_NAME
                    AND S.INDEX_NAME = TC.CONSTRAINT_NAME
                    
                LEFT OUTER JOIN information_schema.KEY_COLUMN_USAGE AS KCU
                    ON T.TABLE_CATALOG = KCU.TABLE_CATALOG
                    AND T.TABLE_SCHEMA = KCU.TABLE_SCHEMA
                    AND T.TABLE_NAME = KCU.TABLE_NAME
                    AND C.COLUMN_NAME = KCU.COLUMN_NAME
                    
                LEFT OUTER JOIN information_schema.REFERENTIAL_CONSTRAINTS AS RC
                    ON KCU.CONSTRAINT_CATALOG = RC.CONSTRAINT_CATALOG
                    AND KCU.CONSTRAINT_SCHEMA = RC.CONSTRAINT_SCHEMA
                    AND KCU.CONSTRAINT_NAME = RC.CONSTRAINT_NAME
                    AND KCU.TABLE_NAME = RC.TABLE_NAME
                    AND KCU.REFERENCED_TABLE_NAME = RC.REFERENCED_TABLE_NAME
                
                WHERE C.TABLE_SCHEMA = '" . $this->dbname . "'";
        
        // If no specific table is given, we return all database tables
        if (is_array($tables) && !empty($tables)) {
            $sql .= $this->getConnection()->quoteInto(' AND C.TABLE_NAME IN (?)', $tables);
        }
        
        $sql .= " ORDER BY C.TABLE_NAME, C.ORDINAL_POSITION";
       
        // Prepare the query
        $schema = $this->getConnection()->fetchAll($sql);
        
        $schema = $this->sanitizeSchema($schema);
        
        return $schema;
    }
    
    /**
     * Sort the columns by table in a sub array
     * 
     * @param array $schema
     * @return array
     */
    private function sanitizeSchema($schema)
    {
        if (!is_array($schema)) {
            return [];
        }
        
        $finalSchema = [];
        
        foreach ($schema as $column) {
            $tabname = $column['TABLE_NAME'];
            $colname = $column['COLUMN_NAME'];
            
            if (!isset($finalSchema[$tabname])) {
                $finalSchema[$tabname] = [];
            }
            if (!isset($finalSchema[$tabname][$colname]['CONSTRAINTS'])) {
                $finalSchema[$tabname][$colname]['CONSTRAINTS'] = [];
            }
            
            $finalSchema[$tabname][$colname]['CONSTRAINTS'][] = [
                'REFERENCED_TABLE_NAME' => $column['REFERENCED_TABLE_NAME'], 
                'REFERENCED_COLUMN_NAME' => $column['REFERENCED_COLUMN_NAME'], 
                'CONSTRAINT_NAME' => $column['CONSTRAINT_NAME'],
                'INDEX_NAME' => $column['INDEX_NAME'],
                'INDEX_TYPE' => $column['INDEX_TYPE'],
                'CONSTRAINT_TYPE' => $column['CONSTRAINT_TYPE'],
                'DELETE_RULE' => $column['DELETE_RULE']
            ];
            
            unset($column['REFERENCED_TABLE_NAME'], $column[11],
                $column['REFERENCED_COLUMN_NAME'], $column[12],
                $column['CONSTRAINT_NAME'], $column[13],
                $column['INDEX_NAME'], $column[14],
                $column['INDEX_TYPE'], $column[15],
                $column['CONSTRAINT_TYPE'], $column[16],
                $column['DELETE_RULE'], $column[17]
            );
            
            $column['CONSTRAINTS'] = $finalSchema[$tabname][$colname]['CONSTRAINTS'];
            
            $finalSchema[$tabname][$colname] = $column;
        }
        
        return $finalSchema;
    }
}
