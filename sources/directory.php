<?php

namespace cx_appengine;

require_once(__DIR__.'/cache.php');

/**
 * This is enum which contain exception cases for directory.
 */
enum directory_exception_case {
    
    /** 
     * This would be throw when directory not exists.
     */
    case not_exists;

}

/**
 * This is exception for the directory.
 */
class directory_exception extends \Exception {

    /**
     * This create exception based on exception case.
     *
     * @param directory_exception_case $case Exception case.
     */
    public function __construct(directory_exception_case $case) {
        match ($case) {
            directory_exception_case::not_exists => 
                parent::__construct('Directory not exists.', 2000),
        };
    }

}

/**
 * This is directory wraper, which could wrap directory, and its type.
 */
class directory {
    
    /** 
     * This function build new wrapper.
     *
     * @param string $path Path to directory which would be wraped.
     * @param string $type Type of wraped files.
     *
     * @throws directory_exception When directory not exists.
     *
     * @return directory New directory wraper.
     */
    public function __construct(string $path, string $type = '') {
        if (!is_dir($path)) {
            throw new directory_exception(
                directory_exception_case::not_exists
            );
        }

        $this->path = realpath($path);
        $this->type = $type;
    }

    /**
     * This function create new wraper to the file.
     *
     * @param string $name Name of the file in directory.
     *
     * @return cache New wraper to the file.
     */
    public function load(string $name) : cache {
        return new cache($this->get_path().$this->get_name($name));
    }

    /**
     * This function return path to the wrapped directory.
     *
     * @return string Path to the wraped directory.
     */
    public function get_path() : string {
        return $this->path;
    }
    
    /** 
     * This function return name of the file to read. This process extension,
     * and when file has not specified extension, and directory type was
     * specified, then it add extension.
     *
     * @param string $name Name of the file to process.
     *
     * @return string Processed name of the file.
     */
    private function get_name(string $name) : string {
        $builder = new string_builder($name);
        $builder->push_start('/');

        if (empty($this->type)) {
            return $builder->get();
        }

        if (!$builder->contain('.')) {
            $builder->push('.')->push($this->type);
        }

        return $builder->get();
    }
    
    /** 
     * @var string $type
     * Type of the files to wrap.
     */
    private string $type;
    
    /**
     * @var string $path
     * Path of the directory.
     */
    private string $path;

}

?>
