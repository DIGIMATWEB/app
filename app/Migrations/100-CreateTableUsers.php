<?php declare(strict_types=1);
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Migrations;

use Framework\Database\Definition\Table\TableDefinition;
use Framework\Database\Extra\Migration;

/**
 * Class CreateTableUsers.
 *
 * @package app
 */
final class CreateTableUsers extends Migration
{
    protected string $table = 'Users';

    public function up() : void
    {
        $this->database->createTable($this->table)
            ->definition(static function (TableDefinition $def) : void {
                $def->column('id')->int(11)->autoIncrement()->primaryKey();
                $def->column('name')->varchar(64);
                $def->column('email')->varchar(255)->uniqueKey();
                $def->column('password')->varchar(255);
                $def->column('configs')->json()->default('{}');
                $def->column('updatedAt')->timestamp();
                $def->column('createdAt')->timestamp();
            })->run();
    }

    public function down() : void
    {
        $this->database->dropTable($this->table)->ifExists()->run();
    }
}
