<?php declare(strict_types=1);
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Seeds;

/**
 * Class Seeder.
 *
 * @package app
 */
class Seeder extends \Framework\Database\Extra\Seeder
{
    public function run() : void
    {
        $this->call([
            Users::class,
        ]);
    }
}
