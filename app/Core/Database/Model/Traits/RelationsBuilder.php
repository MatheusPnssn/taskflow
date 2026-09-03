<?php
namespace App\Core\Database\Model\Traits;

trait RelationsBuilder {

    protected array $relationsData = [];
    protected array $relations = [];
    protected array $tableRelationClass = [];
    protected string $relationHelperSql = "";
    protected array $tableHierarchy = [];
    public function _belongsTo(){

    }

    public function _belongsToMany(){

    }

    public function _belongsToManyThrough(){

    }

    public function _hasOne(){

    }

    public function _hasMany(string $model, $foreignKey = null, $localKey = "id"){
        $instance = new $model();
        $instance->isRelation = true;
        $instance->relationHelperSql = $instance->table." ON (".$this->table.".".$localKey." = ".$instance->table.".".$foreignKey;

        $this->tableRelationClass[$instance->table] = $model;

        return $instance;
    }

    public function _hasManyThrough(){

    }
}