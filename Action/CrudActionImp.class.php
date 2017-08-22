<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CrudAction
 *
 * @author Cleison Ferreira
 */
$__autoload = new LoadClass();
$__autoload->carregar('CrudAction');

abstract class CrudActionImp implements CrudAction {

    public $Dto;
    public $Dao;

    public function save($obj) {
        $this->Dto = $obj;        
        return $this->getDao()->create($this->Dto);
    }

    public function update($obj) {
        $this->Dto = $obj;
        return $this->getDao()->update($this->Dto);
    }

    public function load($obj, $condicao) {
        $this->Dto = $obj;
        return $this->getDao()->select($this->Dto, $condicao);
    }

    public function restore($obj) {
        $this->Dto = $obj;
        return $this->getDao()->restore($this->Dto);
    }

    public function delete($obj) {
        $this->Dto = $obj;
        return $this->getDao()->delete($this->Dto);
    }

    public abstract function getDao();
}

?>
