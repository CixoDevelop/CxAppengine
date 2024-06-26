<?php
    namespace cx_appengine;

    if (defined('CX_APPENGINE_VERSION')) {
        return;
    }

    define('CX_APPENGINE_VERSION', 'V1.0');

    require_once(__DIR__.'/sources/cache.php');
    require_once(__DIR__.'/sources/directory.php');
    require_once(__DIR__.'/sources/template.php');
    require_once(__DIR__.'/sources/templates.php');
    require_once(__DIR__.'/sources/dictionary.php');
    require_once(__DIR__.'/sources/string_builder.php');
    require_once(__DIR__.'/sources/validator.php');
    require_once(__DIR__.'/sources/view.php');
    require_once(__DIR__.'/sources/activity.php');
    require_once(__DIR__.'/sources/validable_activity.php');
    require_once(__DIR__.'/sources/landing_activity.php');
    require_once(__DIR__.'/sources/cased_exception.php');
?>
