<?php
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controllers\API;

use App;

/**
 * Class Home.
 *
 * @package app
 */
final class Home extends Controller
{
    /**
     * @return array<string,mixed>
     */
    public function index() : array
    {
        return $this->respond([
            'data' => App::router()->getMatchedCollection(),
        ]);
    }
}
