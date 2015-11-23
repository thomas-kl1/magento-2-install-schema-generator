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
namespace Blackbird\InstallSchemaGenerator\Controller\Adminhtml\Index;

use Magento\Framework\App\Filesystem\DirectoryList;

class Post extends \Magento\Backend\App\Action
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
        parent::__construct(
            $context
        );
        $this->fileFactory = $fileFactory;
    }
    
    /**
     * Download file
     * 
     * @param String $fileName
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
     * Display Install Schema
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $isPost = $this->getRequest()->getPost();
        
        if($isPost) {
            $tables = $this->getRequest()->getParam('tables');
        
            if (!is_array($tables)) {
                $this->messageManager->addError(__('Please select at least one table.'));
            } else {
                $retriever = $this->_objectManager->create('Blackbird\InstallSchemaGenerator\Model\Resource\SchemaRetriever');
                $builder = $this->_objectManager->create('Blackbird\InstallSchemaGenerator\Model\SchemaSetupBuilder');

                try {
                    $schema = $retriever->getSchema($tables);
                    $result = $builder->getSetupBySchema($schema);
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }

                $this->download('InstallSchema.php', $result);
                $this->messageManager->addSuccess(__('Your InstallSchema.php is downloading !'));   
            }
        }
        
        $this->_redirect('*/*/index');
        return;
    }
}
