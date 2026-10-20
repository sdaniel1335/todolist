<div class="container login-page">
  <section class="login-card" aria-labelledby="login-title">
  <div class="login-icon" aria-hidden="true"><i class="bi bi-person-lock"></i></div>
  <h1 id="login-title" class="mb-4"><?php echo htmlspecialchars($title); ?></h1>

  <form method="post" action="<?php echo url('/login'); ?>">
    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

    <div class="mb-3">
      <label for="username" class="form-label"><?php echo lang_html('USERNAME'); ?></label>
      <input type="text" class="form-control" id="username" name="username" maxlength="100" autocomplete="username" required autofocus>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label"><?php echo lang_html('PASSWORD'); ?></label>
      <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
    </div>

    <button type="submit" class="btn btn-primary login-submit"><?php echo lang_html('LOGIN'); ?></button>
  </form>
  </section>
</div>
