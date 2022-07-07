<?php

namespace Dao\Mnt;

use Dao\Table;

class Personas extends Table
{

    public static function getAll()
    {
        $sqlstr = "Select * from personas;";
        return self::obtenerRegistros($sqlstr, array());
    }

    public static function getById(int $id)
    {
        $sqlstr = "Select * from personas where id = :id;";
        $sqlParams = array("id" => $id);
        return self::obtenerRegistros($sqlstr, $sqlParams);
    }


    public static function insert($identidad, $nombre, $edad)
    {
        $sqlstr = "INSERT INTO personas(identidad,nombre,edad) values (:identidad,:nombre,:edad)";
        $sqlParams = ["identidad" => $identidad, "nombre" => $nombre, "edad" => $edad];
        return self::executeNonQuery($sqlstr, $sqlParams);
    }


    public static function update($id, $identidad, $nombre, $edad)
    {
        $sqlstr = "update personas set `id` = :id,`identidad` = :identidad,`nombre` = :nombre,`edad` = :edad where id = :id";
        $sqlParams = ["id" => $id, "identidad" => $identidad, "nombre" => $nombre, "edad" => $edad];
        return self::executeNonQuery($sqlstr, $sqlParams);
    }

    public static function delete(int $id)
    {
        $sqlstr = "Delete from personas where id = :id;";
        $sqlParams = array("id" => $id);
        return self::executeNonQuery($sqlstr, $sqlParams);
    }
}
