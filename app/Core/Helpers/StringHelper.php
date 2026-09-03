<?php

namespace App\Core\Helpers;
class StringHelper {
    public static function clean($string) {
        $newString = preg_replace(array("/([áàãâä])/","/([ÁÀÃÂÄ])/","/([éèêë])/","/([ÉÈÊË])/","/([íìîï])/","/([ÍÌÎÏ])/","/([óòõôö])/","/([ÓÒÕÔÖ])/","/([úùûü])/","/([ÚÙÛÜ])/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"), $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $newString);
    }
}