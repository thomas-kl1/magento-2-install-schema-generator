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
        $this->fileFactory = $fileFactory;
        $this->installSchemaBuilder = $installSchemaBuilder;
        parent::__construct($context);
    }
    
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $tables = $this->getRequest()->getParam('tables');

        if (!is_array($tables)) {
            $this->messageManager->addErrorMessage(__('Please select at least one table.'));
        } else {
            try {
                $filename = $this->installSchemaBuilder->generate($tables, $this->getCustomNamespace());

                $this->fileFactory->create(
                    'InstallSchema.php',
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

    /**
     * Retrieve the namespace param
     *
     * @return string
     */
    private function getCustomNamespace()
    {
        $vendor = trim($this->getRequest()->getParam('vendor'));
        $vendor = !empty($vendor) ? $vendor : 'Vendor';
        $module = trim($this->getRequest()->getParam('module'));
        $module = !empty($module) ? $module : 'Area';

        return $vendor . '\\' . $module;
    }
}
