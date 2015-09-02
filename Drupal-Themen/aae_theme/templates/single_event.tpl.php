<h3 class="left"><?= $resultEvent->name; ?></h3>

<p class="right">
<strong>Zeit:</strong>

<?php if($resultEvent->start != "") {
  $explodedstart = explode(' ', $resultEvent->start);
  $explodedende = explode(' ', $resultEvent->ende);

  echo $explodedstart[0];

  if($resultEvent->ende != $explodedstart[0]){
   echo '- '.$explodedende[0];
  }

  if($explodedstart[1] != "" && $explodedende[1] != ""){
   echo '<br>'.$explodedstart[1].'-'.$explodedende[1].'<br>';
  }
} ?>
</p>

<div class="divider" style="padding: 20px 0 25px;"></div>

<?php if($resultEvent->kurzbeschreibung != "") : ?>
  <h4>Beschreibung</h4>
  <p><?= $resultEvent->kurzbeschreibung; ?></p>
<?php endif; ?>

<?php if($resultEvent->bild != "") : ?>
  <img style="padding: 10px 0;" src="<?= $resultEvent->bild; ?>">
<?php endif;


	//Adresse des Akteurs
	$resultAdresse = db_select($tbl_adresse, 'b')
	  ->fields('b', array(
	    'strasse',
	    'nr',
	    'plz',
	    'bezirk',
	    'gps',
	  ))
	  ->condition('ADID', $resultEvent->ort, '=')
	  ->execute();

	if($resultAdresse->rowCount() != 0){
	foreach ($resultAdresse as $row1) {

		if($row1->strasse != "" && $row1->nr != ""){
		  echo $row1->strasse.' '.$row1->nr.'<br>';
		}
		//Bezirksnamen holen:
		$resultBezirk = db_select($tbl_bezirke, 'z')
		  ->fields('z', array(
		    'bezirksname',
		  ))
		  ->condition('BID', $row1->bezirk, '=')
		  ->execute();

		foreach ($resultBezirk as $row2) {
		  if($row1->plz != ""){
		    $profileHTML .= $row1->plz.' ';
		  }
		  if($row2->bezirksname != ""){
		    $profileHTML .= $row2->bezirksname;
		  }
		  $profileHTML .= '<br>';
		}
		if($row1->gps != ""){
		  $profileHTML .= 'GPS: '.$row1->gps.'<br>';
		}
	}
	}

  if(count($sparten) != 0) { ?>
	  <p><strong>Tags:</strong>
	<?php  $laenge = count($sparten);
	  $j = 0;
	  while($j < $laenge){
	    echo $sparten[$j];
	    $j++;
	  }
    echo '</p>';
	} ?>

 <h4>Veranstalter</h4>

 <?php foreach ($ersteller as $row2) : ?>
   <p>Erstellt von: <?= $row2->name; ?></p>
 <?php endforeach; ?>

   <p>Akteur: <a href="<?= base_path(); ?>Akteurprofil/<?= $resultVeranstalter->AID; ?>"><?= $resultVeranstalter->name; ?></a></p>

  <?php if($resultEvent->url != "") : ?>
    <p>Weitere Informationen: <a href="<?= $resultEvent->url; ?>"><?= $resultEvent->url; ?></a></p>
  <?php endif; ?>

  <?= $profileHTML; ?>

<?php if($okay == 1) : ?>
  <a class="small secondary button" href="<?= base_path(); ?>Eventloeschen/<?= $eventId; ?>" >Event Löschen</a>
  <a class="small button" href="<?= base_path(); ?>Eventedit/<?= $eventId; ?>" >Event bearbeiten</a>
<?php endif; ?>
