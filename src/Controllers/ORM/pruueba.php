<?php

namespace Controllers\Mnt;

use Controllers\PublicController;
use Views\Renderer;
use Utilities\Validators;
use Dao\Mnt\Productos;

class Producto extends PublicController
{

    private $viewData = array();
    private $arrModeDesc = array();
    private $arrEstados = array();
    public function run(): void
    {
        $this->init();
        if (!$this->isPostBack()) {
            $this->procesarGet();
        }
        if ($this->isPostBack()) {
            $this->procesarPost();
        }
        $this->processView();
        Renderer::render("mnt/producto", $this->viewData);
    }
    private function init()
    {
        $this->viewData["mode"] = "";
        $this->viewData["mode_desc"] = "";
        $this->viewData["crsf_token"] = "";
        $this->viewData["invPrdId"] = 0;
        $this->viewData["invPrdBrCod"] = "";
        $this->viewData["invPrdCodInt"] = "";
        $this->viewData["invPrdDsc"] = "";
        $this->viewData["invPrdTip"] = "";
        $this->viewData["invPrdEst"] = "";
        $this->viewData["invPrdPadre"] = 0;
        $this->viewData["invPrdFactor"] = 0;
        $this->viewData["invPrdVnd"] = "";
        $this->viewData["invPrdPrecioVenta"] = "";
        $this->viewData["invPrdPrecioCompra"] = "";
        $this->viewData["invPrdStock"] = 0;
        $this->viewData["error_invPrdBrCod"] = array();
        $this->viewData["error_invPrdCodInt"] = array();
        $this->viewData["error_invPrdDsc"] = array();
        $this->viewData["btnEnviarText"] = "Guardar";
        $this->viewData["readonly"] = false;
        $this->viewData["showBtn"] = true;
        $this->arrModeDesc = array(
            "INS" => "Nuevo Producto",
            "UPD" => "Editando %s %s",
            "DSP" => "Detalle de %s %s",
            "DEL" => "Eliminado %s %s"
        );
        $this->arrEstados = array(
            array("value" => "ACT", "text" => "Activo"),
            array("value" => "INA", "text" => "Inactivo"),
        );
    }
    private function procesarGet()
    {
        if (isset($_GET["mode"])) {
            $this->viewData["mode"] = $_GET["mode"];
            if (!isset($this->arrModeDesc[$this->viewData["mode"]])) {
                error_log("Error: (Productos) Mode solicitado no existe.");
                \Utilities\Site::redirectToWithMsg(
                    "index.php?page=Mnt_Productos",
                    "No se puede procesar su solicitud!"
                );
            }
        }
        if ($this->viewData["mode"] !== "INS" && isset($_GET["id"])) {
            $this->viewData["invPrdId"] = intval($_GET["id"]);
            $tmpProducto = Productos::getById($this->viewData["invPrdId"]);
            error_log(json_encode($tmpProducto));
            \Utilities\ArrUtils::mergeFullArrayTo($tmpProducto, $this->viewData);
        }
    }
    private function procesarPost()
    {

        $hasErrors = false;
        \Utilities\ArrUtils::mergeArrayTo($_POST, $this->viewData);
        if (
            isset($_SESSION[$this->name . "crsf_token"])
            && $_SESSION[$this->name . "crsf_token"] !== $this->viewData["crsf_token"]
        ) {
            \Utilities\Site::redirectToWithMsg(
                "index.php?page=mnt_productos",
                "ERROR: Algo inesperado sucedió con la petición Intente de nuevo."
            );
        }

        if (Validators::IsEmpty($this->viewData["invPrdBrCod"])) {
            $this->viewData["error_invPrdBrCod"][]
                = "El campo invPrdBrCod es requerido";
            $hasErrors = true;
        }
        if (Validators::IsEmpty($this->viewData["invPrdCodInt"])) {
            $this->viewData["error_invPrdCodInt"][]
                = "El campo invPrdCodInt es requerido";
            $hasErrors = true;
        }
        if (Validators::IsEmpty($this->viewData["invPrdDsc"])) {
            $this->viewData["error_invPrdDsc"][]
                = "El campo invPrdDsc es requerido";
            $hasErrors = true;
        }
        error_log(json_encode($this->viewData));
        if (!$hasErrors) {
            $result = null;
            switch ($this->viewData["mode"]) {
                case "INS":
                    $result = Productos::insert($this->viewData["invPrdBrCod"], $this->viewData["invPrdCodInt"], $this->viewData["invPrdDsc"], $this->viewData["invPrdTip"], $this->viewData["invPrdEst"], $this->viewData["invPrdPadre"], $this->viewData["invPrdFactor"], $this->viewData["invPrdVnd"], $this->viewData["invPrdPrecioVenta"], $this->viewData["invPrdPrecioCompra"], $this->viewData["invPrdStock"]);
                    if ($result) {
                        \Utilities\Site::redirectToWithMsg(
                            "index.php?page=mnt_productos",
                            "Producto Guardado Satisfactoriamente!"
                        );
                    }
                    break;
                case "UPD":
                    $result = Productos::update(intval($this->viewData["invPrdId"]), $this->viewData["invPrdBrCod"], $this->viewData["invPrdCodInt"], $this->viewData["invPrdDsc"], $this->viewData["invPrdTip"], $this->viewData["invPrdEst"], $this->viewData["invPrdPadre"], $this->viewData["invPrdFactor"], $this->viewData["invPrdVnd"], $this->viewData["invPrdPrecioVenta"], $this->viewData["invPrdPrecioCompra"], $this->viewData["invPrdStock"]);
                    if ($result) {
                        \Utilities\Site::redirectToWithMsg(
                            "index.php?page=mnt_productos",
                            "Producto Actualizado Satisfactoriamente!"
                        );
                    }
                    break;
                case "DEL":
                    $result = Productos::delete(intval($this->viewData["invPrdId"]));
                    if ($result) {
                        \Utilities\Site::redirectToWithMsg(
                            "index.php?page=mnt_productos",
                            "Producto Eliminado Satisfactoriamente!"
                        );
                    }
                    break;
            }
        }
    }

    private function processView()
    {
        if ($this->viewData["mode"] === "INS") {
            $this->viewData["mode_desc"]  = $this->arrModeDesc["INS"];
            $this->viewData["btnEnviarText"] = "Guardar Nuevo";
        } else {
            $this->viewData["mode_desc"]  = sprintf(
                $this->arrModeDesc[$this->viewData["mode"]],
                $this->viewData["invPrdId"],
                $this->viewData["invPrdBrCod"]
            );
            if ($this->viewData["mode"] === "DSP") {
                $this->viewData["readonly"] = true;
                $this->viewData["showBtn"] = false;
            }
            if ($this->viewData["mode"] === "DEL") {
                $this->viewData["readonly"] = true;
                $this->viewData["btnEnviarText"] = "Eliminar";
            }
            if ($this->viewData["mode"] === "UPD") {
                $this->viewData["btnEnviarText"] = "Actualizar";
            }
            $this->viewData["crsf_token"] = md5(getdate()[0] . $this->name);
            $_SESSION[$this->name . "crsf_token"] = $this->viewData["crsf_token"];
        }
    }
}
