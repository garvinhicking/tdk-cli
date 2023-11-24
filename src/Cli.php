<?php

declare(strict_types=1);

namespace GarvinHicking\TdkCli;

use Composer\Script\Event;
use Composer\Util\ProcessExecutor;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class Cli
{
    protected static string $coreDevFolder = 'typo3-core';

    public static function message(Event|OutputInterface $event, string $message): void
    {
        if ($event instanceof Event) {
            $event->getIO()->write($message);
        } else {
            $event->writeln($message);
        }
    }

    public static function cloneRepository(Event|OutputInterface $event): bool
    {
        self::message($event, "Checking repo.");

        $filesystem = new Filesystem();
        if (!$filesystem->exists(self::$coreDevFolder)) {
            $process = new ProcessExecutor();
            $gitRemoteUrl = 'https://github.com/TYPO3/typo3.git';
            $command = sprintf('echo git clone %s %s', ProcessExecutor::escape($gitRemoteUrl), ProcessExecutor::escape(self::$coreDevFolder));
            self::message($event, '<info>Cloning TYPO3 repository. This may take a while depending on your internet connection!</info>');
            $status = $process->executeTty($command);

            if ($status) {
                self::message($event, '<warning>Could not download git repository ' . $gitRemoteUrl . ' </warning>');
                return false;
            }
        } else {
            self::message($event, 'Repository exists! Therefore no download required.');
            return true;
        }

        return true;
    }
}

