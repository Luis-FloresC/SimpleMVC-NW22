<?php

namespace Controllers\Mnt;

use Controllers\PublicController;
use Views\Renderer;
use Utilities\Validators;
use Dao\Mnt\Personas;

class Persona extends PublicController
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
        Renderer::render("mnt/persona", $this->viewData);
    }
    private function init()
    {
        $this->viewData["mode"] = "";
        $this->viewData["mode_desc"] = "";
        $this->viewData["crsf_token"] = "";
        $this->viewData["id"] = 0;
        $this->viewData["identidad"] = "";
        $this->viewData["nombre"] = "";
        $this->viewData["edad"] = 0;
        $this->viewData["error_identidad"] = array();
        $this->viewData["error_nombre"] = array();
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
                error_log("Error: (Personas) Mode solicitado no existe.");
                \Utilities\Site::redirectToWithMsg(
                    "index.php?page=mnt_personas",
                    "No se puede procesar su solicitud!"
                );
            }
        }
        if ($this->viewData["mode"] !== "INS" && isset($_GET["id"])) {
            $this->viewData["id"] = intval($_GET["id"]);
            $tmpPersona = Personas::getById($this->viewData["id"]);
            error_log(json_encode($tmpPersona));
            \Utilities\ArrUtils::mergeFullArrayTo($tmpPersona, $this->viewData);

            $this->viewData["id"] = $tmpPersona[0]["id"];
            $this->viewData["identidad"] = $tmpPersona[0]["identidad"];
            $this->viewData["nombre"] = $tmpPersona[0]["nombre"];
            $this->viewData["edad"] = $tmpPersona[0]["edad"];
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
                "index.php?page=mnt_personas",
                "ERROR: Algo inesperado sucedió con la petición Intente de nuevo."
            );
        }

        if (Validators::IsEmpty($this->viewData["identidad"])) {
            $this->viewData["error_identidad"][]
                = "El campo identidad es requerido";
            $hasErrors = true;
        }
        if (Validators::IsEmpty($this->viewData["nombre"])) {
            $this->viewData["error_nombre"][]
                = "El campo nombre es requerido";
            $hasErrors = true;
        }
        error_log(json_encode($this->viewData));
        if (!$hasErrors) {
            $result = null;
            switch ($this->viewData["mode"]) {
                case "INS":
                    $result = Personas::insert($this->viewData["identidad"], $this->viewData["nombre"], $this->viewData["edad"]);
                    if ($result) {
                        \Utilities\Site::redirectToWithMsg(
                            "index.php?page=mnt_personas",
                            "Persona Guardado Satisfactoriamente!"
                        );
                    }
                    break;
                case "UPD":
                    $result = Personas::update(intval($this->viewData["id"]), $this->viewData["identidad"], $this->viewData["nombre"], $this->viewData["edad"]);
                    if ($result) {
                        \Utilities\Site::redirectToWithMsg(
                            "index.php?page=mnt_personas",
                            "Persona Actualizado Satisfactoriamente!"
                        );
                    }
                    break;
                case "DEL":
                    $result = Personas::delete(intval($this->viewData["id"]));
                    if ($result) {
                        \Utilities\Site::redirectToWithMsg(
                            "index.php?page=mnt_personas",
                            "Persona Eliminado Satisfactoriamente!"
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
                $this->viewData["id"],
                $this->viewData["identidad"]
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
        }
        $this->viewData["crsf_token"] = md5(getdate()[0] . $this->name);
        $_SESSION[$this->name . "crsf_token"] = $this->viewData["crsf_token"];
    }
}
