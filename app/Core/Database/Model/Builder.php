<?php

namespace App\Core\Database\Model;

class Builder {
    public static function generateTableName(string $className): string{
        $className = explode("\\", $className);
        $tableName = end($className);
        if (!str_ends_with($tableName, 's')){
            $tableName = $tableName."s";
        }else if (!str_ends_with($tableName, 'y')){
            $tableName = substr($tableName, 0, -1)."ies";
        }

        $tableName = preg_split('/(?=[A-Z])/', $tableName, -1, PREG_SPLIT_NO_EMPTY);

        $tableName = implode('_', $tableName);

        return strtolower($tableName);
    }
}