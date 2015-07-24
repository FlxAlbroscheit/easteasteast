<?php
/**
 * akteurloeschen.php löscht einen Akteur aus der DB
 * (nach vorheriger Abfrage).
 *
 * Ruth, 2015-07-20
 */

//Eingeloggter User
global $user;
$user_id = $user->uid;

//EID holen:
$path = current_path();
$explodedpath = explode("/", $path);
$akteur_id = $explodedpath[1];

//DB-Tabellen
$tbl_hat_user = "aae_data_hat_user";
$tbl_akteur_events = "aae_data_akteur_hat_events";
$tbl_akteur = "aae_data_akteur";

//Sicherheitsschutz
if(!user_is_logged_in()){
  drupal_access_denied();
}

//Prüfen ob Schreibrecht vorliegt
$resultUser = db_select($tbl_hat_user, 'u') 
 ->fields('u', array(
    'hat_UID',
    'hat_AID',
  ))
  ->condition('hat_AID', $akteur_id, '=')
  ->condition('hat_UID', $user_id, '=')
  ->execute();
$hat_recht = $resultUser->rowCount();

if(!array_intersect(array('redakteur','administrator'), $user->roles)){
  if($hat_recht != 1){
    drupal_access_denied();
  }
}
//AKteurnamen ermitteln
$akteur = "";
$resultakteur = db_select($tbl_akteur, 'a') 
 ->fields('a', array(
    'name',
  ))
  ->condition('AID', $akteur_id, '=')
  ->execute();
foreach ($resultakteur as $row) {
  $akteur = $row->name;
}


//-----------------------------------

//das wird ausgeführt, wenn auf "Löschen" gedrückt wird
if (isset($_POST['submit'])) {
	
  $akteur_id = $_POST['akteur_id'];
  require_once $modulePath . '/database/db_connect.php';
  //include $modulePath . '/templates/utils/rest_helper.php'; Ist aus dem Künstlermodul übernommen
  $db = new DB_CONNECT();

  //Akteur aus $tbl_akteur_events loeschen
  $akteureventloeschen = db_delete($tbl_akteur_events)
    ->condition('AID', $akteur_id, '=')
    ->execute();
  //Akteur aus $tbl_hat_user loeschen
  $akteuruserloeschen = db_delete($tbl_hat_user)
    ->condition('hat_AID', $akteur_id, '=')
    ->execute();

  //Akteur aus DB loeschen
  $akteurloeschen = db_delete($tbl_akteur)
    ->condition('AID', $akteur_id, '=')
    ->execute();

  header("Location: ?q=Akteure"); //Hier muss hin, welche Seite aufgerufen werden soll,
		//nach dem die Daten erfolgreich gespeichert wurden.
	
} else{
	
}

$pathThisFile = $_SERVER['REQUEST_URI']; 

//Darstellung
$profileHTML = <<<EOF
  <p>Möchten Sie den Akteur $akteur wirklich löschen?</p><br>
  <form action='$pathThisFile' method='POST' enctype='multipart/form-data'>
    <input name="akteur_id" type="hidden" id="akteurAIDInput" value="$akteur_id" />
    <a href="javascript:history.go(-1)">Abbrechen</a>
    <input type="submit" class="akteur" id="akteurSubmit" name="submit" value="Loeschen">
  </form>
EOF;