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
namespace Blackbird\InstallSchemaGenerator\Controller\Adminhtml\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Blackbird\InstallSchemaGenerator\Api\SchemaSetupBuilderInterface;

class Retriever extends Action
{
    /**
     * @var FileFactory
     */
    private $fileFactory;
    
    /**
     * @var SchemaSetupBuilderInterface
     */
    private $installSchemaBuilder;
    
    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param SchemaSetupBuilderInterface $installSchemaBuilder
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        SchemaSetupBuilderInterface $installSchemaBuilder
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->installSchemaBuilder = $installSchemaBuilder;
    }
    
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        
        // todo refactor
        $vendor = trim($this->getRequest()->getParam('vendor'));
        $vendor = !empty($vendor) ? $vendor : 'Vendor';
        $module = trim($this->getRequest()->getParam('module'));
        $module = !empty($module) ? $module : 'Module';
        $namespace = $vendor . '\\' . $module;
        $tables = $this->getRequest()->getParam('tables');

        if (!is_array($tables)) {
            $this->messageManager->addErrorMessage(__('Please select at least one table.'));
        } else {
            try {
                $filename = $this->installSchemaBuilder->generate($tables, $namespace);

                $this->fileFactory->create(
                    $filename,
                    ['type' => 'filename', 'value' => $filename],
                    DirectoryList::TMP,
                    'application/octet-stream'
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            }
        }
        
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
