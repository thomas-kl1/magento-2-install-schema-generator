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
namespace Blackbird\InstallSchemaGenerator\Model\Resource;

use Magento\Framework\Model\Resource\Db\AbstractDb;

class SchemaRetriever extends AbstractDb
{       
    protected $dbname;
    
    public function _construct()
    {
        // Load default configuration
        $test = include '/app/etc/env.php';
        $this->dbname = $test['db']['connection']['default']['dbname'];
    }
    
    /**
     * Get all tables of database
     * 
     * @return Array
     */
    public function getTables() {
        $sql = "SELECT TABLE_NAME FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = '" . $this->dbname . "'";
        
        // Prepare query
        $fetch = $this->getReadConnection()->fetchAll($sql);
        return $fetch;
    }
    
    /**
     * Return schema of tables
     * 
     * @param array $tables
     * @return array
     */
    public function getSchema($tables = array())
    {
        // Select all informations about columns, indexes and foreign keys for table(s)
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
        
        // If tables is empty, columns of all tables will be return
        if (is_array($tables) && !empty($tables)) {
            $sql .= " AND C.TABLE_NAME IN (";
            foreach($tables as $table) {
                $sql .= "'" . $table . "', ";
            }
            $sql = substr($sql, 0, -2) . ")";
        }
        
        $sql .= " ORDER BY C.TABLE_NAME, C.ORDINAL_POSITION";
       
        // Prepare query
        $schema = $this->getReadConnection()->fetchAll($sql);
        
        $schema = $this->sanitizeSchema($schema);
        
        return $schema;
    }
    
    /**
     * Sort columns by table in a sub array
     * 
     * @param array $schema
     * @return array
     */
    private function sanitizeSchema($schema)
    {
        if (!is_array($schema)) {
            return array();
        }
        
        $finalSchema = array();
        
        foreach ($schema as $column) {
            $tabname = $column['TABLE_NAME'];
            $colname = $column['COLUMN_NAME'];
            
            if (!isset($finalSchema[$tabname])) {
                $finalSchema[$tabname] = array();
            }
            if (!isset($finalSchema[$tabname][$colname]['CONSTRAINTS'])) {
                $finalSchema[$tabname][$colname]['CONSTRAINTS'] = array();
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
