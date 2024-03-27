<?php

namespace cx_appengine;

require_once(__DIR__.'/string_builder.php');
require_once(__DIR__.'/validator.php');
require_once(__DIR__.'/activity.php');
require_once(__DIR__.'/validable_activity.php');

/**
 * This class is landing activity, that mean default activity in the view. It
 * could be declarated used normal activity, by returns null from 
 * show_after_button(), but this class can make it cleaner.
 */
abstract class landing_activity extends validable_activity {
    
    /** 
     * Default landing activity must return null.
     *
     * @return null Default landing activity must return null.
     */
    public final function show_after_button() : null {
        return null;
    }

    /**
     * Default landing activity can not get any init inputs.
     *
     * @return array<string, string> [] Empty init inputs.
     */
    public final function init_inputs() : array {
        return [];
    }

}

?>
