<div class="container py-4" style="max-width: 640px;">
  <h1 class="mb-4"><?php echo htmlspecialchars($title); ?></h1>

  <?php if ($has_api_key) { ?>
    <div class="alert alert-info">
      <?php echo lang_html('A_TODOIST_API_KEY_IS_ALREADY_SET_ENTER_A_NEW_KEY_BELOW_TO_REPLACE_IT'); ?>
    </div>
  <?php } else { ?>
    <div class="alert alert-secondary">
      <?php echo lang_html('NO_TODOIST_API_KEY_IS_SET_YET'); ?>
    </div>
  <?php } ?>

  <form method="post" action="<?php echo url('/todoist'); ?>">
    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

    <div class="mb-3">
      <label for="api_key" class="form-label">
        <?php echo $has_api_key ? lang('NEW_TODOIST_API_KEY') : lang('TODOIST_API_KEY'); ?>
      </label>
      <input type="password" class="form-control" id="api_key" name="api_key" maxlength="255" autocomplete="off" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary">
      <?php echo $has_api_key ? lang('CHANGE_API_KEY') : lang('SAVE_API_KEY'); ?>
    </button>
  </form>
</div>
