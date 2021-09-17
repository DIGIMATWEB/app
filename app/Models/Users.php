<?php declare(strict_types=1);
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Models;

use App\Entities\User;
use Framework\MVC\Model;

/**
 * Class Users.
 *
 * @package app
 *
 * @method User|null find(int|string $id)
 */
class Users extends Model
{
    protected array $allowedFields = [
        'name',
        'email',
        'password',
        'configs',
    ];
    protected bool $autoTimestamps = true;
    protected array $validationRules = [
        'name' => 'required|minLength:5|maxLength:32',
        'email' => 'required|email|maxLength:255|unique:Users',
        'password' => 'required|minLength:95|maxLength:255',
        'configs' => 'optional|json',
    ];
    protected string $returnType = User::class;
    protected bool $cacheActive = false;

    protected function getValidationLabels() : array
    {
        return $this->validationLabels
            ?? $this->validationLabels = [ // @phpstan-ignore-line
                'name' => lang('users.name'),
                'email' => lang('users.email'),
                'password' => lang('users.password'),
                'configs' => lang('users.configs'),
            ];
    }
}
