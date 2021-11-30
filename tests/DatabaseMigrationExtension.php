<?php

namespace App\Tests;

use Codeception\Events;
use Codeception\Extension;

class DatabaseMigrationExtension extends Extension
{
    public static array $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
    ];

    public function beforeSuite()
    {
        try {
            $symfony = $this->getModule('Symfony');

            $this->writeln($_ENV["DATABASE_URL"]);

            $symfony->runSymfonyConsoleCommand('doctrine:database:drop', ['--if-exists' => true, '--force' => true]);
            $symfony->runSymfonyConsoleCommand('doctrine:database:create');
            $symfony->runSymfonyConsoleCommand('doctrine:migrations:migrate', ['--no-interaction' => true]);
        } catch (\Exception $e) {
            $this->writeln(
                sprintf(
                    'An error occurred whilst rebuilding the test database: %s\n %s',
                    $e->getMessage(), $e->getTraceAsString()
                )
            );
        }
    }
}
