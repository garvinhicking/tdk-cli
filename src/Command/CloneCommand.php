<?php

declare(strict_types=1);

namespace GarvinHicking\TdkCli\Command;

use GarvinHicking\TdkCli\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CloneCommand extends Command
{
    protected static $defaultName = 'clone';

    protected function configure(): void
    {
        $this->setDescription('Clone TYPO3 repository');
        $this->setHelp(
            <<<'EOT'
                    The <info>%command.name%</info> command checks out a
                    working directory for the TYPO3 GitHub repository.

                    <info>$ php %command.name%</info>

                    EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Cli::cloneRepository($output)) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
