<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Cleison Ferreira
 */
interface CrudAction {
    public function load($obj, $condicao);
    public function save($obj);
    public function delete($obj);
    public function restore($obj);
    public function update($obj);
}
?>
