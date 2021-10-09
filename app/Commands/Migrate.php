<?php
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Commands;

use App;
use Framework\CLI\CLI;
use Framework\CLI\Command;
use Framework\Database\Extra\Migrator;

/**
 * Class Migrate.
 *
 * @package app
 */
class Migrate extends Command
{
    protected string $description = 'Runs database migrations.';
    protected string $usage = 'migrate [to]';
    protected array $options = [
        '--database' => 'Database config instance name.',
        '--locator' => 'Locator config instance name.',
    ];

    public function run() : void
    {
        $databaseInstance = (string) ($this->console->getOption('database') ?? 'default');
        $locatorInstance = (string) ($this->console->getOption('database') ?? 'default');
        $locator = App::locator($locatorInstance);
        $migrator = new Migrator(App::database($databaseInstance), $locator);
        $migrator->addFiles($locator->listFiles(APP_DIR . 'Migrations'));
        $to = $this->getTo();
        if ($to === 'up') {
            foreach ($migrator->migrateUp() as $version) {
                $this->showMigrationMessage($version);
            }
            return;
        }
        if ($to === 'down') {
            foreach ($migrator->migrateDown() as $version) {
                $this->showMigrationMessage($version);
            }
            return;
        }
        if ($migrator->getCurrentVersion() === $to) {
            CLI::write('Migrations already in version ' . $to . '.', CLI::FG_GREEN);
            return;
        }
        foreach ($migrator->migrateTo($to) as $version) {
            $this->showMigrationMessage($version);
        }
    }

    protected function getTo() : string
    {
        $to = $this->console->getArgument(0);
        if ($to === null) {
            $to = CLI::prompt('Migrate to');
        }
        if ($to !== 'down' && $to !== 'up' && ! \is_numeric($to)) {
            CLI::error('Invalid direction/version.', null);
            $to = $this->getTo();
        }
        return $to;
    }

    protected function showMigrationMessage(string $version) : void
    {
        CLI::write('Migrated to version ' . $version . '.');
    }
}
