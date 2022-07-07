<?php

namespace Controllers\ORM;

// ---------------------------------------------------------------
// Sección de imports
// ---------------------------------------------------------------
use Controllers\PublicController;
use Dao\ORM\GeneradorModel as DaoGeneradorModel;
use Views\Renderer;
use Utilities\arrUtils as UarrUtils;

class Generador extends PublicController
{
    private $_viewData = array();
    private $_tiposDeDatos = array();
    public function run(): void
    {
        $this->_tiposDeDatos = array(
            "0" => "TINYINT",
            "1" => "SMALLINT",
            "2" => "MEDIUMINT",
            "3" => "INT",
            "4" => "BIGINT"
        );

        if ($this->isPostBack()) {
            UarrUtils::mergeFullArrayTo($_POST, $this->_viewData);
            $this->_viewData["Tabla"] = DaoGeneradorModel::obtenerEstructuraDeTabla($this->_viewData["table"]);
            $this->ControllerForList();
            $this->DaoModel();
            $this->ControllerForTable();
            $this->ViewForList();
            $this->ViewForTable();
        }
        error_log(json_encode($this->_viewData));

        Renderer::render('ORM/generador', $this->_viewData);
    }

    private function GetTypesOfData()
    {
        $TiposDatos = array();
        foreach ($this->_viewData["Tabla"] as $field) {
            $TiposDatos[] = $field["Type"];
        }
        return $TiposDatos;
    }


    private function GetFieldPrimaryKey()
    {
        $primaryKey = array();
        foreach ($this->_viewData["Tabla"] as $field) {
            if ($field["Key"] === "PRI") {
                $primaryKey[] = $field["Field"];
            }
        }
        return $primaryKey;
    }

    private function GetFields()
    {
        $Campos = array();
        foreach ($this->_viewData["Tabla"] as $field) {
            $Campos[] = $field["Field"];
        }
        return $Campos;
    }

    private function GetVariablesPrivadas()
    {
        $Variables = array();
        foreach ($this->_viewData["Tabla"] as $field) {
            $tipoDato = array_search(strtoupper($field["Type"]), $this->_tiposDeDatos) ? 0 : '""';
            $Variables[] = sprintf('private $_%s = %s', $field["Field"], $tipoDato);
        }
        return $Variables;
    }

    private function getVariablesError()
    {
        $Variables = array();
        foreach ($this->_viewData["Tabla"] as $field) {
            if ($field["Null"] === "NO" && $field["Key"] !== "PRI") {
                $Variables[] = sprintf('$this->viewData["error_%s"] = array();', $field["Field"]);
            }
        }
        return $Variables;
    }

    private function GetVariablesErrorArray(){
        $Variables = array();
        foreach ($this->_viewData["Tabla"] as $field) {
            if ($field["Null"] === "NO" && $field["Key"] !== "PRI") {
                $Variables[] = sprintf('error_%s', $field["Field"]);
            }
        }
        return $Variables;
    }


    private function GetVariablesViewData()
    {
        $Variables = array();
        foreach ($this->_viewData["Tabla"] as $field) {
            $tipoDato = array_search(strtoupper($field["Type"]), $this->_tiposDeDatos) ? 0 : '""';
            $Variables[] = sprintf('$this->viewData["%s"] = %s;', $field["Field"], $tipoDato);
        }
        return $Variables;
    }

    private function GetParametrosViewData($isUpdate)
    {
        $Acum = "(";
        foreach ($this->_viewData["Tabla"] as $field) {
            if ($isUpdate) {
                if ($field["Key"] === "PRI") {
                    $Acum .= sprintf('intval($this->viewData["%s"]),', $field["Field"]);
                } else {
                    $Acum .= sprintf('$this->viewData["%s"],', $field["Field"]);
                }
            } else {
                if ($field["Key"] !== "PRI") {
                    $Acum .= sprintf('$this->viewData["%s"],', $field["Field"]);
                }
            }
        }
        $Acum = substr($Acum, 0, -1);

        $Acum .= ")";
        return $Acum;
    }

    private function GetParametros($isUpdate)
    {
        $Acum = "(";
        foreach ($this->_viewData["Tabla"] as $field) {
            if ($isUpdate) {
                $Acum .= sprintf("$%s,", $field["Field"]);
            } else {
                if ($field["Key"] !== "PRI") {
                    $Acum .= sprintf("$%s,", $field["Field"]);
                }
            }
        }
        $Acum = substr($Acum, 0, -1);

        $Acum .= ")";
        return $Acum;
    }

    private function GetParametrosMysql($isUpdate)
    {
        $Acum = "";
        foreach ($this->_viewData["Tabla"] as $field) {
            if ($isUpdate) {
                $Acum .= sprintf("`%s` = :%s,", $field["Field"], $field["Field"]);
            } else {
                if ($field["Key"] !== "PRI") {
                    $Acum .= sprintf(":%s,", $field["Field"]);
                }
            }
        }
        $Acum = substr($Acum, 0, -1);


        return $Acum;
    }

    private function GetParametrosArray($isUpdate)
    {
        $Acum = "[";
        foreach ($this->_viewData["Tabla"] as $field) {
            if ($isUpdate) {
                $Acum .= sprintf('"%s"=>$%s,', $field["Field"], $field["Field"]);
            } else {
                if ($field["Key"] !== "PRI") {
                    $Acum .= sprintf('"%s"=> $%s,', $field["Field"], $field["Field"]);
                }
            }
        }
        $Acum = substr($Acum, 0, -1);

        $Acum .= "]";
        return $Acum;
    }

    private function DaoModel()
    {
        $buffer = array();
        $llavePrimaria = $this->GetFieldPrimaryKey();
        $buffer[] = '<?php';
        $buffer[] = sprintf('namespace Dao\%s;', $this->_viewData["namespace"]);
        $buffer[] = 'use Dao\Table;';
        $buffer[] = '';
        $buffer[] = sprintf('class %s extends Table{', $this->_viewData["entity"]);
        $buffer[] =  '';
        $buffer[] = 'public static function getAll(){';
        $buffer[] = sprintf('$sqlstr = "Select * from %s;";', $this->_viewData["table"]);
        $buffer[] = 'return self::obtenerRegistros($sqlstr, array());';
        $buffer[] =  '}';
        $buffer[] =  '';

        $buffer[] = sprintf('public static function getById(int $%s){', $llavePrimaria[0]);
        $buffer[] = sprintf('$sqlstr = "Select * from %s where %s = :%s;";', $this->_viewData["table"], $llavePrimaria[0], $llavePrimaria[0]);
        $buffer[] = sprintf('$sqlParams = array("%s" => $%s);', $llavePrimaria[0], $llavePrimaria[0]);
        $buffer[] = 'return self::obtenerRegistros($sqlstr, $sqlParams);';
        $buffer[] =  '}';
        $buffer[] =  '';

        $buffer[] =  '';
        $buffer[] = sprintf('public static function insert%s{', $this->GetParametros(false));
        $buffer[] = sprintf('$sqlstr = "INSERT INTO %s%s values (%s)";', $this->_viewData["table"], $this->GetParametros(false), $this->GetParametrosMysql(false));
        $buffer[] = sprintf('$sqlParams = %s;', $this->GetParametrosArray(false));
        $buffer[] = 'return self::executeNonQuery($sqlstr, $sqlParams);';
        $buffer[] =  '}';
        $buffer[] =  '';

        $buffer[] =  '';
        $buffer[] = sprintf('public static function update%s{', $this->GetParametros(true));
        $buffer[] = sprintf('$sqlstr = "update %s set %s where %s = :%s";', $this->_viewData["table"], $this->GetParametrosMysql(true), $llavePrimaria[0], $llavePrimaria[0]);
        $buffer[] = sprintf('$sqlParams = %s;', $this->GetParametrosArray(true));
        $buffer[] = 'return self::executeNonQuery($sqlstr, $sqlParams);';
        $buffer[] =  '}';
        $buffer[] =  '';



        $buffer[] = sprintf('public static function delete(int $%s){', $llavePrimaria[0]);
        $buffer[] = sprintf('$sqlstr = "Delete from %s where %s = :%s;";', $this->_viewData["table"], $llavePrimaria[0], $llavePrimaria[0]);
        $buffer[] = sprintf('$sqlParams = array("%s" => $%s);', $llavePrimaria[0], $llavePrimaria[0]);
        $buffer[] = 'return self::executeNonQuery($sqlstr, $sqlParams);';
        $buffer[] =  '}';
        $buffer[] =  '';

        $buffer[] =  '}';
        $buffer[] = '?>';

        $this->_viewData["DaoModel"] = htmlspecialchars(implode("\n", $buffer));
    }


    private function ControllerForList()
    {
        $buffer = array();
        $TiposDatos = $this->GetTypesOfData();
        $LlavePrimaria = ($this->GetFieldPrimaryKey());
        $Campos = $this->GetFields();
        $Variables = $this->GetVariablesPrivadas();

        $buffer[] = '<?php';
        $buffer[] = ' ';
        $buffer[] = sprintf('namespace Controllers\%s;', $this->_viewData["namespace"]);
        $buffer[] = 'use Controllers\PublicController;';
        $buffer[] = sprintf('use Dao\%s\%s as Dao%s;', $this->_viewData["namespace"], $this->_viewData["entity"], $this->_viewData["entity"]);
        $buffer[] = 'use Views\Renderer;';
        $buffer[] = ' ';
        $buffer[] = sprintf('class %s extends PublicController{', $this->_viewData["entity"]);
        $buffer[] = 'private $_viewData = array();';
        $buffer[] = 'public function run(): void{';
        $buffer[] = sprintf('$this->_viewData["%s"] = Dao%s::getAll();', $this->_viewData["entity"], $this->_viewData["entity"]);
        $buffer[] = 'error_log(json_encode($this->_viewData));';
        $buffer[] = sprintf('Renderer::render("%s/%s", $this->_viewData);', $this->_viewData["namespace"], $this->_viewData["entity"]);
        $buffer[] = '}';
        $buffer[] = '}';
        /*
        foreach ($TiposDatos as $dato) {
            $buffer[] = $dato;
        }
        foreach ($LlavePrimaria as $dato) {
            $buffer[] = $dato;
        }
        foreach ($Campos as $dato) {
            $buffer[] = $dato;
        }
        foreach ($Variables as $dato) {
            $buffer[] = $dato;
        }
        $buffer[] = $this->GetParametros(false);
        $buffer[] = $this->GetParametros(true);
        $buffer[] = $this->GetParametrosArray(false);
        $buffer[] = $this->GetParametrosArray(true);
        */
        $this->_viewData["ControllerForList"] = htmlspecialchars(implode("\n", $buffer));
    }

    private function ControllerForTable()
    {
        $buffer = array();
        $Variables = $this->GetVariablesViewData();
        $llavePrimaria = ($this->GetFieldPrimaryKey());
        $VariablesError = $this->GetVariablesError();
        $Campos = $this->GetFields();
        $ParametrosViewDataGuardar = $this->GetParametrosViewData(false);
        $ParametrosViewDataModi = $this->GetParametrosViewData(true);
        $NombreClase =  substr($this->_viewData["entity"], 0, -1);
        $buffer[] = '<?php';
        $buffer[] = '';
        $buffer[] = sprintf('namespace Controllers\%s;', $this->_viewData["namespace"]);
        $buffer[] = 'use Controllers\PublicController;';
        $buffer[] = 'use Views\Renderer;';
        $buffer[] = 'use Utilities\Validators;';
        $buffer[] = sprintf('use Dao\%s\%s;', $this->_viewData["namespace"], $this->_viewData["entity"]);

        $buffer[] = '';
        $buffer[] = sprintf('class %s extends PublicController{', $NombreClase);
        $buffer[] = '';

        $buffer[] = 'private $viewData = array();';

        $buffer[] = 'private $arrModeDesc = array();';
        $buffer[] = 'private $arrEstados = array();';

        $buffer[] = 'public function run():void {';

        $buffer[] = '$this->init();';
        $buffer[] = 'if (!$this->isPostBack()) {';
        $buffer[] = '$this->procesarGet();}';

        $buffer[] = 'if ($this->isPostBack()) {';
        $buffer[] = '$this->procesarPost();}';

        $buffer[] = '$this->processView();';
        $buffer[] = sprintf('Renderer::render("%s/%s", $this->viewData);', strtolower($this->_viewData["namespace"]), strtolower($NombreClase));

        $buffer[] = '}'; //fin del run
        $buffer[] = 'private function init(){';

        $buffer[] = ' $this->viewData["mode"] = "";
            $this->viewData["mode_desc"] = "";
            $this->viewData["crsf_token"] = "";';
        foreach ($Variables as $dato) {
            $buffer[] = $dato;
        }

        foreach ($VariablesError as $dato) {
            $buffer[] = $dato;
        }
        $buffer[] = '$this->viewData["btnEnviarText"] = "Guardar";';
        $buffer[] = '$this->viewData["readonly"] = false;';
        $buffer[] = '$this->viewData["showBtn"] = true;';
        $buffer[] = '$this->arrModeDesc = array(';
        $buffer[] = '"INS"=>"Nuevo Producto",';
        $buffer[] = '"UPD"=>"Editando %s %s",';
        $buffer[] = '"DSP"=>"Detalle de %s %s",';
        $buffer[] = '"DEL"=>"Eliminado %s %s");';

        $buffer[] = '$this->arrEstados = array(';
        $buffer[] = 'array("value" => "ACT", "text" => "Activo"),';
        $buffer[] = 'array("value" => "INA", "text" => "Inactivo"),';
        $buffer[] = ');';


        $buffer[] = '}'; //Fin de función init
        $buffer[] = 'private function procesarGet(){';
        $buffer[] = sprintf(' if (isset($_GET["mode"])) {
            $this->viewData["mode"] = $_GET["mode"];
            if (!isset($this->arrModeDesc[$this->viewData["mode"]])) {
                error_log("Error: (%s) Mode solicitado no existe.");
                \Utilities\Site::redirectToWithMsg(
                    "index.php?page=%s_%s",
                    "No se puede procesar su solicitud!"
                );
            }
        }', $this->_viewData["entity"], $this->_viewData["namespace"], $this->_viewData["entity"]);

        $buffer[] = sprintf('if ($this->viewData["mode"] !== "INS" && isset($_GET["id"])) {
            $this->viewData["%s"] = intval($_GET["id"]);
            $tmp%s = %s::getById($this->viewData["%s"]);
            error_log(json_encode($tmp%s));
            \Utilities\ArrUtils::mergeFullArrayTo($tmp%s, $this->viewData);
        }
    }', $llavePrimaria[0], $NombreClase, $this->_viewData["entity"], $llavePrimaria[0], $NombreClase, $NombreClase);









        $buffer[] = 'private function procesarPost(){';
        $buffer[] = '';
        $buffer[] = '$hasErrors = false;
                    \Utilities\ArrUtils::mergeArrayTo($_POST, $this->viewData);';
        $buffer[] = sprintf('if (isset($_SESSION[$this->name . "crsf_token"])
        && $_SESSION[$this->name . "crsf_token"] !== $this->viewData["crsf_token"]
    ) {
        \Utilities\Site::redirectToWithMsg(
            "index.php?page=%s_%s",
            "ERROR: Algo inesperado sucedió con la petición Intente de nuevo."
        );
    }', strtolower($this->_viewData["namespace"]), strtolower($this->_viewData["entity"]));
        $buffer[] = '';

        foreach ($this->_viewData["Tabla"] as $field) {
            if ($field["Null"] === "NO" && $field["Key"] !== "PRI") {
                $buffer[] = sprintf('if (Validators::IsEmpty($this->viewData["%s"])) {
                $this->viewData["error_%s"][]
                    = "El campo %s es requerido";
                $hasErrors = true;
            }', $field["Field"], $field["Field"], $field["Field"]);
            }
        }
        $buffer[] = 'error_log(json_encode($this->viewData));';
        $buffer[] = 'if (!$hasErrors) {';
        $buffer[] = '$result = null;';
        $buffer[] = 'switch($this->viewData["mode"]) {';
        $buffer[] = sprintf('case "INS":
                $result = %s::insert%s;
                if ($result) {
                        \Utilities\Site::redirectToWithMsg(
                            "index.php?page=%s_%s",
                            "%s Guardado Satisfactoriamente!"
                        );
                }
                break;', $this->_viewData["entity"], $ParametrosViewDataGuardar, strtolower($this->_viewData["namespace"]), strtolower($this->_viewData["entity"]), $NombreClase);
        $buffer[] = sprintf('case "UPD":
                    $result = %s::update%s;
                    if ($result) {
                            \Utilities\Site::redirectToWithMsg(
                                "index.php?page=%s_%s",
                                "%s Actualizado Satisfactoriamente!"
                            );
                    }
                    break;', $this->_viewData["entity"], $ParametrosViewDataModi, strtolower($this->_viewData["namespace"]), strtolower($this->_viewData["entity"]), $NombreClase);

        $buffer[] = sprintf('case "DEL":
                        $result = %s::delete(intval($this->viewData["%s"]));
                        if ($result) {
                                \Utilities\Site::redirectToWithMsg(
                                    "index.php?page=%s_%s",
                                    "%s Eliminado Satisfactoriamente!"
                                );
                        }
                        break;', $this->_viewData["entity"], $llavePrimaria[0], strtolower($this->_viewData["namespace"]), strtolower($this->_viewData["entity"]), $NombreClase);
        $buffer[] = '}';
        $buffer[] = '}'; //Fin del if hasherror    
        $buffer[] = '}'; //fin de procesarPost

        $buffer[] = '';
        $buffer[] = ' private function processView()
        {';
        $buffer[] = 'if ($this->viewData["mode"] === "INS") {
                $this->viewData["mode_desc"]  = $this->arrModeDesc["INS"];
                $this->viewData["btnEnviarText"] = "Guardar Nuevo";
            } else {';
        $buffer[] = sprintf('$this->viewData["mode_desc"]  = sprintf(
            $this->arrModeDesc[$this->viewData["mode"]],
            $this->viewData["%s"],
            $this->viewData["%s"]
        );', $llavePrimaria[0], $Campos[1]);

        $buffer[] = ' if ($this->viewData["mode"] === "DSP") {
            $this->viewData["readonly"] = true;
            $this->viewData["showBtn"] = false;
        }
        if ($this->viewData["mode"] === "DEL") {
            $this->viewData["readonly"] = true;
            $this->viewData["btnEnviarText"] = "Eliminar";
        }
        if ($this->viewData["mode"] === "UPD") {
            $this->viewData["btnEnviarText"] = "Actualizar";
        }';

        $buffer[] = '$this->viewData["crsf_token"] = md5(getdate()[0] . $this->name);
        $_SESSION[$this->name . "crsf_token"] = $this->viewData["crsf_token"];';
        $buffer[] = '}'; //fin del else
        $buffer[] = '}'; //fin de processView
        $buffer[] = '}'; //fin de la clase
        $buffer[] = '?>';




        $this->_viewData["ControllerForTable"] = htmlspecialchars(implode("\n", $buffer));
    }

    private function ViewForList(){
        $buffer = array();
        $NombreClase =  substr($this->_viewData["entity"], 0, -1);
        $llavePrimaria = $this->GetFieldPrimaryKey();
        $Campos = $this->GetFields();
        $buffer[] = sprintf('<h1>Trabajar con %s</h1>', $this->_viewData['entity']);
        $buffer[] = '<section>';
        $buffer[] = '   <table>';
        $buffer[] = '       <thead>';
        $buffer[] = '           <tr>';
        foreach ($this->_viewData["Tabla"] as $field) {
            $buffer[] = sprintf('               <th>%s</th>', $field["Field"]);
        }
        $buffer[] = sprintf('           <th><a href="index.php?page=%s-%s&mode=INS">Nuevo</a></th>', $this->_viewData['namespace'], $this->_viewData['entity']);
        $buffer[] = '           </tr>';
        $buffer[] = '       </thead>';
        $buffer[] = '       <tbody>';
        $buffer[] = sprintf('           {{foreach %s}}', $this->_viewData["entity"]);
        $buffer[] = '           <tr>';
        foreach ($this->_viewData["Tabla"] as $field) {
            if($Campos[1] === $field["Field"]){
                $buffer[] = sprintf('               <td> <a href="index.php?page=%s-%s&mode=DSP&id={{%s}}">{{%s}}</a></td>',  $this->_viewData['namespace'], $NombreClase ,$llavePrimaria[0],$field["Field"]);
            }
            else{
                $buffer[] = sprintf('               <td>{{%s}}</td>', $field["Field"]);
            }
            
        }
        $buffer[] = '               <td>';
        $buffer[] =  sprintf('               <a href="index.php?page=%s_%s&mode=UPD&id={{%s}}">Editar</a>', $this->_viewData['namespace'], $NombreClase ,$llavePrimaria[0]);
        $buffer[] =  sprintf('               <a href="index.php?page=%s_%s&mode=DEL&id={{%s}}">Eliminar</a>', $this->_viewData['namespace'], $NombreClase,$llavePrimaria[0]);
        $buffer[] = '               </td>';
        $buffer[] = '           </tr>';
        $buffer[] = sprintf('           {{endfor %s}}', $this->_viewData["entity"]);
        $buffer[] = '       </tbody>';
        $buffer[] = '   </table>';
        $buffer[] = '</section>';
        $this->_viewData["ViewForList"] = htmlspecialchars(implode("\n", $buffer));
    }

    private function ViewForTable(){
        $buffer = array();
        $NombreClase =  substr($this->_viewData["entity"], 0, -1);
        $Campos = $this->GetFields();
        $VariablesError = $this->GetVariablesErrorArray();
        $llavePrimaria = $this->GetFieldPrimaryKey();
        $buffer[] = '<h1>{{mode_desc}}</h1>';
        $buffer[] = '<section>';
        $buffer[] = sprintf('   <form action="index.php?page=%s_%s" method="post">',strtolower($this->_viewData["namespace"]), strtolower($NombreClase));
        $buffer[] = sprintf('        <input type="hidden" name="mode" value="{{mode}}" />
        <input type="hidden" name="crsf_token" value="{{crsf_token}}" />
        <input type="hidden" name="%s" value="{{%s}}" />',$llavePrimaria[0], $llavePrimaria[0]);

        foreach ($this->_viewData["Tabla"] as $field){
            if($llavePrimaria[0] !== $field["Field"]){
                $buffer[] = sprintf(' <fieldset>
                <label for="%s">%s</label>
                <input {{if readonly}}readonly{{endif readonly}} type="text" id="%s" name="%s" placeholder="%s" value="{{%s}}"/>',$field["Field"],$field["Field"],$field["Field"],$field["Field"],$field["Field"],$field["Field"]);
                foreach($VariablesError as $dato){
                   if($dato === sprintf('error_%s',$field["Field"])){
                     $buffer[] = sprintf('{{if error_%s}}
                     {{foreach error_%s}}
                       <div class="error">{{this}}</div>
                     {{endfor error_%s}}
                 {{endif error_%s}}',$field["Field"],$field["Field"],$field["Field"],$field["Field"]);
                   }
                    
                }
                 $buffer[] = '</fieldset>';
            }
        }

        $buffer[] = ' <fieldset>
        {{if showBtn}}
          <button type="submit" name="btnEnviar">{{btnEnviarText}}</button>
          &nbsp;
        {{endif showBtn}}
        <button name="btnCancelar" id="btnCancelar">Cancelar</button>
      </fieldset>';


   
         
        $buffer[] = '   </form>';
        $buffer[] = '</section>';

        $buffer[] = sprintf('<script>
        document.addEventListener("DOMContentLoaded", function(){
          document.getElementById("btnCancelar").addEventListener("click", function(e){
            e.preventDefault();
            e.stopPropagation();
            window.location.href = "index.php?page=%s_%s";
          });
        });
      </script>',strtolower($this->_viewData["namespace"]),strtolower($this->_viewData["entity"]));
        $this->_viewData["ViewForTable"] = htmlspecialchars(implode("\n", $buffer));
    }
}
