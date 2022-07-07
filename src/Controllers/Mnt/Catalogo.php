<?php
/**
 * PHP Version 7.2
 * Mnt
 *
 * @category Controller
 * @package  Controllers\Mnt
 * @author   Angel David Quintanilla
 * @license  Comercial http://
 * @version  CVS:1.0.0
 * @link     http://url.com
 */
 namespace Controllers\Mnt;

// ---------------------------------------------------------------
// Sección de imports
// ---------------------------------------------------------------
use Controllers\PublicController;
use Dao\Mnt\Productos as DaoProductos;
use Views\Renderer;

/**
 * Productos
 *
 * @category Public
 * @package  Controllers\Mnt;
 * @author   Angel David Quintanilla
 * @license  MIT http://
 * @link     http://
 */
class Catalogo extends PublicController //Nos permite tener los controladores adecuados
//Recordemos que el PublicController nos obliga a usar el metodo run para devolver algun tipo de vista
{
    /**
     * Runs the controller
     *
     * @return void
     */

    private $PageTitle = "";
    private $Productos = array();
    private $Page = 0;
    private $ProductLimit = 3;
    private $Start = 0;
    private $Total = 0;
    private $PagesCount = 1;
    private $PageIndexes = array();

    private $Previous = 0;
    private $PreviousState = "";
    private $Next = 0;
    private $NextState = "";

    private $UsuarioBusqueda = "";
    private $UsuarioBusquedaByPrice = "";
    public function run() :void
    {
        $this->Page = isset($_GET['PageIndex']) ? $_GET['PageIndex'] : 1;
        $this->Start = ($this->Page-1) * $this->ProductLimit;
        $this->UsuarioBusqueda = isset($_GET['UsuarioBusqueda']) ? $_GET['UsuarioBusqueda'] : "";
        $this->UsuarioBusquedaByPrice = isset($_GET['UsuarioBusquedaByPrice']) ? $_GET['UsuarioBusquedaByPrice']: "";
        $this->_load($this->UsuarioBusqueda,$this->UsuarioBusquedaByPrice);

        $dataview = get_object_vars($this);

        $layout = "layout.view.tpl";

        if (\Utilities\Security::isLogged()) {
            $layout = "privatelayout.view.tpl";
            \Utilities\Nav::setNavContext();
        }

        \Views\Renderer::render("mnt/catalogo", $dataview, $layout);
    }

    private function _load($busqueda="", $busquedaByPrice="")
    {
        if (empty($busqueda) && empty($busquedaByPrice)) {
            $this->PageTitle = "Todos los Productos";
            $_total = DaoProductos::getProductCount();
            $_data = DaoProductos::getProductosforPage($this->Start, $this->ProductLimit);
            $this->Total = intval($_total["Total"]);
        } else {
            if (empty($busquedaByPrice)) {
                $this->PageTitle = "Resultados de la Búsqueda: ".$this->UsuarioBusqueda;
                $_total = DaoProductos::searchProductosClienteCount($this->UsuarioBusqueda);
                $_data = DaoProductos::searchProductosCliente($this->UsuarioBusqueda, $this->Start, $this->ProductLimit);
                $this->Total = intval($_total["Total"]);
            } else {
                $rangos = explode("-",$busquedaByPrice);

                $this->PageTitle = "Resultados de la Búsqueda por precio: ".$this->UsuarioBusquedaByPrice;
                $_total = DaoProductos::searchProductosByPriceCount($rangos[0],$rangos[1]);
                $_data = DaoProductos::searchProductosByPrice($rangos[0],$rangos[1], $this->Start, $this->ProductLimit);
                $this->Total = intval($_total[0]["Total"]);
            }
        }
        
       
        $this->PagesCount = ceil($this->Total/$this->ProductLimit);

        if (empty($this->UsuarioBusqueda)) {
            for ($i=1; $i<=$this->PagesCount; $i++) {
                $this->PageIndexes[] = array("Index"=>$i, "Busqueda"=>"", "Estado"=> ($this->Page == $i) ? "active" : "");
            }
        } else {
            for ($i=1; $i<=$this->PagesCount; $i++) {
                $this->PageIndexes[] = array("Index"=>$i, "Busqueda"=>$this->UsuarioBusqueda, "Estado"=> ($this->Page == $i) ? "active" : "");
            }
        }

        $this->Previous = $this->Page - 1;
        $this->Next = $this->Page + 1;
       
        $max_description_length = 50;
        
        foreach ($_data as $key => $value) {
            if (strlen($_data[$key]["invPrdDsc"]) > $max_description_length) {
                $string = $value["invPrdDsc"];
                $offset = ($max_description_length - 3) - strlen($string);
                $_data[$key]["invPrdDsc"] = substr($string, 0, strrpos($string, ' ', $offset)) . '...';
            }

            $precioFinal = ($value["invPrdPrecioVenta"]) + ($value["invPrdPrecioVenta"] * 0.15);
            $_data[$key]["invPrdPrecioVenta"] = number_format($precioFinal, 2);
        }

        if ($_data) {
            $this->Productos = $_data;
        }

        $this->_setViewData();
    }

    private function _setViewData()
    {
        $this->NextState = ($this->Page==$this->PagesCount) ? "disabled" : "";
        $this->PreviousState = ($this->Page == 1) ? "disabled" : "";
    }
}
