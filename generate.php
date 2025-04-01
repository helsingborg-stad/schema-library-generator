#!/usr/bin/env php
<?php

if(isset($GLOBALS['_composer_autoload_path'])) {
    require $GLOBALS['_composer_autoload_path'];
} elseif (file_exists(__DIR__.'/vendor/autoload.php')) {
    require __DIR__.'/vendor/autoload.php';
} else {
    require __DIR__.'/../../autoload.php';
}

$application = new Spatie\SchemaOrg\Generator\Console\Application();

$application->run();
