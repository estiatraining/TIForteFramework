<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Conn extends Utilitarios{
    private $host;
    private $port;
    private $dbname;
    private $usr;
    private $passwd;
    private $sgbd;
    public function connect() {
        $logs = new Logs();
        try {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] .'/AllRadio/TIForteFramework/sysConf/conf.ini')) {
                $dados = parse_ini_file($_SERVER['DOCUMENT_ROOT'] .'/AllRadio/TIForteFramework/sysConf/conf.ini');
                $this->usr = $dados['user'];
                $this->passwd = $dados['password'];
                $this->host = $dados['host'];
                $this->port = $dados['port'];
                $this->dbname = $dados['bank'];
                $this->sgbd = $dados['sgbd'];
                if ($this->sgbd == 'mysql') {
                    $conn = mysql_connect($this->host, $this->usr, $this->passwd);
                    $db = mysql_select_db($this->dbname, $conn);
                    if (!$conn or !$db) {
                        throw new Excecoes(Excecoes::ERRO_BANCO);
                    }
                    else
                        return $conn;
                }else if ($this->sgbd == 'pgsql') {
                    $conn = pg_connect("host=".$this->host." port=".$this->port." dbname=".$this->dbname." user=".$this->usr." password=".$this->passwd."");
                    if (!$conn) {
                        throw new Excecoes(Excecoes::ERRO_BANCO);
                    }
                    else
                        return $conn;
                }
            }
            else
                throw new Excecoes(Excecoes::ERRO_FILE);
        } catch (Exception $e) {
            $e . "<b><i> Linha: " . $e->getLine() . "<br />Arquivo: " . $_SERVER['PHP_SELF'] . "</i></b>";
            $logs->escrever($e);
            header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/AllRadio/index.phtml?msg=" . $e);
        }
    }

    public function closeConn() {
        if ($this->sgbd == 'mysql') {
            mysql_close();
        } else if ($this->sgbd == 'pgsql') {
            pg_close();
        }
    }
}

?>
