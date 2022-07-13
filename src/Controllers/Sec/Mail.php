<?php

namespace Controllers\Sec;

use Controllers\PublicController;

use Views\Renderer;

class Mail extends PublicController
{
    private $_viewData = array();
    private $generalError = "";
    public function run(): void
    {

        error_log(json_encode($this->_viewData));
        $this->SendMail();
        $this->_viewData = get_object_vars($this);
        Renderer::render("security/mail", $this->_viewData);
    }

    private function SendMail()
    {
        

        $paracorreo 		= "lua_floresc@unicah.edu";
        $titulo				= "Recuperar contraseña";
        $mensaje			= "Hola";
        $tucorreo			= "From: lf016158@gmail.com";

        if (mail($paracorreo, $titulo, $mensaje, $tucorreo)) {
            echo "<script> alert('Contraseña enviado');window.location= 'index.php' </script>";
        } else {
            echo "<script> alert('Error');window.location= 'index.html' </script>";
        }
    }
    
}
