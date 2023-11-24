<?php

declare(strict_types=1);

namespace GarvinHicking\TdkCli\Cli;

use Composer\Script\Event;
use Composer\Util\ProcessExecutor;
use Symfony\Component\Filesystem\Filesystem;

class Cli
{
    protected static string $coreDevFolder = 'typo3-core';

    public static function cloneRepository(Event $event): void
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists(self::$coreDevFolder)) {
            $process = new ProcessExecutor();
            $gitRemoteUrl = 'https://github.com/TYPO3/typo3.git';
            $command = sprintf('git clone %s %s', ProcessExecutor::escape($gitRemoteUrl), ProcessExecutor::escape(self::$coreDevFolder));
            $event->getIO()->write('<info>Cloning TYPO3 repository. This may take a while depending on your internet connection!</info>');
            $status = $process->executeTty($command);

            if ($status) {
                $event->getIO()->write('<warning>Could not download git repository ' . $gitRemoteUrl . ' </warning>');
            }
        } else {
            $event->getIO()->write('Repository exists! Therefore no download required.');
        }
    }
}

