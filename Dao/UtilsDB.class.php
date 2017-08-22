<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UtilsDB
 *
 * @author Cleison Ferreira
 */
$__autoload = new LoadClass();
$__autoload->carregar('Conn');

class UtilsDB extends Conn {

    private $conn;
    private $sgbd;

    public function __construct() {
        $this->conn = $this->connect();
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/AllRadio/TIForteFramework/sysConf/conf.ini')) {
            $dados = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/AllRadio/TIForteFramework/sysConf/conf.ini');
            $this->sgbd = $dados['sgbd'];
        }
    }

    public function executeDelete($tabela, $colunas) {
        $logs = new Logs();
        $result = null;
        try {
            $iterator = $colunas->getIterator();
            while ($iterator->valid()) {
                $campos = $iterator->key();
                $param = $iterator->current();
                $iterator->next();
                break;
            }
            $sql = 'DELETE FROM ' . $tabela . " WHERE " . $campos . " = " . $param . "; ";
            if ($this->sgbd == 'mysql') {
                $result = mysql_query($sql);
                if (!$result)
                    throw new Excecoes(Excecoes::ERRO_EXCLUSAO);
                else
                    return true;
            } else if ($this->sgbd == 'pgsql') {
                $result = mysql_query($sql);
                if (!$result)
                    throw new Excecoes(Excecoes::ERRO_EXCLUSAO);
                else
                    return true;
            }
        } catch (Exception $e) {
            $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
            $e . "<br />" . $sql;
            $logs->escrever($e);
        }
    }

    public function executeRestore($tabela, $colunas, $bean) {
        $lines = 0;
        $result = null;
        $sql = '';
        $logs = new Logs();
        try {
            $iterator = $colunas->getIterator();
            while ($iterator->valid()) {
                $campos = $iterator->key();
                $param = $iterator->current();
                $iterator->next();
                break;
            }
            $sql .= "SELECT * FROM " . $tabela . " WHERE " . $campos . " = " . $param . " ORDER BY " . $campos . "; ";
            if ($this->sgbd == 'mysql') {
                $result = mysql_query($sql);
                if (!$result)
                    throw new Excecoes(Excecoes::ERRO_RESTORE);
                $lines = mysql_num_rows($result);
                if ($lines > 0) {
                    $bean = mysql_fetch_object($result);
                    return $bean;
                }
            } else if ($this->sgbd == 'pgsql') {
                $result = pg_query($sql);
                if (!$result)
                    throw new Excecoes(Excecoes::ERRO_RESTORE);
                $lines = pg_num_rows($result);
                if ($lines > 0) {
                    $bean = pg_fetch_object($result);
                    return $bean;
                }
            }
        } catch (Exception $e) {
            $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
            $e . "<br />" . $sql;
            $logs->escrever($e);
        }
    }

    public function executeSelect($tabela, $bean, $condicao) {
        $lines = 0;
        $result = null;
        $sql = '';
        $logs = new Logs();
        try {
            if($condicao == ""){
                $condicao = '1 = 1';
            }
            $sql = "SELECT * FROM " . $tabela . " WHERE " . $condicao . "; ";
            if ($this->sgbd == 'mysql') {
                $result = mysql_query($sql);
                if (!$result)
                    throw new Excecoes(Excecoes::ERRO_CONSULTA);
                $lines = mysql_num_rows($result);
                if ($lines > 0) {
                    $object = array();
                    while($row = mysql_fetch_object($result)){
                        $object[] = $row;
                    }
                    $obj = new ArrayObject($object);
                    return $obj;
                }
            } else if ($this->sgbd == 'pgsql') {
                $result = pg_query($sql);
                if (!$result)
                    throw new Excecoes(Excecoes::ERRO_CONSULTA);
                $lines = pg_num_rows($result);
                if ($lines > 0) {
                    $object = array();
                    while($row = pg_fetch_object($result)){
                        $object[] = $row;
                    }
                    $obj = new ArrayObject($object);
                    return $obj;
                }
            }
        } catch (Exception $e) {
            $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
            $e . "<br />" . $sql;
            $logs->escrever($e);
        }
    }

    public function executeUpdate($tabela, $colunas, $bean) {
        $result = null;
        $auxCampos = '';
        $auxParam = '';
        $virgula = ', ';
        $camposUpdate = '';
        $cont = 0;
        $logs = new Logs();
        try {
            $iterator = $colunas->getIterator();
            while ($iterator->valid()) {
                if ($cont == 0) {
                    $id = $iterator->key();
                    $valor = $iterator->current();
                } else {
                    $campos = $iterator->key();
                    $param = $iterator->current();
                }
                $iterator->next();
                if ($cont == ( $iterator->count() - 1 )) {
                    $virgula = ' ';
                }
                if ($cont > 0) {
                    $auxCampos .= $campos . $virgula;
                    if (is_string($param) || is_double($param) || is_float($param)) {
                        $camposUpdate .= $campos . " = '" . $param . "' " . $virgula;
                    } else if (is_int($param) || is_integer($param)) {
                        $camposUpdate .= $campos . " = " . $param . " " . $virgula;
                    } else if (is_bool($param)) {
                        if(!$param)
                            $param = 'false';
                        $camposUpdate .= $campos . " = " . $param . " " . $virgula;
                    }
                }
                $cont++;
            }
            $sql = "UPDATE " . $tabela . " SET " . $camposUpdate . " WHERE " . $id . " = " . $valor . ";";
            if ($this->sgbd == 'mysql') {
                $result = mysql_query($sql);
                if (!$result)
                    throw new Excecoes(Excecoes::ERRO_ALTERACAO);
                return $bean;
            } else if ($this->sgbd == 'pgsql') {
                $result = pg_query($sql);
                if (!$result)
                    throw new Excecoes(Excecoes::ERRO_ALTERACAO);
                return $bean;
            }
        } catch (Exception $e) {
            $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
            $e . "<br />" . $sql;
            $logs->escrever($e);
        }
    }

    public function executeInsert($tabela, $colunas, $bean) {
        $result = null;
        $auxCampos = '';
        $auxParam = '';
        $virgula = '';
        $selectCondicoes = '';
        $logs = new Logs();
        try {
            $campos = null;
            $param = null;
            $sql = '';
            $cont = 0;
            $virgula = ', ';
            $and = ' AND ';
            $iterator = $colunas->getIterator();
            while ($iterator->valid()) {
                $campos = $iterator->key();
                $param = $iterator->current();
                $iterator->next();
                if ($cont == ( $iterator->count() - 1 )) {
                    $virgula = ' ';
                    $and = ' ';
                }
                if ($cont > 0) {
                    $auxCampos .= $campos . $virgula;
                    if (is_string($param) || is_double($param) || is_float($param)) {
                        $auxParam .= "'" . $param . "'" . $virgula;
                        $selectCondicoes .= $campos . " = '" . $param . "' " . $and;
                    } else if (is_int($param) || is_integer($param)) {
                        $auxParam .= $param . $virgula;
                        $selectCondicoes .= $campos . " = " . $param . " " . $and;
                    } else if (is_bool($param)) {
                        if(!$param)
                            $param = 'false';
                        $auxParam .= $param . $virgula;
                        $selectCondicoes .= $campos . " = " . $param . " " . $and;
                    }
                }
                $cont++;
            }
            $sql = "INSERT INTO " . $tabela . " ( " . $auxCampos . " ) VALUES ( " . $auxParam . " );";
            if ($this->sgbd == 'mysql') {
                $result = mysql_query($sql);
                if (!$result)
                    throw new Excecoes(Excecoes::ERRO_INCLUSAO);
                $bean = $this->executeSelect($tabela, $bean, $selectCondicoes);
                return $bean;
            } else if ($this->sgbd == 'pgsql') {
                $result = pg_query($sql);
                if (!$result)
                    throw new Excecoes(Excecoes::ERRO_INCLUSAO);
                $bean = $this->executeSelect($tabela, $bean, $selectCondicoes);
                return $bean;
            }
        } catch (Exception $e) {
            $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
            $e . "<br />" . $sql;
            $logs->escrever($e);
        }
    }

    public function startTransaction() {
        $sql = 'START TRANSACTION; ';
        $result = false;
        $logs = new Logs();
        if ($this->sgbd == 'mysql') {
            try {
                $result = mysql_query($sql);
                return true;
            } catch (Exception $e) {
                $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
                $logs->escrever($e);
            }
        } else if ($this->sgbd == 'pgsql') {
            try {
                $result = pg_query($sql);
                return true;
            } catch (Exception $e) {
                $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
                $logs->escrever($e);
            }
        }
    }

    public function getRollback() {
        $sql = 'ROLLBACK; ';
        $result = false;
        $logs = new Logs();
        if ($this->sgbd == 'mysql') {
            try {
                $result = mysql_query($sql);
                return true;
            } catch (Exception $e) {
                $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
                $logs->escrever($e);
            }
        } else if ($this->sgbd == 'pgsql') {
            try {
                $result = pg_query($sql);
                return true;
            } catch (Exception $e) {
                $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
                $logs->escrever($e);
            }
        }
    }

    public function getCommit() {
        $sql = 'COMMIT; ';
        $result = false;
        $logs = new Logs();
        if ($this->sgbd == 'mysql') {
            try {
                $result = mysql_query($sql);
                return true;
            } catch (Exception $e) {
                $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
                $logs->escrever($e);
            }
        } else if ($this->sgbd == 'pgsql') {
            try {
                $result = pg_query($sql);
                return true;
            } catch (Exception $e) {
                $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
                $logs->escrever($e);
            }
        }
    }
}
?>
