<?php
namespace Dao\Mnt;
use Dao\Table;

class Productos extends Table{

public static function getAll(){
$sqlstr = "Select * from productos;";
return self::obtenerRegistros($sqlstr, array());
}

public static function getById(int $invPrdId){
$sqlstr = "Select * from productos where invPrdId = :invPrdId;";
$sqlParams = array("invPrdId" => $invPrdId);
return self::obtenerRegistros($sqlstr, $sqlParams);
}


public static function insert($invPrdBrCod,$invPrdCodInt,$invPrdDsc,$invPrdTip,$invPrdEst,$invPrdPadre,$invPrdFactor,$invPrdVnd,$invPrdPrecioVenta,$invPrdPrecioCompra,$invPrdStock){
$sqlstr = "INSERT INTO productos($invPrdBrCod,$invPrdCodInt,$invPrdDsc,$invPrdTip,$invPrdEst,$invPrdPadre,$invPrdFactor,$invPrdVnd,$invPrdPrecioVenta,$invPrdPrecioCompra,$invPrdStock) values (:invPrdBrCod,:invPrdCodInt,:invPrdDsc,:invPrdTip,:invPrdEst,:invPrdPadre,:invPrdFactor,:invPrdVnd,:invPrdPrecioVenta,:invPrdPrecioCompra,:invPrdStock)";
$sqlParams = ["invPrdBrCod"=> $invPrdBrCod,"invPrdCodInt"=> $invPrdCodInt,"invPrdDsc"=> $invPrdDsc,"invPrdTip"=> $invPrdTip,"invPrdEst"=> $invPrdEst,"invPrdPadre"=> $invPrdPadre,"invPrdFactor"=> $invPrdFactor,"invPrdVnd"=> $invPrdVnd,"invPrdPrecioVenta"=> $invPrdPrecioVenta,"invPrdPrecioCompra"=> $invPrdPrecioCompra,"invPrdStock"=> $invPrdStock];
return self::executeNonQuery($sqlstr, $sqlParams);
}


public static function update($invPrdId,$invPrdBrCod,$invPrdCodInt,$invPrdDsc,$invPrdTip,$invPrdEst,$invPrdPadre,$invPrdFactor,$invPrdVnd,$invPrdPrecioVenta,$invPrdPrecioCompra,$invPrdStock){
$sqlstr = "update productos set `invPrdId` = :invPrdId,`invPrdBrCod` = :invPrdBrCod,`invPrdCodInt` = :invPrdCodInt,`invPrdDsc` = :invPrdDsc,`invPrdTip` = :invPrdTip,`invPrdEst` = :invPrdEst,`invPrdPadre` = :invPrdPadre,`invPrdFactor` = :invPrdFactor,`invPrdVnd` = :invPrdVnd,`invPrdPrecioVenta` = :invPrdPrecioVenta,`invPrdPrecioCompra` = :invPrdPrecioCompra,`invPrdStock` = :invPrdStock where invPrdId = :invPrdId";
$sqlParams = ["invPrdId"=>$invPrdId,"invPrdBrCod"=>$invPrdBrCod,"invPrdCodInt"=>$invPrdCodInt,"invPrdDsc"=>$invPrdDsc,"invPrdTip"=>$invPrdTip,"invPrdEst"=>$invPrdEst,"invPrdPadre"=>$invPrdPadre,"invPrdFactor"=>$invPrdFactor,"invPrdVnd"=>$invPrdVnd,"invPrdPrecioVenta"=>$invPrdPrecioVenta,"invPrdPrecioCompra"=>$invPrdPrecioCompra,"invPrdStock"=>$invPrdStock];
return self::executeNonQuery($sqlstr, $sqlParams);
}

public static function delete(int $invPrdId){
$sqlstr = "Delete from productos where invPrdId = :invPrdId;";
$sqlParams = array("invPrdId" => $invPrdId);
return self::executeNonQuery($sqlstr, $sqlParams);
}

}
?>