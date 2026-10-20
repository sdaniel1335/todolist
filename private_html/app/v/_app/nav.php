<nav class="container py-3 d-flex flex-wrap align-items-center gap-2 gap-md-3">
  <a href="<?php echo url('/'); ?>"><?php echo htmlspecialchars(APP_TITLE); ?></a>

  <?php if (auth_logged_in()) { ?>
    <span class="ms-md-auto me-auto me-md-0">
      <?php echo htmlspecialchars(auth_username(), ENT_QUOTES, 'UTF-8'); ?>
    </span>

    <a href="<?php echo url('/todoist'); ?>"><?php echo lang_html('TODOIST_API'); ?></a>
    <a href="<?php echo url('/password'); ?>"><?php echo lang_html('CHANGE_PASSWORD'); ?></a>

    <form method="post" action="<?php echo url('/logout'); ?>" class="m-0">
      <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
      <button type="submit" class="btn btn-link p-0"><?php echo lang_html('LOGOUT'); ?></button>
    </form>
  <?php } ?>
</nav>
