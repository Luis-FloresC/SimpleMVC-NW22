<?php

namespace Controllers\Mnt;

use Controllers\PublicController;
use Dao\Mnt\Personas as DaoPersonas;
use Views\Renderer;

class Personas extends PublicController
{
    private $_viewData = array();
    public function run(): void
    {
        $this->_viewData["Personas"] = DaoPersonas::getAll();
        error_log(json_encode($this->_viewData));
        Renderer::render("mnt/personas", $this->_viewData);
    }
}
