<?php
namespace Controllers\Sec;
use Controllers\PublicController;
use Dao\Security\Security as DaoSecurity;
use Utilities\Validators as Validators;
use Views\Renderer;
class RecoverAccount extends PublicController
{
    private $_viewData = array();
    private $txtEmail = "";
    private $hasError = false;
    private $generalError = "";
    private $errorEmail = "";
    public function run() :void
    {
        if($this->isPostBack()){
            $this->txtEmail = $_POST["txtEmail"] != "" ? $_POST["txtEmail"] : "";
           

            if(!Validators::IsValidEmail($this->txtEmail))
            {
                $this->errorEmail = "¡Correo  2no tiene el formato adecuado!";
                $this->hasError = true;
            }

        }
        $this->_viewData = get_object_vars($this);
        Renderer::render("security/recoverAccount",$this->_viewData);
    }
}

?>