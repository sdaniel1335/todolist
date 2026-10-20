<?php

// Resolve scalar translation constants; compatible with PHP 5.3.
function lang($key)
{
  $language = defined('APP_LANG') ? strtoupper(APP_LANG) : 'EN';
  $constant = 'LANG_' . $language . '_' . $key;

  if (!defined($constant)) {
    $constant = 'LANG_EN_' . $key;
  }

  return constant($constant);
}

function lang_html($key)
{
  return htmlspecialchars(lang($key), ENT_QUOTES, 'UTF-8');
}
