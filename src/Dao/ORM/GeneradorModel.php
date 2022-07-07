<?php

namespace Dao\ORM;

use Dao\Table;

class GeneradorModel extends Table
{
    public static function obtenerEstructuraDeTabla($tableName)
    {
        $sqlstr = "desc ${tableName};";
        return self::obtenerRegistros(
            $sqlstr,
            array()
        );
    }

}