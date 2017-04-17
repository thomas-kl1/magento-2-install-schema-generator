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
namespace Blackbird\InstallSchemaGenerator\Model;

use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\View\Element\BlockFactory;
use Blackbird\InstallSchemaGenerator\Api\SchemaSetupBuilderInterface;
use Blackbird\InstallSchemaGenerator\Model\DB\SchemaRetriever;
use Blackbird\InstallSchemaGenerator\Block\InstallSchema;

/**
 * Class SchemaSetupBuilder
 */
class SchemaSetupBuilder implements SchemaSetupBuilderInterface
{
    /**
     * @var SchemaRetriever
     */
    private $schemaRetriever;
    
    /**
     * @var FileFactory
     */
    private $fileFactory;
    
    /**
     * @var BlockFactory 
     */
    private $blockFactory;
    
    /**
     * @param SchemaRetriever $schemaRetriever
     * @param FileFactory $fileFactory
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        SchemaRetriever $schemaRetriever,
        FileFactory $fileFactory,
        BlockFactory $blockFactory
    ) {
        $this->schemaRetriever = $schemaRetriever;
        $this->fileFactory = $fileFactory;
        $this->blockFactory = $blockFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function generate(
        array $tables = [],
        $namespace = null,
        $location = null
    ) {
        // Generate the render
        $block = $this->blockFactory->createBlock(InstallSchema::class)
            ->setNamespace($this->isNamespace($namespace) ? $namespace : 'Vendor\Area')
            ->setTables($this->schemaRetriever->getSchema($tables));
        
        $filename = '';//todo
        
        // Create the InstallSchema.php class file
        $this->fileFactory->create(
            $filename,
            $block->toHtml(),
            DirectoryList::TMP//todo
        );
        
        return $filename;
    }
    
    /**
     * If pattern is a part of namespace, then return it, else return false
     * 
     * @param string $namespace
     * @return boolean|string
     */
    private function isNamespace($namespace)
    {
        return (!empty($namespace) && count(explode("\\", $namespace)) === 2);
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
}
