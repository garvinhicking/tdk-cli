<?php

declare(strict_types=1);

namespace GarvinHicking\TdkCli;

use Composer\Script\Event;
use Composer\Util\ProcessExecutor;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

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
        $finder = Finder::create()
            ->ignoreVCS(false)
            ->ignoreDotFiles(false)
            ->depth(0)
            ->files()
            ->notName(['composer-repository.bak', '.DS_Store'])
            ->in(self::$coreDevFolder);

        if (count($finder) > 0) {
            self::message($event, sprintf("TDK: Directory <info>%s</info> is populated with unknown files. Please reset directory to vanilla state.", self::$coreDevFolder));
            return false;
        }

        $filesystem = new Filesystem();
        $initFile = self::$coreDevFolder . '/typo3/sysext/composer-repository.bak';
        $bakFile = '';
        if (file_exists($initFile)) {
            $bakFile = file_get_contents($initFile);
        }

        $filesystem->remove(self::$coreDevFolder);

        $process = new ProcessExecutor();
        $gitRemoteUrl = 'https://github.com/TYPO3/typo3.git';
        $command = sprintf('git clone %s %s', ProcessExecutor::escape($gitRemoteUrl), ProcessExecutor::escape(self::$coreDevFolder));
        self::message($event, '<info>Cloning TYPO3 repository. This may take a while depending on your internet connection!</info>');
        $status = $process->executeTty($command);

        if ($status) {
            self::message($event, '<warning>Could not download git repository ' . $gitRemoteUrl . ' </warning>');
            return false;
        }

        // Restore the composer-repository.bak file so that it does not show up as removed/invalid
        // in our project's composer.json
        // It will not show up for the typo3-core sub-repository, because *.bak is excluded there.
        file_put_contents($initFile, $bakFile);

        return true;
    }
}

