<?php

function aae_preprocess_html(&$variables) {

  drupal_add_js('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');

  drupal_add_js(path_to_theme().'/js/jquery.fullPage.min.js');
  drupal_add_js(path_to_theme().'/js/pace.min.js');
  drupal_add_js(path_to_theme().'/js/doubletaptogo.min.js');
  drupal_add_js(path_to_theme().'/js/app.js');

  drupal_add_css(path_to_theme().'/css/foundation.css');
  drupal_add_css('http://fonts.googleapis.com/css?family=Open+Sans:400,300', array('type' => 'external'));
  drupal_add_css(path_to_theme().'/css/pace.css');
  drupal_add_css(path_to_theme().'/css/app.css');


  if (drupal_is_front_page()) {
    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css', array('type' => 'external'));
    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css', array('type' => 'external'));
    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css', array('type' => 'external'));
    drupal_add_css(path_to_theme().'/css/jquery.fullPage.css');
    drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js');
    drupal_add_js(path_to_theme().'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js');

    drupal_add_js(path_to_theme().'/js/home.js');
 }

  if (user_access('administer')) {
   echo '<!-- IF IS_ADMIN -->';
   echo '<style type="text/css">#mainnav { margin-top: -13px !important; z-index: 20 !important; }</style>';
   echo '<!-- /IF -->';
 }
}

/**
* Überschreibt das Login-Form in header.tpl.php
*/

function aae_form_alter(&$form, &$form_state, $form_id) {


  if ( TRUE === in_array( $form_id, array( 'user_login', 'user_login_block') ) ) {

    $form['name']['#attributes']['placeholder'] = t( 'Benutzername' );
    $form['pass']['#attributes']['placeholder'] = t( 'Passwort' );
    $form['name']['#title_display'] = "invisible";
    $form['pass']['#title_display'] = "invisible";

    $form['links']['#markup'] = '';

    $form['actions']['submit']['#attributes']['class'][] = 'small button';
    $form['actions']['submit']['#value'] = 'Einloggen';
    $form['name']['#description'] = t('');
    $form['pass']['#description'] = t('');

  } else {

    $form['actions']['submit']['#attributes']['class'][] = 'small button';
    $form['actions']['submit']['#value'] = 'Suche...';

  }
}
?>