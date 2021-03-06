<?php
namespace DBCO\HealthAuthorityAPI\Application\Commands;

use DBCO\HealthAuthorityAPI\Application\Services\SecurityService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cache security module keys.
 *
 * @package DBCO\HealthAuthorityAPI\Application\Commands
 */
class CacheKeysCommand extends Command
{
    protected static $defaultName = 'security:cache-keys';

    /**
     * @var SecurityService
     */
    private SecurityService $securityService;

    /**
     * Constructor.
     *
     * @param SecurityService $securityService
     */
    public function __construct(SecurityService $securityService)
    {
        parent::__construct();
        $this->securityService = $securityService;
    }

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Load keys from the security module into memory')
            ->setHelp('Can be used to load keys from the security module into Redis memory');
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->securityService->cacheKeys();
        $output->writeln("Keys cached successfully!");
        return Command::SUCCESS;
    }
}
