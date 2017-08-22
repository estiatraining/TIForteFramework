<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CrudDao
 *
 * @author Cleison Ferreira
 */
$__autoload = new LoadClass();
$__autoload->carregar('UtilsDB;CrudDao');

abstract class CrudDaoImp extends UtilsDB implements CrudDao {

    public function create($obj) {        
        $this->setDto($obj);
        $bean = $this->getDto();        
        $tabela = $this->getTable();
        $colunas = $this->getColumns();
        $bean = $this->executeInsert($tabela, $colunas, $bean);
        $this->closeConn();
        return $bean;
    }

    public function delete($obj) {
        $this->setDto($obj);
        $bean = $this->getDto();
        $tabela = $this->getTable();
        $colunas = $this->getColumns();
        $bean = $obj;        
        $this->closeConn();
        return $this->executeDelete($tabela, $colunas);
    }

    public function restore($obj) {
        $this->setDto($obj);
        $bean = $this->getDto();
        $tabela = $this->getTable();
        $colunas = $this->getColumns();
        $bean = $obj;
        $bean = $this->executeRestore($tabela, $colunas, $bean);
        $this->closeConn();
        return $bean;
    }

    public function select($obj, $condicao) {
        $this->setDto($obj);
        $bean = $this->getDto();
        $tabela = $this->getTable();
        $colunas = $this->getColumns();
        $bean = $obj;
        $bean = $this->executeSelect($tabela, $bean, $condicao);
        $this->closeConn();
        return $bean;
    }

    public function update($obj) {
        $this->setDto($obj);
        $bean = $this->getDto(); 
        $tabela = $this->getTable();
        $colunas = $this->getColumns();
        $bean = $obj;
        $bean = $this->executeUpdate($tabela, $colunas, $bean);
        $this->closeConn();
        return $bean;
    }

    public abstract function getDto();

    public abstract function setDto($obj);

    public abstract function getTable();

    public abstract function getColumns();
}

?>
