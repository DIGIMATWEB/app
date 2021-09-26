<?php
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controllers\API;

use Framework\HTTP\Response;
use Framework\MVC\Controller as BaseController;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * Class Controller.
 *
 * @package app
 */
abstract class Controller extends BaseController
{
    protected function beforeAction(string $method, array $arguments) : ?Response
    {
        $auth = $this->request->getBasicAuth();
        if (isset($auth['username'], $auth['password'])) {
            if ($this->isAuthorized($auth['username'], $auth['password'])) {
                return null;
            }
        }
        return $this->response->setStatus(Response::CODE_UNAUTHORIZED)
            ->setHeader(
                Response::HEADER_WWW_AUTHENTICATE,
                'realm="REST API Access"'
            )
            ->setJson([
                'status' => [
                    'code' => $this->response->getStatusCode(),
                    'reason' => $this->response->getStatusReason(),
                ],
            ]);
    }

    private function isAuthorized(string $username, string $token) : bool
    {
        if ($username === 'radio' && $token === 'lupalupa') {
            return true;
        }
        return false;
    }

    /**
     * @param array<string,mixed> $custom
     * @param int $code
     *
     * @return array<string,mixed>
     */
    protected function respond(array $custom = [], int $code = Response::CODE_OK) : array
    {
        $this->response->setStatus($code);
        $default = [
            'status' => $this->getStatus(),
        ];
        return $custom ? \array_merge($default, $custom) : $default;
    }

    /**
     * @param array<string,string> $errors
     *
     * @return array<string,mixed>
     */
    protected function respondErrors(array $errors) : array
    {
        return $this->respond([
            'errors' => $errors,
        ], Response::CODE_BAD_REQUEST);
    }

    /**
     * @param array<string,mixed> $custom
     *
     * @return array<string,mixed>
     */
    public function respondNotFound(array $custom = []) : array
    {
        return $this->respond($custom, Response::CODE_NOT_FOUND);
    }

    /**
     * @return array<string,mixed>
     */
    #[Pure]
    #[ArrayShape(['code' => 'int', 'reason' => 'string'])]
    protected function getStatus() : array
    {
        return [
            'code' => $this->response->getStatusCode(),
            'reason' => $this->response->getStatusReason(),
        ];
    }
}
