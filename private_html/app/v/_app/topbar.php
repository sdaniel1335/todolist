<?php
/**
 * File: topbar.php
 * File version: 0.1.0 (version of this file only, not the project)
 * DO NOT MODIFY THIS FILE. This prohibition applies to everyone,
 * including AI assistants, automated tools, and human contributors.
 */
$topbar_logged_in = auth_logged_in();
$topbar_lifetime = max(0, (int) ini_get('session.gc_maxlifetime'));
$module_root = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
$current_module = realpath(ROOT);
$modules = array();
if ($topbar_logged_in && $module_root !== false && is_dir($module_root) && is_readable($module_root)) {
  $entries = scandir($module_root);
  if ($entries !== false) {
    foreach ($entries as $entry) {
      if ($entry === '.' || $entry === '..') { continue; }
      $directory = $module_root . DIRECTORY_SEPARATOR . $entry;
      if (is_dir($directory) && is_file($directory . DIRECTORY_SEPARATOR . 'MODULE')) {
        $modules[$entry] = realpath($directory) === $current_module;
      }
    }
  }
}
uksort($modules, 'strcasecmp');
?>
<nav id="module-topbar" style="background-color: #f2f2f2; border-bottom: 1px solid #666;<?php echo $topbar_logged_in ? '' : ' display: none;'; ?>" aria-label="<?php echo htmlspecialchars(lang('LANG_MODULES'), ENT_QUOTES, 'UTF-8'); ?>">
  <div style="display: flex; flex-wrap: wrap; justify-content: flex-end; align-items: center; gap: 1rem; padding: 0.25rem 1rem 0.25rem 0; font-size: 0.875em;">
    <span id="module-session-status" style="background-color: <?php echo $topbar_logged_in ? '#198754' : '#dc3545'; ?>; color: #fff; padding: 0.125rem 0.375rem; border-radius: 0.25rem;" title="<?php echo htmlspecialchars(lang('LANG_ESTIMATED_SESSION_TIME_REFRESH_TO_CHECK_LOGIN_STATUS'), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $topbar_logged_in ? lang('LANG_SESSION') : lang('LANG_EXPIRED'); ?></span>
    <?php foreach ($modules as $name => $active) { ?>
      <?php if ($active) { ?>
        <strong aria-current="page"><?php echo htmlspecialchars(ucfirst($name), ENT_QUOTES, 'UTF-8'); ?></strong>
      <?php } else { ?>
        <a href="/<?php echo rawurlencode($name); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars(ucfirst($name), ENT_QUOTES, 'UTF-8'); ?></a>
      <?php } ?>
    <?php } ?>
  </div>
</nav>
<script>
  (function () {
    var loggedIn = <?php echo $topbar_logged_in ? 'true' : 'false'; ?>;
    var key = 'module-session-seen:' + <?php echo json_encode(BASE_URL); ?>;
    var bar = document.getElementById('module-topbar');
    var badge = document.getElementById('module-session-status');
    try {
      if (loggedIn) {
        sessionStorage.setItem(key, '1');
      } else if (sessionStorage.getItem(key) === '1') {
        bar.style.display = '';
      }
    } catch (error) {}
    if (!loggedIn) { return; }
    var deadline = Date.now() + <?php echo $topbar_lifetime; ?> * 1000;
    function update() {
      var remaining = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
      if (remaining === 0) {
        badge.textContent = <?php echo json_encode(lang('LANG_SESSION_REFRESH_TO_CHECK')); ?>;
        badge.style.backgroundColor = '#FF0000';
        return;
      }
      var minutes = Math.floor(remaining / 60);
      var seconds = remaining % 60;
      badge.textContent = <?php echo json_encode(lang('LANG_SESSION_TEXT')); ?> + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    }
    update();
    window.setInterval(update, 1000);
  }());
</script>
