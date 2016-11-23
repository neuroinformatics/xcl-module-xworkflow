<?php

use Xworkflow\Core\LanguageManager;

if (!isset($mydirname)) {
    exit();
}

$langman = new LanguageManager($mydirname, 'admin');

if ($langman->exists('LOADED')) {
    return;
}

// system
$langman->set('LOADED', 1);
