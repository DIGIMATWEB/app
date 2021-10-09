<?php declare(strict_types=1);
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Seeds;

use App\Models\Users as UsersModel;
use Framework\Crypto\Password;
use Framework\Database\Extra\Seeder;
use Generator;
use RuntimeException;

/**
 * Class Users.
 *
 * @package app
 */
class Users extends Seeder
{
    public function run() : void
    {
        $usersModel = new UsersModel();
        foreach ($this->rows() as $row) {
            $created = $usersModel->create($row);
            if ($created === false) {
                throw new RuntimeException(
                    'User could not be created. Errors: ' . \json_encode($usersModel->getErrors())
                );
            }
        }
    }

    /**
     * @return Generator<array>
     */
    protected function rows() : Generator
    {
        yield [
            'name' => 'John Doe',
            'email' => 'john@doe.tld',
            'password' => Password::hash('password'),
        ];
        yield [
            'name' => 'Mary Doe',
            'email' => 'mary@doe.tld',
            'password' => Password::hash('password'),
        ];
        yield [
            'name' => 'Nathan Doe',
            'email' => 'nathan@doe.tld',
            'password' => Password::hash('password'),
        ];
    }
}
