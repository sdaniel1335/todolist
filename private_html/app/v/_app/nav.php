<nav class="container py-3 d-flex flex-wrap align-items-center gap-2 gap-md-3 app-nav">
  <a class="app-brand" href="<?php echo url('/'); ?>"><span class="app-brand-icon" aria-hidden="true"><i class="bi bi-check2-square"></i></span><?php echo htmlspecialchars(APP_TITLE); ?></a>

  <?php if (auth_logged_in()) { ?>
    <span class="ms-md-auto me-auto me-md-0 app-user">
      <?php echo htmlspecialchars(auth_username(), ENT_QUOTES, 'UTF-8'); ?>
    </span>

    <a class="app-nav-link" href="<?php echo url('/todoist'); ?>"><?php echo lang_html('TODOIST_API'); ?></a>
    <a class="app-nav-link" href="<?php echo url('/password'); ?>"><?php echo lang_html('CHANGE_PASSWORD'); ?></a>

    <form method="post" action="<?php echo url('/logout'); ?>" class="m-0">
      <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
      <button type="submit" class="btn btn-link app-logout"><?php echo lang_html('LOGOUT'); ?></button>
    </form>
  <?php } ?>
</nav>
