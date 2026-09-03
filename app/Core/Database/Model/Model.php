<?php

namespace App\Core\Database\Model;

use App\Core\Database\Model\Traits\QueryBuilder;
use App\Core\Database\Connection;
use App\Core\Database\Model\Traits\RelationsBuilder;
use PDO;

abstract class Model {

    use QueryBuilder, RelationsBuilder;
    protected Connection $connection;
    protected string $table = "";
    protected array $attributes;
    protected bool $isRelation = false;
    function __construct() {
        $this->connection = new Connection();

        if ($this->table == "") {
            $this->table = strtolower(Builder::generateTableName(get_called_class()));
        }

    }
    public static function __callStatic($name, $args) {
        $instance = new static();
        $function = '_'.$name;

        return $instance->$function(...$args);
    }

    public function __call($name, $args) {
        $function = '_'.$name;

        return $this->$function(...$args);
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function __get($name) {
        return $this->attributes[$name] ?? $this->relationsData[$name] ?? null;
    }

    public function cleanConditions() {
        $this->whereConditions = [];
        $this->bindValues = [];
    }

    protected function buildData($stmt): array {
        $columnsMeta = [];
        for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $columnsMeta[$i] = $stmt->getColumnMeta($i);
        }

        $instances = [];

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $mainData = [];
            $relationData = [];

            foreach ($columnsMeta as $i => $meta) {
                $table  = $meta['table'] ?: $this->table;
                $column = $meta['name'];

                if ($table === $this->table) {
                    $mainData[$column] = $row[$i];
                } else {
                    $relationData[$table][$column] = $row[$i];
                }
            }

            $pk = $mainData['id'];

            if (!isset($instances[$pk])) {
                $instance = new static();
                foreach ($mainData as $field => $value) {
                    $instance->$field = $value;
                }
                $instances[$pk] = $instance;
            }

            foreach ($relationData as $table => $data) {
                if (array_filter($data) !== []) {
                    $relationModel = new $this->tableRelationClass[$table]();
                    foreach ($data as $field => $value) {
                        $relationModel->$field = $value;
                    }

                    $instances[$pk]->relationsData[$table][] = $relationModel;
                }
            }
        }

        return $instances;
    }

    public function _first() {
        $this->_limit(1);

        $results = $this->_get();

        return !empty($results) ? $results[0] : null;
    }

    public function _find($id) {
        return $this->_where('id', $id)->_first();
    }

    public function _all(){
        $this->cleanConditions();

        $stmt = $this->connection->pdo()->prepare($this->selectSql());
        $stmt->execute($this->bindValues);

        $instances = $this->buildData($stmt);

        return array_values($instances);
    }

    public function _get(){
        $stmt = $this->connection->pdo()->prepare($this->selectSql());
        $stmt->execute($this->bindValues);
        $instances = $this->buildData($stmt);

        return array_values($instances);
    }

    public function _paginate(int $limit) {
        $this->_limit($limit);

        $stmt = $this->connection->pdo()->prepare($this->selectSql());
        $stmt->execute($this->bindValues);
        $instances = $this->buildData($stmt);

        return array_values($instances);
    }

    public function _create(array $data){
        $stmt = $this->connection->pdo()->prepare($this->insertSql($data));

        $binds = [];
        foreach($data as $field => $value){
            $binds[':'.$field] = $value;
        }

        return $stmt->execute($binds);
    }

    public function _update(array $data){
        $stmt = $this->connection->pdo()->prepare($this->updateSql($data));

        $binds = $this->bindValues;
        foreach($data as $field => $value){
            $binds[':update_'.$field] = $value;
        }

        return $stmt->execute($binds);
    }

    public function _delete(){
        $stmt = $this->connection->pdo()->prepare($this->deleteSql());

        return $stmt->execute($this->bindValues);
    }
}