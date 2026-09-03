<?php

namespace App\Core\Database\Model\Traits;
trait QueryBuilder {

    use SqlBuilder;
    protected array $selectedColumns = ['*'];
    protected array $whereConditions = [];
    protected array $bindValues = [];
    protected array $orderBy = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    public function buildWherePlaceholders(string $field, string|array $values): string|array {
        $cleanField = str_replace('.', '_', $field) . '_' . uniqid();
        if ($this->isRelation){
            $cleanField = "relation_".$this->table."_".$cleanField;
        }

        if (is_array($values)){
            $placeholders = [];
            foreach ($values as $index => $value) {
                $placeholder = $cleanField . "_" . $index;
                $placeholders[] = ":" . $placeholder;
                $this->bindValues[":" . $placeholder] = $value;
            }
        }else {
            $placeholder = $cleanField;
            $this->bindValues[':'.$placeholder] = $values;
        }


        return $placeholder ?? $placeholders;
    }

    public function _limit(int $limit): static {
        $this->limit = $limit;
        return $this;
    }

    public function _offset(int $offset): static {
        $this->offset = $offset;
        return $this;
    }

    public function _select(string|array $columns): static {
        $this->selectedColumns = is_string($columns) ? func_get_args() : $columns;
        return $this;
    }

    public function _where($field, $value, $operator = '='): static{
        $placeholder = $this->buildWherePlaceholders($field, $value);
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => strtoupper($operator),
            'method'      => 'AND',
            'placeholder' => $placeholder
        ];

        return $this;
    }

    public function _orWhere($field, $value, $operator = '='): static{
        $placeholder = $this->buildWherePlaceholders($field, $value);
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => strtoupper($operator),
            'method'      => 'OR',
            'placeholder' => $placeholder
        ];

        return $this;
    }

    public function _whereIn($field, array $values) : static{
        $placeholders = $this->buildWherePlaceholders($field, $values);
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'IN',
            'method'      => 'AND',
            'placeholder' => $placeholders
        ];

        return $this;
    }

    public function _orWhereIn($field, array $values) : static{
        $placeholders = $this->buildWherePlaceholders($field, $values);
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'IN',
            'method'      => 'OR',
            'placeholder' => $placeholders
        ];

        return $this;
    }

    public function _whereNotIn($field, array $values) : static{
        $placeholders = $this->buildWherePlaceholders($field, $values);
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'NOT IN',
            'method'      => 'AND',
            'placeholder' => $placeholders
        ];

        return $this;
    }

    public function _orWhereNotIn($field, array $values) : static{
        $placeholders = $this->buildWherePlaceholders($field, $values);
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'NOT IN',
            'method'      => 'OR',
            'placeholder' => $placeholders
        ];

        return $this;
    }

    public function _whereNull(string $field) : static{
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'IS NULL',
            'method'      => 'AND',
        ];

        return $this;
    }

    public function _orWhereNull(string $field) : static{
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'IS NULL',
            'method'      => 'OR',
        ];

        return $this;
    }

    public function _whereNotNull(string $field) : static{
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'IS NOT NULL',
            'method'      => 'AND',
        ];

        return $this;
    }

    public function _orWhereNotNull(string $field) : static{
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'IS NOT NULL',
            'method'      => 'OR',
        ];

        return $this;
    }

    public function _whereBetween(string $field, array $values) : static{
        $placeholders = $this->buildWherePlaceholders($field, $values);
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'BETWEEN',
            'placeholder' => $placeholders,
            'method'      => 'AND',
        ];

        return $this;
    }

    public function _orWhereBetween(string $field, array $values) : static{
        $placeholders = $this->buildWherePlaceholders($field, $values);
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'BETWEEN',
            'placeholder' => $placeholders,
            'method'      => 'OR',
        ];

        return $this;
    }

    public function _whereNotBetween(string $field, array $values) : static{
        $placeholders = $this->buildWherePlaceholders($field, $values);
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'NOT BETWEEN',
            'placeholder' => $placeholders,
            'method'      => 'AND',
        ];

        return $this;
    }

    public function _orWhereNotBetween(string $field, array $values) : static{
        $placeholders = $this->buildWherePlaceholders($field, $values);
        $this->whereConditions[] = [
            'type'        => 'basic',
            'field'       => $field,
            'operator'    => 'NOT BETWEEN',
            'placeholder' => $placeholders,
            'method'      => 'OR',
        ];

        return $this;
    }

    public function _whereRaw(string $sql, array $values = []) : static{
        foreach($values as $key => $value){
            $bindKey = str_starts_with($key, ':') ? $key : ':'.$key;
            $this->bindValues[$bindKey] = $value;
        }

        $this->whereConditions[] = [
            'type'   => 'raw',
            'sql'    => $sql,
            'method' => 'AND'
        ];

        return $this;
    }

    public function _orWhereRaw(string $sql, array $values = []) : static{
        foreach($values as $key => $value){
            $bindKey = str_starts_with($key, ':') ? $key : ':'.$key;
            $this->bindValues[$bindKey] = $value;
        }

        $this->whereConditions[] = [
            'type'   => 'raw',
            'sql'    => $sql,
            'method' => 'OR'
        ];

        return $this;
    }

    public function _whereGroup(\Closure $callback, string $method = 'AND'): static {
        $groupQuery = new static();
        $callback($groupQuery);
        $this->bindValues = array_merge($this->bindValues, $groupQuery->bindValues);
        $this->whereConditions[] = [
            'type'       => 'group',
            'method'     => strtoupper($method),
            'conditions' => $groupQuery->whereConditions
        ];

        return $this;
    }

    public function _orWhereGroup(\Closure $callback): static {
        return $this->_whereGroup($callback, 'OR');
    }

    public function _orderBy($field, $direction = 'ASC'): static{
        $this->orderBy[$field] = strtoupper($direction);

        return $this;
    }

    public function _with(string|array $relations): static{
        if(is_array($relations)){
            foreach($relations as $relation){
                if(str_contains($relation, '.')){
                    $subRelations = explode('.', $relation);
                    $this->relations[$subRelations[0]][] = $subRelations[1];
                }else if (!isset($this->relations[$relation])) {
                    $this->relations[$relation] = [];
                }
            }
        }else {
            $this->relations[$relations] = [];
        }

        return $this;
    }
}