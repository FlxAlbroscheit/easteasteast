<?php
/**
 * eventprofil.php zeigt das Profil eines Events an.
 *
 * Ruth, 2015-07-10
 * Felix, 2015-09-01
 * TODO: Kann man $okay nicht auch über eine DB-Abfrage (via Joins) ermitteln?
 */

//-----------------------------------

$tbl_akteur = "aae_data_akteur";
$tbl_adresse = "aae_data_adresse";
$tbl_event = "aae_data_event";
$tbl_hat_user = "aae_data_hat_user";
$tbl_akteur_events = "aae_data_akteur_hat_events";
$tbl_bezirke = "aae_data_bezirke";
$tbl_sparte = "aae_data_kategorie";
$tbl_event_sparte = "aae_data_event_hat_sparte";

//-----------------------------------

require_once $modulePath . '/database/db_connect.php';
$db = new DB_CONNECT();
global $user;
//EID holen:
$path = current_path();
$explodedpath = explode("/", $path);
$event_id = $explodedpath[1];

//Prüfen, wer Schreibrechte hat
//Sicherheitsschutz, ob User entsprechende Rechte hat

$resultakteurid = db_select($tbl_akteur_events, 'e')
  ->fields('e', array( 'AID' ))
  ->condition('EID', $event_id, '=')
  ->execute();

$akteur_id = "";
$okay=""; //gibt an, ob Zugang erlaubt wird oder nicht

foreach ($resultakteurid as $row) {

  $akteur_id = $row->AID;//Akteur speichern
  //Prüfen ob Schreibrecht vorliegt: ob User zu dem Akteur gehört
  $resultUser = db_select($tbl_hat_user, 'u')
    ->fields('u', array(
      'hat_UID',
      'hat_AID',
    ))
    ->condition('hat_AID', $akteur_id, '=')
    ->condition('hat_UID', $user_id, '=')
    ->execute();
  $hat_recht = $resultUser->rowCount();

  if($hat_recht == 1) $okay = 1; //Zugang erlaubt
}

//Abfrage, ob User Ersteller des Events ist:
$ersteller = db_select($tbl_event, 'e')
  ->fields('e', array(
    'ersteller',
  ))
  ->condition('ersteller', $user->uid, '=')
  ->execute();

 if($ersteller->rowCount() == 1) $okay = 1;

 if(array_intersect(array('administrator'), $user->roles)){
  $okay = 1;
 }


//Selektion der Eventinformationen
$resultEvent = db_select($tbl_event, 'a')
  ->fields('a')
  ->condition('EID', $event_id, '=')
  ->execute()
  ->fetchAll();

 foreach($resultEvent as $event) {
  $resultEvent = $event; // Kleiner Fix, um EIN Objekt zu generieren
 }

$resultveranstalter = db_select($tbl_akteur_events, 'e')
  ->fields('e', array( 'AID' ))
  ->condition('EID', $event_id, '=')
  ->execute();

//Selektion der Tags
$resultsparten = db_select($tbl_event_sparte, 's')
  ->fields('s', array( 'hat_KID' ))
  ->condition('hat_EID', $event_id, '=')
  ->execute();

$countsparten = $resultsparten->rowCount();
$sparten = array();
$i = 0;

if($countsparten != 0){
	foreach ($resultsparten as $row) {
	  $resultspartenname = db_select($tbl_sparte, 'p')
	  ->fields('p', array(
	    'kategorie',
	  ))
	  ->condition('KID', $row->hat_KID, '=')
	  ->execute();
	  foreach ($resultspartenname as $row1) {
	  	$sparten[$i] = $row1->kategorie;
	  }
	  $i++;
	}
}

// Ausgabe des Events

ob_start(); // Aktiviert "Render"-modus

include_once path_to_theme().'/templates/single_event.tpl.php';

$profileHTML = ob_get_clean(); // Übergabe des gerenderten "events.tpl"
