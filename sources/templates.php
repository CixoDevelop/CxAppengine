<?php

namespace cx_appengine;

require_once(__DIR__.'/cache.php');
require_once(__DIR__.'/directory.php');
require_once(__DIR__.'/string_builder.php');
require_once(__DIR__.'/template.php');

/**
 * This class is templates manager. It create new template instances from
 * name, and file in given directory wraper.
 */
class templates {
   
    /**
     * This function create new templates wraper from directory wraper.
     *
     * @param directory $directory Library with templates.
     * @param dictionary $dictionary Dictionary with sentences to translate.
     *
     * @return templates New templates wraper.
     */
    public function __construct(
        directory $directory, 
        ?dictionary $dictionary = null
    ) {
        $this->directory = $directory;
        
        if ($dictionary !== null) {
            $this->dictionary = $dictionary;
        } else {
            $this->dictionary = new blank_dictionary();
        }
    }

    /**
     * This function copy templates with new directory.
     *
     * @return templates Copy templates with other directory.
     */
    public function copy(directory $directory) : self {
        return new self($directory, $this->get_dictionary());
    }

    /**
     * This function create new template from name in the templates directory.
     *
     * @param string $name Name of the template.
     *
     * @return template New template to render.
     */
    public function prepare(string $name) : template {
        return new template($this->get_directory()->load($name), $this);
    }

    /** 
     * This function return directory with templates.
     *
     * @return directory Directory with templates.
     */
    public function get_directory() : directory {
        return $this->directory;
    }

    /**
     * This function return templates dictionary.
     *
     * @return dictionary Dictionary for templates.
     */
    public function get_dictionary() : dictionary {
        return $this->dictionary;
    }

    /**
     * @var directory $directory
     * This variable store directory with templates.
     */
    private directory $directory;

    /**
     * @var dictionary $dictionary
     * This is dictionary used to translate sentences.
     */
    private dictionary $dictionary;

}

?>
