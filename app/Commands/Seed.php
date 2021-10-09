<?php
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Commands;

use App;
use App\Seeds\Seeder;
use Framework\CLI\Command;

/**
 * Class Seed.
 *
 * @package app
 */
class Seed extends Command
{
    protected string $description = 'Seeds a database.';
    protected string $usage = 'seed';
    protected array $options = [
        '--database' => 'Database config instance name.',
    ];

    public function run() : void
    {
        $instance = (string) ($this->console->getOption('database') ?? 'default');
        $seeder = new Seeder(App::database($instance));
        $seeder->run();
    }
}
