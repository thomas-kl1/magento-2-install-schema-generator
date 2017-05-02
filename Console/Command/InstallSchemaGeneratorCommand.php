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
namespace Blackbird\InstallSchemaGenerator\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State as AppState;
use Blackbird\InstallSchemaGenerator\Api\SchemaSetupBuilderInterface;

/**
 * Class InstallSchemaGeneratorCommand
 */
class InstallSchemaGeneratorCommand extends Command
{
    /**
     * Databse tables argument
     */
    const INPUT_DATABASE_TABLES = 'tables';

    /**
     * Namespace argument option
     */
    const INPUT_NAMESPACE = 'namespace';
    
    /**
     * Option for custom generation location
     */
    const GENERATE_LOCATION = 'location';

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var SchemaSetupBuilderInterface
     */
    private $installSchemaBuilder;

    /**
     * @param AppState $appState
     * @param SchemaSetupBuilderInterface $installSchemaBuilder
     * @param null $name
     */
    public function __construct(
        AppState $appState,
        SchemaSetupBuilderInterface $installSchemaBuilder,
        $name = null
    ) {
        $this->appState = $appState;
        $this->installSchemaBuilder = $installSchemaBuilder;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('isg:generate')
            ->setDescription('Generate the InstallSchema.php file for the database tables')
            ->setDefinition([
                new InputArgument(
                    self::INPUT_DATABASE_TABLES,
                    InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                    'Space-separated list of database tables or omit to apply to all database tables.'
                ),
                /*new InputOption(
                    self::INPUT_NAMESPACE,
                    '-n',
                    InputOption::VALUE_REQUIRED,
                    'Set specific namespace to the InstallSchema class.'
                ),*/
                new InputOption(
                    self::GENERATE_LOCATION,
                    '-l',
                    InputOption::VALUE_REQUIRED,
                    'Set the relative file location from the temp dir of your Magento, to generate the InstallSchema.php file.'
                ),
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);

        try {
            $filename = $this->installSchemaBuilder->generate(
                array_map('trim', $input->getArgument(self::INPUT_DATABASE_TABLES))/*,
                $input->getOption(self::INPUT_NAMESPACE),
                $input->getOption(self::GENERATE_LOCATION)*/
            );
            
            $output->writeln('<info>The InstallSchema class file has been written in: ' . $filename . '</info>');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());        
        }
    }
}
