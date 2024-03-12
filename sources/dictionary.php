<?php

namespace cx_appengine;

require_once(__DIR__.'/string_builder.php');

/**
 * This is abstract class to building dictionaries.
 */
abstract class dictionary {
    
    /** 
     * This function wound translate content.
     *
     * @param string_builder $what Content to translate.
     *
     * @return string_builder Translated content.
     */
    public abstract function translate(
        string_builder $what
    ) : string_builder;

}

/**
 * This is simple blank dictionary.
 */
class blank_dictionary extends dictionary {
    
    /**
     * Is does not require constructor.
     *
     * @return test_dictionary New dictionary.
     */
    public function __construct() {}

    /**
     * This function would simply add info to string.
     *
     * @param string_builder $what Text to translate.
     *
     * @return string_builder Translated text.
     */
    public function translate(string_builder $what) : string_builder {
        return $what->clone()->push_start('TRANSLATE: ');
    }

}

?>
