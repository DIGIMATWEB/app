<?php
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Entities;

use Framework\Date\Date;
use Framework\HTTP\URL;
use Framework\MVC\Entity;

/**
 * Class User.
 *
 * @package app
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property \Framework\HTTP\URL $url
 * @property array $configs
 * @property \Framework\Date\Date $createdAt
 * @property \Framework\Date\Date $updatedAt
 */
class User extends Entity
{
    protected static array $jsonVars = [
        'id',
        'name',
        'email',
        'url',
        'configs',
        'createdAt',
        'updatedAt',
    ];
    protected int $id;
    protected string $name;
    protected string $email;
    protected string $password;
    protected URL $url;
    /**
     * @var array<mixed>
     */
    protected array $configs;
    protected Date $createdAt;
    protected Date $updatedAt;

    protected function init() : void
    {
        $this->url = new URL(route_url('api.users.show', [$this->id]));
    }
}
