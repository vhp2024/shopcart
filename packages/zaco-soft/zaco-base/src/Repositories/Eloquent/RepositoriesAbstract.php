<?php

namespace ZacoSoft\ZacoBase\Repositories\Eloquent;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use ZacoSoft\ZacoBase\Libraries\Date;
use ZacoSoft\ZacoBase\Repositories\Interfaces\RepositoryInterface;

abstract class RepositoriesAbstract implements RepositoryInterface
{
    /**
     * @var Eloquent | Model
     */
    protected $model;

    /**
     * @var Eloquent | Model
     */
    protected $originalModel;

    /**
     * @var $pagination
     */
    protected $pagination;

    /**
     * @var int $currentPaged
     */
    protected $currentPaged = 1;

    /**
     * additional data for params
     * @var array $additional[]
     */
    protected $additional = [];

    /**
     * RepositoriesAbstract constructor.
     * @param Model|Eloquent $model
     */
    public function __construct(Model $model)
    {
        $this->attachModel($model);
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return $this->model->getTable();
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id, array $with = [])
    {
        $data = $this->make($with)->where($this->model->getKeyName(), $id);
        $data = $this->applyBeforeExecuteQuery($data, true);
        $data = $data->first();

        $this->resetModel();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function make(array $with = [])
    {
        if (!empty($with)) {
            $this->model = $this->model->with($with);
        }

        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function applyBeforeExecuteQuery($data, $isSingle = false)
    {
        $this->resetModel();

//        $data = apply_filters($data, $this->originalModel);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function resetModel()
    {
        $this->model = new $this->originalModel;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail($id, array $with = [])
    {
        $data = $this->make($with)->where($this->model->getKeyName(), $id);
        $data = $this->applyBeforeExecuteQuery($data, true);
        $result = $data->first();
        $this->resetModel();

        if (!empty($result)) {
            return $result;
        }

        throw (new ModelNotFoundException)->setModel(
            get_class($this->originalModel),
            $id
        );
    }

    /**
     * {@inheritdoc}
     */
    public function firstOrFail(array $condition, array $with = [])
    {
        if (!empty($condition)) {
            $this->applyConditions($condition);
        }

        $data = $this->make($with);
        $data = $this->applyBeforeExecuteQuery($data, true);
        $result = $data->first();
        $this->resetModel();

        if (!empty($result)) {
            return $result;
        }

        throw (new ModelNotFoundException)->setModel(
            get_class($this->originalModel),
            $condition
        );
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $with = [])
    {
        $data = $this->make($with);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function pluck($column, $key = null)
    {
        $select = [$column];
        if (!empty($key)) {
            $select = [$column, $key];
        }

        $data = $this->model->select($select);

        return $this->applyBeforeExecuteQuery($data)->pluck($column, $key)->all();
    }

    /**
     * {@inheritdoc}
     */
    public function allBy(array $condition, array $with = [], array $select = ['*'])
    {
        if (!empty($condition)) {
            $this->applyConditions($condition);
        }

        $data = $this->make($with)->select($select);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * @param array $where
     * @param null|Eloquent|Builder $model
     */
    protected function applyConditions(array $where, &$model = null)
    {
        if (!$model) {
            $newModel = $this->model;
        } else {
            $newModel = $model;
        }
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                switch (strtoupper($condition)) {
                    case 'IN':
                        $newModel = $newModel->whereIn($field, $val);
                        break;
                    case 'NOT_IN':
                        $newModel = $newModel->whereNotIn($field, $val);
                        break;
                    case 'SEARCH':
                        // $field is array [field1, field2..]
                        $newModel = $newModel->whereLike($field, $val);
                        break;
                    default:
                        $newModel = $newModel->where($field, $condition, $val);

                        break;
                }
            } else {
                if ($value === null) {
                    $newModel = $newModel->whereRaw($field);
                } else {
                    $newModel = $newModel->where($field, '=', $value);
                }
            }
        }
        if (!$model) {
            $this->model = $newModel;
        } else {
            $model = $newModel;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $fields = array_merge($this->model->getFillable(), $this->model->getHidden());
        if (in_array('created_user_id', $fields)) {
            $data['created_user_id'] = \Auth::user()->id;
        }
        $data = $this->model->create($data);

        $this->resetModel();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function createOrUpdate($data, $condition = [])
    {
        /**
         * @var Model $item
         */
        if (is_array($data)) {
            if (empty($condition)) {
                $item = new $this->model;
            } else {
                $item = $this->getFirstBy($condition);
            }
            if (empty($item)) {
                $item = new $this->model;
            }

            $item = $item->fill($data);
        } elseif ($data instanceof Model) {
            $item = $data;
        } else {
            return false;
        }

        if ($item->save()) {
            $this->resetModel();
            return $item;
        }

        $this->resetModel();

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstBy(array $condition = [], array $select = ['*'], array $with = [])
    {
        $this->make($with);

        if (!empty($select)) {
            $data = $this->model->where($condition)->select($select);
        } else {
            $data = $this->model->where($condition);
        }

        return $this->applyBeforeExecuteQuery($data, true)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function softDelete(Model $model)
    {
        $fields = array_merge($this->model->getFillable(), $this->model->getHidden());
        if (in_array('deleted_user_id', $fields)) {
            $model->deleted_user_id = \Auth::user()->id;
        }

        if (in_array('deleted_at', $fields)) {
            $model->deleted_at = Date::now();
        }

        return $model->update();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Model $model)
    {
        $fields = array_merge($this->model->getFillable(), $this->model->getHidden());

        return $model->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function firstOrCreate(array $data, array $with = [])
    {
        $data = $this->model->firstOrCreate($data, $with);

        $this->resetModel();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $condition, array $data)
    {
        $this->applyConditions($condition);
        $data = $this->model->update($data);

        $this->resetModel();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function select(array $select = ['*'], array $condition = [], array $orderBy = [])
    {
        $data = $this->model->where($condition)->select($select);

        foreach ($orderBy as $column => $direction) {
            if ($direction !== null) {
                $data = $data->orderBy($column, $direction);
            }
        }

        return $this->applyBeforeExecuteQuery($data);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteBy(array $condition = [])
    {
        $this->applyConditions($condition);

        $data = $this->model->get();

        if (empty($data)) {
            return false;
        }
        foreach ($data as $item) {
            $item->delete();
        }

        $this->resetModel();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function count(array $condition = [])
    {
        $this->applyConditions($condition);
        $data = $this->model->count();

        $this->resetModel();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getByWhereIn($column, array $value = [], array $args = [])
    {
        $data = $this->model->whereIn($column, $value);

        if (!empty(Arr::get($args, 'where'))) {
            $this->applyConditions($args['where'], $data);
        }

        $data = $this->applyBeforeExecuteQuery($data);

        if (!empty(Arr::get($args, 'paginate'))) {
            return $data->paginate($args['paginate']);
        } elseif (!empty(Arr::get($args, 'limit'))) {
            return $data->limit($args['limit']);
        }

        return $data->get();
    }

    /**
     * {@inheritdoc}
     */
    public function advancedGet(array $params = [])
    {
        $params = array_merge([
            'condition' => [],
            'order_by' => [],
            'group_by' => null,
            'take' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => null,
            ],
            'select' => ['*'],
            'distinct' => false,
            'from' => null, // String: tablename as alias
            'with' => [],
            'join' => null, // ['leftJoin', function(){}]
        ], $params);

        if (!empty($this->pagination) && !$params['paginate']['per_page']) {
            $params['paginate'] = $this->pagination;
        }

        $this->applyConditions($params['condition']);
        $data = $this->model;

        // Select distinct
        if (isset($params['distinct']) && $params['distinct']) {
            $data->distinct();
        }

        // Set alias table
        if ($params['from'] && $params['from'] != '' && $params['from'] != null) {
            $data->from($params['from']);
        }

        if ($params['select']) {
            if (is_array($params['select'])) {
                $data = $data->select($params['select']);
            } else {
                $data = $data->select(DB::raw($params['select']));
            }
        }

        if ($params['join']) {
            foreach ($params['join'] as $join) {
                list($joinType, $joinCondition, $join) = $join;
                $data = $data->{$joinType}($joinCondition ?? null, $join ?? null);
            }
        }

        foreach ($params['order_by'] as $column => $direction) {
            if ($direction !== null) {
                $data = $data->orderBy($column, $direction);
            }
        }
        if ($params['group_by']) {
            // disable strict mode
            $data = $data->groupBy($params['group_by']);
        }
        foreach ($params['with'] as $with) {
            $data = $data->with($with);
        }

        if ($params['take'] == 1) {
            $result = $this->applyBeforeExecuteQuery($data, true)->first();
        } elseif ($params['take']) {
            $result = $this->applyBeforeExecuteQuery($data)->take($params['take'])->get();
        } elseif ($params['paginate'] && $params['paginate']['per_page']) {
            $paginate_type = 'paginate';
            if (Arr::get($params, 'paginate.type') && method_exists($data, Arr::get($params, 'paginate.type'))) {
                $paginate_type = Arr::get($params, 'paginate.type');
            }
            $result = $this->applyBeforeExecuteQuery($data)
                ->$paginate_type(
                    Arr::get($params, 'paginate.per_page', 10),
                    [$this->originalModel->getTable() . '.' . $this->originalModel->getKeyName()],
                    'page',
                    Arr::get($params, 'paginate.current_paged', $this->currentPaged)
                );

            // try to reformat pagination result
            try {
                $result = [
                    'rows' => $result->getCollection(),
                    'pagination' => [
                        'total' => $result->total(),
                        'per_page' => $result->perPage(),
                        'current_page' => $result->currentPage(),
                        'last_page' => $result->lastPage(),
                        'from' => $result->firstItem(),
                        'to' => $result->lastItem(),
                    ],
                ];
            } catch (ModelNotFoundException $e) {}
        } else {
            $result = $this->applyBeforeExecuteQuery($data)->get();
        }

        return $result;
    }

    public function getList($conditions, $options = [])
    {
        $select = isset($options['select']) ? $options['select'] : ['*'];
        $orderBy = isset($options['orderBy']) ? $options['orderBy'] : [];
        $page = isset($options['page']) ? $options['page'] : 1;
        $limit = isset($options['limit']) ? $options['limit'] : 20;

        $params = [
            'select' => $select,
            'condition' => $conditions,
            'order_by' => $orderBy,
            'paginate' => [
                'per_page' => $limit,
                'current_paged' => $page,
            ],
        ];

        return $this->advancedGet($params);
    }

    /**
     * {@inheritdoc}
     */
    public function forceDelete(array $condition = [])
    {
        $item = $this->model->where($condition)->withTrashed()->first();
        if (!empty($item)) {
            $item->forceDelete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function restoreBy(array $condition = [])
    {
        $item = $this->model->where($condition)->withTrashed()->first();
        if (!empty($item)) {
            $item->restore();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstByWithTrash(array $condition = [], array $select = [])
    {
        $query = $this->model->where($condition)->withTrashed();

        if (!empty($select)) {
            return $query->select($select)->first();
        }

        return $this->applyBeforeExecuteQuery($query, true)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    /**
     * {@inheritdoc}
     */
    public function firstOrNew(array $condition)
    {
        $this->applyConditions($condition);

        $result = $this->model->first() ?: new $this->originalModel;

        $this->resetModel();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function attachModel(Model $model)
    {
        $this->model = $model;
        $this->originalModel = $model;
    }

    public function setPagination(array $pagination): self
    {
        if (!empty($pagination)) {
            $array = [];
            if (isset($pagination['per_page'])) {
                $array['per_page'] = $pagination['per_page'];
            }

            if (isset($pagination['page'])) {
                $array['current_paged'] = $pagination['page'];
            }

            $this->pagination = $array;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentPaged(int $page): self
    {
        $this->currentPaged = $page;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditional(array $data): self
    {
        $this->additional = $data;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAppends($appends = [])
    {
        $classModel = get_class($this->getModel());
        call_user_func($classModel . '::setExtendAppends', $appends);

        return $this;
    }
}
