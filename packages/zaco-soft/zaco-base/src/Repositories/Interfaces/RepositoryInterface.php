<?php

namespace ZacoSoft\ZacoBase\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    /**
     * @param $data
     * @param bool $isSingle
     * @return Builder
     */
    public function applyBeforeExecuteQuery($data, $isSingle = false);

    /**
     * Runtime override of the model.
     *
     * @param string $model
     * @return $this
     */
    public function setModel($model);

    /**
     * Get empty model.
     * @return \Eloquent|Model
     */
    public function getModel();

    /**
     * Get table name.
     *
     * @return string
     */
    public function getTable();

    /**
     * Make a new instance of the entity to query on.
     *
     * @param array $with
     */
    public function make(array $with = []);

    /**
     * Find a single entity by key value.
     *
     * @param array $condition
     * @param array $select
     * @param array $with
     * @return mixed
     */
    public function getFirstBy(array $condition = [], array $select = [], array $with = []);

    /**
     * Retrieve model by id regardless of status.
     *
     * @param $id
     * @param array $with
     * @return mixed
     */
    public function findById($id, array $with = []);

    /**
     * @param $id
     * @param array $with
     * @return mixed
     */
    public function findOrFail($id, array $with = []);

    /**
     * @param array $condition
     * @param array $with
     * @return mixed
     */
    public function firstOrFail(array $condition, array $with = []);

    /**
     * @param string $column
     * @param string $key
     * @return array
     */
    public function pluck($column, $key = null);

    /**
     * Get all models.
     *
     * @param array $with Eager load related models
     * @return Collection
     */
    public function all(array $with = []);

    /**
     * Get all models by key/value.
     *
     * @param array $condition
     * @param array $with
     * @param array $select
     *
     * @return Collection
     */
    public function allBy(array $condition, array $with = [], array $select = ['*']);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Create a new model.
     *
     * @param Model|array $data
     * @param array $condition
     * @return false|Model
     */
    public function createOrUpdate($data, $condition = []);

    /**
     * Delete model.
     *
     * @param Model $model
     * @return bool
     * @throws \Exception
     */
    public function delete(Model $model);

    /**
     * @param array $data
     * @param array $with
     * @return mixed
     */
    public function firstOrCreate(array $data, array $with = []);

    /**
     * @param array $condition
     * @param array $data
     * @return mixed
     */
    public function update(array $condition, array $data);

    /**
     * @param array $select
     * @param array $condition
     * @param array $orderBy
     * @return mixed
     */
    public function select(array $select = ['*'], array $condition = [], array $orderBy = []);

    /**
     * @param array $condition
     * @return mixed
     * @throws \Exception
     */
    public function deleteBy(array $condition = []);

    /**
     * @param array $condition
     * @return mixed
     */
    public function count(array $condition = []);

    /**
     * @param $column
     * @param array $value
     * @param array $args
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getByWhereIn($column, array $value = [], array $args = []);

    /**
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    public function advancedGet(array $params = []);

    /**
     * @param array $condition
     */
    public function forceDelete(array $condition = []);

    /**
     * @param array $condition
     * @return mixed
     */
    public function restoreBy(array $condition = []);

    /**
     * Find a single entity by key value.
     *
     * @param array $condition
     * @param array $select
     * @return mixed
     */
    public function getFirstByWithTrash(array $condition = [], array $select = []);

    /**
     * @param array $data
     * @return bool
     */
    public function insert(array $data);

    /**
     * @param array $condition
     * @return mixed
     */
    public function firstOrNew(array $condition);

    /**
     * attach default model instead attend in construct
     *
     * @param $model
     * @return mixed
     */
    public function attachModel(Model $model);

    /**
     * set pagination config
     *
     * @param array $pagination
     * @return mixed
     */
    public function setPagination(array $pagination);

    /**
     * @param int $page
     * @return mixed
     */
    public function setCurrentPaged(int $page);

    /**
     * @param array $data
     * @return mixed
     */
    public function setAdditional(array $data);

    /**
     * @param array $appends
     * @return mixed
     */
    public function setAppends($appends = []);
}
