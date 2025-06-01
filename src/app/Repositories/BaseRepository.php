<?php

namespace App\Repositories;

use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->makeModel();
    }

    abstract public function model();

    public function makeModel()
    {
        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    public function all(array $columns = ['*'])
    {
        return $this->model->get($columns);
    }

    public function find($id, array $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->model->find($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    public function findBy(array $criteria, array $columns = ['*'])
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get($columns);
    }

    public function paginate($perPage = 15, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    public function latest($limit = 10, array $columns = ['*'])
    {
        return $this->model->latest($limit)->get($columns);
    }
}
