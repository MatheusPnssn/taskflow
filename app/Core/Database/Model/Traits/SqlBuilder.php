<?php

namespace App\Core\Database\Model\Traits;
trait SqlBuilder{
    const string initialSelector = "SELECT";
    const string initialDeleter = "DELETE";
    const string initialUpdater = "UPDATE";
    const string initialInsert = "INSERT INTO";
    const array avaiableOrders = ['ASC', 'DESC'];
    public function buildSql(): string{
        $sqlBase = "";

        if(!empty($this->relations)){
            foreach($this->relations as $relation => $subRelations){
                $relationInstance = $this->$relation();
                $sqlBase .= $this->buildLeftJoinSql($relationInstance);

                if (!empty($subRelations)) {
                    foreach ($subRelations as $subRelationMethod) {
                        $subRelationInstance = $relationInstance->$subRelationMethod();
                        $sqlBase .= $this->buildLeftJoinSql($subRelationInstance, $relationInstance);
                    }
                }
            }
        }

        if(!empty($this->whereConditions)){
            $sqlBase .= " WHERE ".$this->buildWhereSql($this->whereConditions);
        }

        if(!empty($this->orderBy)){
            $sqlBase .= "ORDER BY ";

            foreach($this->orderBy as $field => $value){
                if(!in_array($value, self::avaiableOrders)){
                    print_r($value);
                    die;
                }
                $sqlBase .= $field." ".$value.", ";
            }

            $sqlBase = substr($sqlBase, 0, -2);
        }

        return $sqlBase;
    }

    public function buildWhereSql(array $whereConditions): string
    {
        $sqlBase = "";
        $isFirst = true;

        foreach($whereConditions as $condition){
            $method = $isFirst ? "" : " " . $condition['method'] . " ";

            if ($condition['type'] === 'group') {
                $groupSql = $this->buildWhereSql($condition['conditions']);
                if (!empty($groupSql)) {
                    $sqlBase .= $method . "(" . $groupSql . ")";
                    $isFirst = false;
                }
                continue;
            }

            if ($condition['type'] === 'raw') {
                $sqlBase .= $method . $condition['sql'];
                $isFirst = false;
                continue;
            }

            $field = $condition['field'];
            $operator = $condition['operator'];

            if ($operator === 'IS NULL' || $operator === 'IS NOT NULL') {
                $sqlBase .= $method . $field . " " . $operator;
            } elseif ($operator === 'IN' || $operator === 'NOT IN') {
                $placeholdersStr = implode(', ', $condition['placeholder']);
                $sqlBase .= $method . $field . " " . $operator . " (" . $placeholdersStr . ")";
            } elseif ($operator === 'BETWEEN' || $operator === 'NOT BETWEEN') {
                $sqlBase .= $method . $field . " " . $operator . " " . $condition['placeholder'][0] . " AND " . $condition['placeholder'][1];
            } else {
                $sqlBase .= $method . $field . " " . $operator . " :" . $condition['placeholder'];
            }

            $isFirst = false;
        }

        return $sqlBase;
    }

    public function buildLeftJoinSql($relationInstance, $mainRelationInstance = null): string {
        $sqlBase = "";

        $this->tableRelationClass[$relationInstance->table] = get_class($relationInstance);
        $this->tableHierarchy[$relationInstance->table] = $mainRelationInstance ? $mainRelationInstance->table : $this->table;

        $sqlBase .= "LEFT JOIN " . $relationInstance->relationHelperSql;

        if (!empty($relationInstance->whereConditions)) {
            $sqlBase .= " AND ".$this->buildWhereSql($relationInstance->whereConditions );
            $this->bindValues = array_merge($this->bindValues, $relationInstance->bindValues);
        }

        return $sqlBase .= ") ";
    }

    public function selectSql(): string{
        $columns = implode(', ', $this->selectedColumns);
        $finalSql = trim(self::initialSelector.' '.$columns.' FROM '.$this->table.' '.$this->buildSql());

        if ($this->limit !== null) {
            $finalSql .= " LIMIT " . $this->limit;
        }

        if ($this->offset !== null) {
            $finalSql .= " OFFSET " . $this->offset;
        }

        return trim($finalSql) . ';';
    }

    public function insertSql(array $data): string{
        $fields = implode(", ", array_keys($data));

        $placeholders = "";
        foreach($data as $field => $value){
            $placeholders .= ":".$field.", ";
        }
        $placeholders = substr($placeholders, 0, -2);

        return self::initialInsert." ".$this->table." (".$fields.") VALUES (".$placeholders.");";
    }

    public function updateSql(array $data): string{
        $setStr = "";
        foreach($data as $field => $value){
            $setStr .= $field." = :update_".$field.", ";
        }
        $setStr = substr($setStr, 0, -2);

        $finalSql = trim(self::initialUpdater.' '.$this->table.' SET '.$setStr.' '.$this->buildSql());
        return $finalSql.';';
    }

    public function deleteSql(): string{
        $finalSql = trim(self::initialDeleter.' FROM '.$this->table.' '.$this->buildSql());
        return $finalSql.';';
    }
}