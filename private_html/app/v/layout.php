<!doctype html>
<html lang="<?php echo APP_LANG; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="generator" content="<?php echo FW_NAME . (APP_ENV != 'prod' ? ' v' . FW_VER : ''); ?>">
  <title><?php echo htmlspecialchars(isset($title) ? $title : APP_TITLE); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link href="<?php echo url('css/app.css'); ?>?v=<?php echo APP_VER; ?><?php echo (APP_ENV != 'prod' ? '&t=' . time() : ''); ?>" rel="stylesheet">
  <link rel="icon" href="<?php echo url('favicon.ico'); ?>">
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

<script src="https://code.jquery.com/jquery-4.0.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
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
