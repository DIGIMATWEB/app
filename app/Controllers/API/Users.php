<?php
/*
 * This file is part of App Project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controllers\API;

use App\Models\Users as UsersModel;
use Framework\Crypto\Password;
use Framework\HTTP\Request;
use Framework\HTTP\Response;
use Framework\MVC\ModelInterface;
use Framework\Pagination\Pager;
use Framework\Routing\Attributes\Route;
use Framework\Routing\ResourceInterface;

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
    #[Route('GET', '/users', name: 'api.users.index')]
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
        return $this->respond([
            'data' => $entities,
            'links' => $this->model->getPager(),
        ]);
    }

    /**
     * @param string $id
     *
     * @return array<string,mixed>
     */
    #[Route('GET', '/users/{int}', name: 'api.users.show')]
    public function show(string $id) : array
    {
        $entity = $this->model->find($id);
        if ($entity) {
            return $this->respond([
                'data' => $entity,
            ]);
        }
        return $this->respondNotFound();
    }

    /**
     * @return array<string,mixed>
     */
    #[Route('POST', '/users', name: 'api.users.create')]
    public function create() : array
    {
        $data = $this->request->isJson()
            ? $this->request->getJson(true)
            : $this->request->getPost();
        if ($data === false) {
            return $this->respond(code: Response::CODE_UNPROCESSABLE_ENTITY);
        }
        if (isset($data['password']) && \is_string($data['password'])) {
            $data['password'] = Password::hash($data['password']);
        }
        $id = $this->model->create($data);
        if ($id === false) {
            return $this->respondErrors($this->model->getErrors());
        }
        $entity = $this->model->find($id);
        if ($entity) {
            return $this->respond([
                'data' => $entity,
            ], Response::CODE_CREATED);
        }
        return $this->respond(code: Response::CODE_PROCESSING);
    }

    /**
     * @param string $id
     *
     * @return array<string,mixed>
     */
    #[Route('PATCH', '/users/{int}', name: 'api.users.update')]
    public function update(string $id) : array
    {
        $data = $this->request->isJson()
            ? $this->request->getJson(true)
            : $this->request->getParsedBody();
        if ($data === false) {
            return $this->respond(code: Response::CODE_UNPROCESSABLE_ENTITY);
        }
        $entity = $this->model->find($id);
        if ( ! $entity) {
            return $this->respondNotFound();
        }
        if (isset($data['email']) && $data['email'] === $entity->email) {
            unset($data['email']);
        }
        if (isset($data['password']) && \is_string($data['password'])) {
            $data['password'] = Password::hash($data['password']);
        }
        $affectedRows = $this->model->update($id, $data);
        if ($affectedRows === false) {
            return $this->respondErrors($this->model->getErrors());
        }
        $entity = $this->model->find($id);
        if ($entity) {
            return $this->respond([
                'data' => $entity,
            ]);
        }
        return $this->respond(code: Response::CODE_PROCESSING);
    }

    /**
     * @param string $id
     *
     * @return array<string,mixed>
     */
    #[Route('PUT', '/users/{int}', name: 'api.users.replace')]
    public function replace(string $id) : array
    {
        $this->response->setHeader(Response::HEADER_ALLOW, \implode(', ', [
            Request::METHOD_DELETE,
            Request::METHOD_GET,
            Request::METHOD_HEAD,
            Request::METHOD_PATCH,
        ]));
        return $this->respond(code: Response::CODE_METHOD_NOT_ALLOWED);
    }

    /**
     * @param string $id
     *
     * @return array<string,mixed>
     */
    #[Route('DELETE', '/users/{int}', name: 'api.users.delete')]
    public function delete(string $id) : array
    {
        $entity = $this->model->find($id);
        if ( ! $entity) {
            return $this->respondNotFound();
        }
        $this->model->delete($id);
        return $this->respond();
    }
}
