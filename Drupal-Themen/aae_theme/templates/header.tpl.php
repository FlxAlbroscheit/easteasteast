<div id="mainnav">
 <div class="row">

 <nav id="nav" role="navigation">
  <a href="#nav" class="show-for-small-only" title="Show navigation">&#9776;</a>
  <a href="#" class="show-for-small-only" title="Hide navigation">&#9776;</a>

  <?php if ($main_menu): ?>

      <?php print render($page['mainnav']); ?>

  <?php endif; ?>

 </nav>

  <aside id="actions" class="large-1 small-2 columns panel radius right">
   <a id="search-button" href="#search-popup" class="popup-link" title="Suchen"><img src="<?= base_path().path_to_theme(); ?>/img/search.svg" /></a>
    <div id="search-popup" class="popup large-3 columns">
     <!-- <input type="text" placeholder="Suchen..." />
     <input type="submit" class="button secondary isSubmitButton" value="" /> -->
     <?php print drupal_render(drupal_get_form('search_block_form', TRUE)); ?>
    </div>

   <a href="#login-popup" class="popup-link" title="Einloggen"><img src="<?= base_path().path_to_theme(); ?>/img/user.svg" /></a>
    <div id="login-popup" class="popup large-3 columns">

     <h4>Login</h4>

     <!-- IF (!is_logged_in) -->
     <?php print drupal_render(drupal_get_form('user_login_block')); ?>

     <div class="divider"></div>

     <p>Neu hier? <a href="<?= base_path(); ?>?q=user/register">Registrieren</a></p>
     <p><a href="<?= base_path(); ?>?q=user/password">Passwort vergessen?</a></p>

     <div class="divider"></div>
     <?= print render($page['user']); ?>
    </div>
  </aside>

 </div>
</div>
