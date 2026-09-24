<div class="container py-4" style="max-width: 480px;">
  <h1 class="mb-4"><?php echo htmlspecialchars($title); ?></h1>

  <form method="post" action="<?php echo url('/password'); ?>">
    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

    <div class="mb-3">
      <label for="current_password" class="form-label">Current password</label>
      <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password" required>
    </div>

    <div class="mb-3">
      <label for="new_password" class="form-label">New password</label>
      <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" autocomplete="new-password" required>
    </div>

    <div class="mb-3">
      <label for="new_password_confirm" class="form-label">Confirm new password</label>
      <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" minlength="8" autocomplete="new-password" required>
    </div>

    <button type="submit" class="btn btn-primary">Change password</button>
  </form>
</div>
