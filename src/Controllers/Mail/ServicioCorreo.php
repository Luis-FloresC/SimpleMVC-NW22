<?php

namespace Controllers\Mnt;

use Controllers\PublicController;
use Dao\Mnt\Personas as DaoPersonas;
use Views\Renderer;

class ServicioCorreo extends PublicController
{
    private $_viewData = array();
    public function run(): void
    {
        
        error_log(json_encode($this->_viewData));
        Renderer::render("mail/mail", $this->_viewData);
    }
}