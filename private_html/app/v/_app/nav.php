<nav class="container py-3 d-flex align-items-center gap-3">
  <a href="<?php echo url('/'); ?>"><?php echo htmlspecialchars(APP_TITLE); ?></a>

  <?php if (auth_logged_in()) { ?>
    <span class="ms-auto">
      <?php echo htmlspecialchars(auth_username(), ENT_QUOTES, 'UTF-8'); ?>
    </span>

    <a href="<?php echo url('/password'); ?>">Change password</a>

    <form method="post" action="<?php echo url('/logout'); ?>" class="m-0">
      <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
      <button type="submit" class="btn btn-link p-0">Logout</button>
    </form>
  <?php } ?>
</nav>
