<div class="container py-4" style="max-width: 480px;">
  <h1 class="mb-4"><?php echo htmlspecialchars($title); ?></h1>

  <form method="post" action="<?php echo url('/login'); ?>">
    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" class="form-control" id="username" name="username" maxlength="100" autocomplete="username" required autofocus>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
    </div>

    <button type="submit" class="btn btn-primary">Login</button>
  </form>
</div>
