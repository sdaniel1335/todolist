<!doctype html>
<html lang="<?php echo APP_LANG; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="generator" content="<?php echo FW_NAME . (APP_ENV != 'prod' ? ' v' . FW_VER : ''); ?>">
  <title><?php echo htmlspecialchars(isset($title) ? $title : APP_TITLE); ?></title>
  <link href="<?php echo url('css/app.css'); ?>?v=<?php echo APP_VER; ?><?php echo (APP_ENV != 'prod' ? '&t=' . time() : ''); ?>" rel="stylesheet">
</head>
<body class="js">

<header>
  <?php element('nav'); ?>
</header>

<?php foreach (flash() as $flash) { ?>
  <div class="container mt-3">
    <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : htmlspecialchars($flash['type']); ?>">
      <?php echo htmlspecialchars($flash['message']); ?>
    </div>
  </div>
<?php } ?>

<main>
  <?php echo $content; ?>
</main>

<footer>
  &copy; <?php echo date('Y') . ' ' . APP_TITLE; ?> &middot; Powered by <a href="<?php echo FW_PROJECT_URL; ?>" target="_blank" rel="noopener"><?php echo FW_NAME . (APP_ENV != 'prod' ? ' v' . FW_VER : ''); ?></a>
</footer>

<script src="<?php echo url('js/app.js'); ?>?v=<?php echo APP_VER; ?><?php echo (APP_ENV != 'prod' ? '&t=' . time() : ''); ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof App !== 'undefined') {
      App.init();
    }
  });
</script>
</body>
</html>
