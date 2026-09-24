<div class="container py-4" style="max-width: 640px;">
  <h1 class="mb-4"><?php echo htmlspecialchars($title); ?></h1>

  <?php if ($has_api_key) { ?>
    <div class="alert alert-info">
      A Todoist API key is already set. Enter a new key below to replace it.
    </div>
  <?php } else { ?>
    <div class="alert alert-secondary">
      No Todoist API key is set yet.
    </div>
  <?php } ?>

  <form method="post" action="<?php echo url('/todoist'); ?>">
    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

    <div class="mb-3">
      <label for="api_key" class="form-label">
        <?php echo $has_api_key ? 'New Todoist API key' : 'Todoist API key'; ?>
      </label>
      <input type="password" class="form-control" id="api_key" name="api_key" maxlength="255" autocomplete="off" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary">
      <?php echo $has_api_key ? 'Change API key' : 'Save API key'; ?>
    </button>
  </form>
</div>
