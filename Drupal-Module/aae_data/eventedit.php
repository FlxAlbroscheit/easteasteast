<?php
/**
 * eventformular.php stellt ein Formular dar,
 * in welches alle Informationen über eine Veranstaltung
 * eingetragen werden können.
 * Pflichtfelder sind: Name, Veranstalter, Datum.
 * Anschließend werden die Daten in die DB-Tabellen eingetragen.
 *
 * Ruth, 2015-07-20
 */


//DB-Tabellen
$tbl_adresse = "aae_data_adresse";
$tbl_event = "aae_data_event";
$tbl_akteur = "aae_data_akteur";
$tbl_hat_user = "aae_data_hat_user";

//Eingeloggter User
global $user;
$user_id = $user->uid;

//EID holen:
$path = current_path();
$explodedpath = explode("/", $path);
$event_id = $explodedpath[1];

//Sicherheitsschutz
if(!user_is_logged_in()){
  drupal_access_denied();
}

$resultakteurid = db_select($tbl_event, 'e')
  ->fields('e', array(
    'veranstalter',
    'name',
  ))
  ->condition('EID', $event_id, '=')
  ->execute(); 
$akteur_id = "";
$event = "";
foreach ($resultakteurid as $row) {
  $akteur_id = $row->veranstalter;
  $event = $row->name;
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

//-----------------------------------

//Variablen zum Speichern von Werten, welche in die DB-Tabellen eingefügt werden sollen
//$tbl_event
$name = "";
$veranstalter = "";
$start = "";
$zeit_von = "";
$zeit_bis = "";
$ende = "";
$bild = "";
$kurzbeschreibung = "";
$url = "";

//$tbl_adresse
$strasse = "";
$nr = "";
$adresszusatz = "";
$plz = "";
$ort = "";
$gps = "";
$adresse = "";

//Speicherort fuer Bilder
$bildpfad = "sites/all/modules/aae_data/images/";
$bild_alt="";

//-----------------------------------

//Variable zur Freigabe: muss true sein
$freigabe = true;

//Fehlervariablen
$fehler_name = "";
$fehler_veranstalter = "";
$fehler_start = "";
$fehler_zeit_von = "";
$fehler_zeit_bis = "";
$fehler_ende = "";
$fehler_bild = "";
$fehler_kurzbeschreibung = "";
$fehler_url = "";
$fehler_strasse = "";
$fehler_nr = "";
$fehler_adresszusatz = "";
$fehler_plz = "";
$fehler_ort = "";
$fehler_gps = "";

//-----------------------------------

//Variablen, welche Texte in den Formularfeldern halten
$ph_name = "Veranstaltungsname";
$ph_veranstalter = "Veranstalter";
$ph_start = "Starttag (dd.mm.yyyy)";
$ph_zeit_von = "von (Uhrzeit: hh:mm)";
$ph_zeit_bis = "bis (Uhrzeit: hh:mm)";
$ph_ende = "Endtag (dd:mm:yyyy)";
$ph_bild = "Bild";
$ph_kurzbeschreibung = "Beschreibung";
$ph_ort = "Ort der Veranstaltung";
$ph_url = "URL";
$ph_strasse = "Strasse";
$ph_nr = "Hausnummer";
$ph_adresszusatz = "Adresszusatz";
$ph_plz = "PLZ";
$ph_ort = "Ort/Stadt";
$ph_gps = "GPS Koordinaten (durch Leerzeichen getrennt!)";

//-----------------------------------

//das wird ausgeführt, wenn auf "Speichern" gedrückt wird
if (isset($_POST['submit'])) {
  $event_id=$_POST['event_id'];
  //Wertezuweisung
  $name = $_POST['name'];
  $veranstalter = $_POST['veranstalter'];
  $start = $_POST['start'];
  $url = $_POST['url'];
  $ende = $_POST['ende'];
  $zeit_von = $_POST['zeit_von'];
  $zeit_bis = $_POST['zeit_bis'];
  if(isset($_POST['bild'])){
    $bild = $_POST['bild'];
  }
  $bild_alt=$_POST['bild_alt'];
  $kurzbeschreibung = $_POST['kurzbeschreibung'];

  $strasse = $_POST['strasse'];
  $nr = $_POST['nr'];
  $adresszusatz = $_POST['adresszusatz'];
  $plz = $_POST['plz'];
  $ort = $_POST['ort'];
  $gps = $_POST['gps'];
	
//-------------------------------------
  //Check-Klauseln
	
  //Check, ob ein Name eingegeben wurde:
  if(strlen($name) == 0){
    //Feld nicht ausgefüllt
    $fehler_name = "Bitte einen Veranstaltungsnamen eingeben!";
	$freigabe = false;
  }
  //Ckeck, ob Veranstalter angegeben wurde
  if(strlen($veranstalter) == 0){
    //Feld nicht ausgefüllt
    $fehler_veranstalter = "Bitte einen Veranstalter auswählen!";
	$freigabe = false;
  }
  //Ckeck, ob Datum angegeben wurde
  if(strlen($start) == 0){
    //Feld nicht ausgefüllt
    $fehler_start = "Bitte eine Datum angeben!";
	$freigabe = false;
  }

  //überflüssige Leerzeichen am Anfang entfernen
  $name=trim($name);  
  $veranstalter = trim($veranstalter);
  $start = trim($start);
  $zeit_von = trim($zeit_von);
  $zeit_bis = trim($zeit_bis);
  $ende = trim($ende);
  $url = trim($url); 
  $bild = trim($bild); 
  $kurzbeschreibung = trim($kurzbeschreibung); 
  $strasse = trim($strasse);
  $nr = trim($nr);
  $adresszusatz = trim($adresszusatz);
  $plz = trim($plz);
  $ort = trim($ort);
  $gps = trim($gps);

  //und alle Tags entfernen (Hacker)
  $name=strip_tags($name);
  $veranstalter = strip_tags($veranstalter);
  $start = strip_tags($start);
  $zeit_von = strip_tags($zeit_von);
  $zeit_bis = strip_tags($zeit_bis);
  $ende = strip_tags($ende);
  $url = strip_tags($url);  
  $bild = strip_tags($bild); 
  $kurzbeschreibung = strip_tags($kurzbeschreibung); 
  $strasse = strip_tags($strasse);
  $nr = strip_tags($nr);
  $adresszusatz = strip_tags($adresszusatz);
  $plz = strip_tags($plz);
  $ort = strip_tags($ort);
  $gps = strip_tags($gps);

  //Abfrage, ob Einträge nicht länger als in DB-Zeichen lang sind.
  if (strlen($name) > 100){
	$fehler_name = "Bitte geben Sie einen kuerzeren Namen an oder verwenden Sie ein Kuerzel.";
	$freigabe = false;
  }
  if (strlen($url) > 200){
	$fehler_url = "Bitte geben Sie eine kuerzere URL an.";
	$freigabe = false;
  }
  if (strlen($kurzbeschreibung) > 500){
	$fehler_kurzbeschreibung = "Bitte geben Sie eine kuerzere Beschreibung an.";
	$freigabe = false;
  }
  if (strlen($strasse) > 100){
	$fehler_strasse = "Bitte geben Sie einen kuerzeren Strassennamen an.";
	$freigabe = false;
  }
  if (strlen($nr) > 100){
	$fehler_nr = "Bitte geben Sie eine kuerzere Nummer an.";
	$freigabe = false;
  }
  if (strlen($adresszusatz) > 100){
	$fehler_adresszusatz = "Bitte geben Sie einen kuerzeren Adresszusatz an.";
	$freigabe = false;
  }
  if (strlen($plz) > 100){
	$fehler_plz = "Bitte geben Sie eine kuerzere PLZ an.";
	$freigabe = false;
  }
  if (strlen($ort) > 100){
	$fehler_ort = "Bitte geben Sie einen kuerzeren Ortsnamen an.";
	$freigabe = false;
  }
  if (strlen($gps) > 100){
	$fehler_gps = "Bitte geben Sie kuerzere GPS-Daten an.";
	$freigabe = false;
  }

  //Wenn Bilddatei ausgewählt wurde...
  if($_FILES){
	$bildname = $_FILES['bild']['name'];
	if($bildname != ""){
	  move_uploaded_file($_FILES['bild']['tmp_name'], $bildpfad.$bildname);//Upload
	  $bild = "images/".$bildname;
	}else{
	  $bild=$bild_alt;
	}
  }
  
//---------------------------------

  //Wenn $goodtogo true, ab in die DB mit den Daten
  if($freigabe == true){
	require_once $modulePath . '/database/db_connect.php';
	//include $modulePath . '/templates/utils/rest_helper.php'; Ist aus dem Künstlermodul übernommen
	$db = new DB_CONNECT();
	//Das Ergebnis von db_insert()->...->execute(); ist die ID, von diesem Eintrag
	
	//Abfrage, ob Adresse bereits in Adresstabelle
	//Addressdaten aus DB holen:
	$resultadresse = db_select($tbl_adresse, 'a')
	  ->fields('a', array(
	    'ADID',
		'gps',
	  ))
	  ->condition('strasse', $strasse, '=')
	  ->condition('nr', $nr, '=')
	  ->condition('adresszusatz', $adresszusatz, '=')
	  ->condition('plz', $plz, '=')
	  ->condition('ort', $ort, '=')
	  ->execute();
	
	//wenn ja: Holen der ID der Adresse, wenn nein: Einfuegen
    $i = $resultadresse->rowCount();
	if($i == 0){//Adresse nicht vorhanden
	  $adresse = db_insert($tbl_adresse)
	    ->fields(array(
		  'strasse' => $strasse,
		  'nr' => $nr,
		  'adresszusatz' => $adresszusatz,
		  'plz' => $plz,
		  'ort' => $ort,
		  'gps' => $gps,
		))
		->execute();
	}else {//Adresse bereits vorhanden
	  foreach ($resultadresse as $row) {
	    //Abfrage, ob GPS-Angaben gemacht wurden
	    if(strlen($gps) != 0 && strlen($row->gps) == 0 ){//ja UND es sind bisher keine GPS-Daten zu der Adresse in der DB
	      //Update der Adresse  
	      $adresse_updated = db_update($tbl_adresse)
	 	    ->fields(array(
			  'gps' => $gps,
	        ))
	        ->condition('ADID', $row->ADID, '=')
	        ->execute();
	    }
	    $adresse = $row->ADID;//Adress-ID merken
	  }
	}
	
	//Zeitformatierung
	if(strlen($ende) == 0){
	  $ende = $start;
	}
	$start = $start.' '.$zeit_von.' '.$zeit_bis;

    //tbl_event UPDATE!!!
	$eventupdate = db_update($tbl_event)
   	  ->fields(array(
		'name' => $name,
		'ort' => $adresse,
		'veranstalter' => $veranstalter,
		'start' => $start,
		'url' => $url,
		'ende' => $ende,
		'bild' => $bild,
		'kurzbeschreibung' => $kurzbeschreibung,
	  ))
	  ->condition('EID', $event_id, '=')
	  ->execute();
		
	header("Location: ?q=Eventprofil/$event_id"); //Hier muss hin, welche Seite aufgerufen werden soll,
	  //nach dem die Daten erfolgreich gespeichert wurden.
	}
	
}else{
  //Erstmaliger Aufruf: Daten aus DB in Felder schreiben
  require_once $modulePath . '/database/db_connect.php';
  $db = new DB_CONNECT();

    //Auswahl der Daten des ausgewählten Events
  $resultevent = db_select($tbl_event, 'e')
    ->fields('e', array(	
	  'name',
	  'veranstalter',
	  'start',
	  'ende',
	  'url',
	  'bild',
	  'kurzbeschreibung',
	  'ort',
	))
	->condition('EID', $event_id, '=')
    ->execute();
  //Speichern der Daten in den Arbeitsvariablen
  foreach($resultevent as $row){
	$name = $row->name;
	$ort = $row->ort;//Address-ID: Addressinformationen muessen aus Addressdbtabelle geholt werden
	$start = $row->start;
	$ende = $row->ende;
	$url = $row->url;
	$bild = $row->bild;
	$kurzbeschreibung = $row->kurzbeschreibung;
	$veranstalter = $row->veranstalter;//Akteur-ID: Akteurinformationen muessen aus Akteurtabelle geholt werden
  }
  $akteur_id = $veranstalter;
  //Adressdaten aus DB holen:
  $resultadresse = db_select($tbl_adresse, 'd')
    ->fields('d', array(	
	  'strasse',
	  'nr',
	  'adresszusatz',
	  'plz',
	  'ort',
	  'gps',
	))
	->condition('ADID', $ort, '=')
    ->execute();
  //Speichern der Adressdaten in den Arbeitsvariablen
  foreach ($resultadresse as $row) {
	$strasse = $row->strasse;
	$nr = $row->nr;
	$adresszusatz = $row->adresszusatz;
	$plz = $row->plz;
	$ort = $row->ort;
	$gps = $row->gps;
  }
  //Akteurnamen aus DB holen:
  //Adressdaten aus DB holen:
  $resultakteur = db_select($tbl_akteur, 'a')
    ->fields('a', array(	
	  'name',
	))
	->condition('AID', $veranstalter, '=')
    ->execute();
  //Speichern der Adressdaten in den Arbeitsvariablen
  foreach ($resultakteur as $row) {
	$veranstalter = $row->name;
  }
  //Zeit auflösen
  $explodedstart=explode(' ', $start);
  $start=$explodedstart[0];
  $zeit_von="";
  $zeit_bis="";
  if(count($explodedstart) == 2){
	$zeit_von=$explodedstart[1];
  }else if (count($explodedstart)==3) {
	$zeit_von=$explodedstart[1];
	$zeit_bis=$explodedstart[2];
  }

}

$pathThisFile = $_SERVER['REQUEST_URI']; 

//Darstellung
$profileHTML = <<<EOF
<form action='$pathThisFile' method='POST' enctype='multipart/form-data'>
  <input name="event_id" type="hidden" id="eventEIDInput" value="$event_id" />
  <!-- verstecktes Feld für bild -->
  <input name="bild_alt" type="hidden" id="bild_alt" value="$bild" />

  <label>Name (Pflichtfeld):</label>
  <input type="text" class="event" id="eventNameInput" name="name" value="$name" placeholder="$ph_name" required>$fehler_name

  <label>Veranstalter (Pflichtfeld):</label>
  <!--<input type="text" class="event" id="eventVeranstalterInput" name="veranstalter" value="$veranstalter" placeholder="$ph_veranstalter">$fehler_veranstalter-->

EOF;

//Akteure abfragen, die in DB
$resultakteure = db_select($tbl_akteur, 'a')
  ->fields('a', array(
    'AID',
	'name',
  ))
  ->execute();
$countakteure = $resultakteure->rowCount();
//Dropdownliste zur Akteurauswahl
$profileHTML .= '<select name="veranstalter" size="'.$countakteure.'" >';
foreach ($resultakteure as $row) {
  if($row->AID == $akteur_id){
	$profileHTML .= '<option value="'.$row->AID.'" selected="selected" >'.$row->name.'</option>';
  }else{
	$profileHTML .= '<option value="'.$row->AID.'">'.$row->name.'</option>';
  }
}
$profileHTML .= '</select>';

$profileHTML .= <<<EOF
  <label>Datum (Pflichtfeld):</label>
  <input type="text" class="event" id="eventStartdatumInput" name="start" value="$start" placeholder="$ph_start">$fehler_start
  <label>von (Uhrzeit; ganztägig: keine Uhrzeit angeben):</label>
  <input type="text" class="event" id="eventZeitvonInput" name="zeit_von" value="$zeit_von" placeholder="$ph_zeit_von">$fehler_zeit_von
  <label>bis (Uhrzeit; ganztägig: keine Uhrzeit angeben):</label>
  <input type="text" class="event" id="eventZeitbisInput" name="zeit_bis" value="$zeit_bis" placeholder="$ph_zeit_bis">$fehler_zeit_bis
  <label>Ende (Datum):</label>
  <input type="text" class="event" id="eventEnddatumInput" name="ende" value="$ende" placeholder="$ph_ende">$fehler_ende

  <label>Addresse:</label>
  <label>Straße:</label>
  <input type="text" class="event" id="eventStrasseInput" name="strasse" value="$strasse" placeholder="$ph_strasse">$fehler_strasse
  <label>Nr.:</label>
  <input type="text" class="event" id="eventNrInput" name="nr" value="$nr" placeholder="$ph_nr">$fehler_nr
  <label>Adresszusatz:</label>
  <input type="text" class="event" id="eventAdresszusatzInput" name="adresszusatz" value="$adresszusatz" placeholder="$ph_adresszusatz">$fehler_adresszusatz
  <label>PLZ:</label>
  <input type="text" class="event" id="eventPLZInput" name="plz" value="$plz" placeholder="$ph_plz">$fehler_plz
  <label>Stadt:</label>
  <input type="text" class="event" id="eventOrtInput" name="ort" value="$ort" placeholder="$ph_ort">$fehler_ort
  <label>Geodaten:</label>
  <input type="text" class="event" id="eventGPSInput" name="gps" value="$gps" placeholder="$ph_gps">$fehler_gps
	

  <label>Website:</label>
  <input type="text" class="event" id="akteurURLInput" name="url" value="$url" placeholder="$ph_url">$fehler_url


  <label>Beschreibung:</label>
  <textarea name="kurzbeschreibung" class="event" cols="45" rows="3" placeholder="$ph_kurzbeschreibung">$kurzbeschreibung</textarea>$fehler_kurzbeschreibung
  <label>Bild:</label>
  <input type="file" class="event" id="akteurBildInput" name="bild" /><br>


  <input type="submit" class="event" id="akteureSubmit" name="submit" value="Speichern">
</form>
<a href="javascript:history.go(-1)">Abbrechen/Zurück</a>
EOF;
