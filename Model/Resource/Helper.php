<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Blackbird\InstallSchemaGenerator\Model\Resource;

class Helper extends \Magento\Framework\DB\Helper
{
    public function getDbname()
    {
        $dbConfig = $this->_getReadAdapter()->getConfig();
        
        return $dbConfig['dbname'];
    }
}
