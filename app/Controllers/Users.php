<?php
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controllers;

use App\Models\Users as UsersModel;
use Framework\Crypto\Password;
use Framework\HTTP\Request;
use Framework\HTTP\Response;
use Framework\MVC\Controller;
use Framework\MVC\ModelInterface;
use Framework\Pagination\Pager;
use Framework\Routing\Attributes\Route;
use Framework\Routing\ResourceInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * Class Users.
 *
 * @package app
 */
final class Users extends Controller implements ResourceInterface
{
    protected string $modelClass = UsersModel::class;
    /**
     * @var UsersModel
     */
    protected ModelInterface | UsersModel $model;

    /**
     * @return array<string,mixed>
     */
    #[Route('GET', '/users', name: 'users.index')]
    public function index() : array
    {
        $page = $this->request->getGet('page') ?? 1;
        $page = Pager::sanitizePageNumber($page);
        $entities = $this->model->paginate($page, 2);
        foreach ($entities as $entity) {
            unset(
                $entity->configs,
                $entity->createdAt,
                $entity->updatedAt
            );
        }
        return [
            'status' => $this->getStatus(),
            'data' => $entities,
            'links' => $this->model->getPager(),
        ];
    }

    /**
     * @param string $id
     *
     * @return array<string,mixed>
     */
    #[Route('GET', '/users/{int}', name: 'users.show')]
    public function show(string $id) : array
    {
        $entity = $this->model->find($id);
        if ($entity) {
            return [
                'status' => $this->getStatus(),
                'data' => $entity,
            ];
        }
        $this->response->setStatus(Response::CODE_NOT_FOUND);
        return [
            'status' => $this->getStatus(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    #[Route('POST', '/users', name: 'users.create')]
    public function create() : array
    {
        $data = $this->request->isJson()
            ? $this->request->getJson(true)
            : $this->request->getPost();
        if ($data === false) {
            $this->response->setStatus(Response::CODE_UNPROCESSABLE_ENTITY);
            return [
                'status' => $this->getStatus(),
            ];
        }
        if (isset($data['password']) && \is_string($data['password'])) {
            $data['password'] = Password::hash($data['password']);
        }
        $id = $this->model->create($data);
        if ($id === false) {
            return $this->respondErrors();
        }
        $entity = $this->model->find($id);
        if ($entity) {
            $this->response->setStatus(Response::CODE_CREATED);
            return [
                'status' => $this->getStatus(),
                'data' => $entity,
            ];
        }
        $this->response->setStatus(Response::CODE_PROCESSING);
        return [
            'status' => $this->getStatus(),
        ];
    }

    /**
     * @param string $id
     *
     * @return array<string,mixed>
     */
    #[Route('PATCH', '/users/{int}', name: 'users.update')]
    public function update(string $id) : array
    {
        $data = $this->request->isJson()
            ? $this->request->getJson(true)
            : $this->request->getPost();
        if ($data === false) {
            $this->response->setStatus(Response::CODE_UNPROCESSABLE_ENTITY);
            return [
                'status' => $this->getStatus(),
            ];
        }
        $entity = $this->model->find($id);
        if ( ! $entity) {
            $this->response->setStatus(Response::CODE_NOT_FOUND);
            return [
                'status' => $this->getStatus(),
            ];
        }
        if (isset($data['email']) && $data['email'] === $entity->email) {
            unset($data['email']);
        }
        if (isset($data['password']) && \is_string($data['password'])) {
            $data['password'] = Password::hash($data['password']);
        }
        $affectedRows = $this->model->update($id, $data);
        if ($affectedRows === false) {
            return $this->respondErrors();
        }
        $entity = $this->model->find($id);
        if ($entity) {
            $this->response->setStatus(Response::CODE_OK);
            return [
                'status' => $this->getStatus(),
                'data' => $entity,
            ];
        }
        $this->response->setStatus(Response::CODE_PROCESSING);
        return [
            'status' => $this->getStatus(),
        ];
    }

    /**
     * @param string $id
     *
     * @return array<string,mixed>
     */
    #[Route('PUT', '/users/{int}', name: 'users.replace')]
    public function replace(string $id) : array
    {
        $this->response->setStatus(Response::CODE_METHOD_NOT_ALLOWED)
            ->setHeader(Response::HEADER_ALLOW, \implode(', ', [
                Request::METHOD_DELETE,
                Request::METHOD_GET,
                Request::METHOD_HEAD,
                Request::METHOD_PATCH,
            ]));
        return [
            'status' => $this->getStatus(),
        ];
    }

    /**
     * @param string $id
     *
     * @return array<string,mixed>
     */
    #[Route('DELETE', '/users/{int}', name: 'users.delete')]
    public function delete(string $id) : array
    {
        $entity = $this->model->find($id);
        if ( ! $entity) {
            $this->response->setStatus(Response::CODE_NOT_FOUND);
            return [
                'status' => $this->getStatus(),
            ];
        }
        $this->model->delete($id);
        $this->response->setStatus(Response::CODE_OK);
        return [
            'status' => $this->getStatus(),
        ];
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

    /**
     * @return array<string,mixed>
     */
    #[ArrayShape(['status' => 'array', 'errors' => 'string[]'])]
    protected function respondErrors() : array
    {
        $this->response->setStatus(Response::CODE_BAD_REQUEST);
        return [
            'status' => $this->getStatus(),
            'errors' => $this->model->getErrors(),
        ];
    }
}
