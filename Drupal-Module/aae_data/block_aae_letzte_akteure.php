<?php
/**
 * Kleiner, interner(!) Block zum Anzeigen der letzten drei (=$limit) eingetragenen Projekte.
 * Wird in theme/page--front.tpl.php aufgerufen
 *
 * @use Einzubinden via require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/block_aae_letzte_akteure.module';
 */

function block_aae_print_letzte_akteure($limit = 3) {

  $tbl_akteur = "aae_data_akteur";

  require_once 'database/db_connect.php';
  $db = new DB_CONNECT();

  $letzteakteure = db_select($tbl_akteur, 'a')
    ->fields('a')
    ->range(0, $limit)
    ->execute()
    ->fetchAll();

  $resultakteure = array();

 foreach($letzteakteure as $row){
    $resultakteure[] = $row; //array('AID' => $row->AID, 'name' => $row->name);
  }

  return $resultakteure;
}