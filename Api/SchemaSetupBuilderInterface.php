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
namespace Blackbird\InstallSchemaGenerator\Api;

/**
 * @api
 */
interface SchemaSetupBuilderInterface
{
    /**
     * Generate the SetupSchema.php class file
     * 
     * @param array $tables
     * @param string $namespace
     * @param string $location
     * @return string
     * @api
     */
    public function generate(
        array $tables = [],
        $namespace = null,
        $location = null
    );
}
