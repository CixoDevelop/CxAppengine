<?php

namespace cx_appengine;

require_once(__DIR__.'/string_builder.php');
require_once(__DIR__.'/directory.php');
require_once(__DIR__.'/cased_exception.php');

/**
 * This is cases for the cache exception.
 */
enum cache_exception_case:string {
    case not_exists = 
        'File not exists on the disk.';
    
    case not_readable = 
        'File which trying to read is not readable.';
}

/** 
 * This class is exception for the cache.
 */
class cache_exception extends cased_exception {}

/** 
 * This class is simple cache for files using in application. It save file
 * in local RAM, to optimize accesing to disk when trying to load same file
 * multiple times.
 */
class cache {
    
    /**
     * This function build new cache. Cache object handle only one file, but 
     * cache storage is static and shared between object, thanks to it storage
     * is not lose when remove instance.
     *
     * @param string $name Name (path) to the file.
     *
     * @throws cache_exception When file not exists.
     *
     * @return New cache instance.
     */
    public function __construct(string $name) {
        if (!is_file($name)) {
            throw new cache_exception(
                cache_exception_case::not_exists, 
                $name
            );
        }

        $this->path = realpath($name);
    }

    /** 
     * This function read and return content of the file. When file had been 
     * already read from disk by this or other instance of this class, then it 
     * is not readed from disk, but from cache in RAM.
     *
     * @throws cache_exception When can not access file.
     *
     * @return string_builder Content of the file.
     */
    public function read() : string_builder {
        if ($this->in_cache()) {
            return new string_builder($this->from_cache());
        }

        set_error_handler(function () {});
        $content = file_get_contents($this->path);
        restore_error_handler();

        if ($content === false) {
            throw new cache_exception(
                cache_exception_case::not_readable, 
                $this->path
            );
        }

        $this->to_cache($content);
        return new string_builder($content);
    }
    
    /**
     * This function return new directory wrape to directory where file is.
     *
     * @return directory Directory where file is.
     */
    public function get_directory() : directory {
        return new directory(dirname($this->path));
    }
    
    /**
     * This function save content of the file to cache.
     * 
     * @param string $conent New content of the file.
     */
    private function to_cache(string $content) : void {
        self::$cached[$this->get_cache_name()] = $content;
    }

    /** 
     * This function check that file exists in cache.
     *
     * @return bool True when exists in cache, false if not.
     */
    private function in_cache() : bool {
        return isset(self::$cached[$this->get_cache_name()]);
    }   

    /** 
     * This function return content of the file from cache.
     *
     * @return string Content of the file from cache.
     */
    private function from_cache() : string {
        return self::$cached[$this->get_cache_name()];
    }   

    /** 
     * This function change path format to format that can be use as php array
     * key.
     * 
     * @return string Name of the file in cache.
     */
    private function get_cache_name() : string {
        return str_replace('/', '_', $this->path);
    }

    /** 
     * @var string $path
     * This variable store path to the file.
     */
    private string $path;
    
    /** 
     * @var array $cached
     * This variable store content of the items cached in memory.
     */
    private static array $cached = [];

}

?>
