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
namespace Blackbird\InstallSchemaGenerator\Controller\Adminhtml\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;

class Retriever extends Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
    }
    
    /**
     * Download the file
     * 
     * @param string $fileName
     * @param string $content
     */
    protected function download($fileName, $content)
    {
        $this->fileFactory->create(
            $fileName,
            $content,
            DirectoryList::TMP,
            'application/octet-stream'
        );
    }
    
    /**
     * Download action of the Install Schema script
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $isPost = $this->getRequest()->getPost();
        
        if($isPost) {
            $vendor = trim($this->getRequest()->getParam('vendor'));
            $vendor = !empty($vendor) ? $vendor : 'Vendor';
            $module = trim($this->getRequest()->getParam('module'));
            $module = !empty($module) ? $module : 'Module';
            
            $namespace = $vendor . '\\' . $module;
            $tables = $this->getRequest()->getParam('tables');
        
            if (!is_array($tables)) {
                $this->messageManager->addErrorMessage(__('Please select at least one table.'));
            } else {
                $retriever = $this->_objectManager->create('Blackbird\InstallSchemaGenerator\Model\ResourceModel\SchemaRetriever');
                $builder = $this->_objectManager->create('Blackbird\InstallSchemaGenerator\Model\SchemaSetupBuilder');

                try {
                    $schema = $retriever->getSchema($tables);
                    $result = $builder->getSetupBySchema($schema, $namespace);
                    $this->download('InstallSchema.php', $result);
                    $this->messageManager->addSuccessMessage(__('Your InstallSchema.php is downloading!'));
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e, $e->getMessage());
                }
            }
        }
        
        return $resultRedirect->setPath('*/*/');
    }
}
