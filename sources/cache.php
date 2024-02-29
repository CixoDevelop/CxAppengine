<?php

namespace cx_appengine;

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
     * @throws Exception code: 1000 (When file not exists).
     *
     * @return New cache instance.
     */
    public function __construct(string $name) {
        if (!is_file($name)) {
            throw new Exception($name.' not exists on disk.', 1000);
        }

        $this->path = realpath($name);
    }

    /** 
     * This function read and return content of the file. When file had been 
     * already read from disk by this or other instance of this class, then it 
     * is not readed from disk, but from cache in RAM.
     *
     * @throws Excepton code: 1001 (When can not access file).
     *
     * @return string Content of the file.
     */
    public function read() : string {
        if ($this->in_cache()) return $this->from_cache();
    
        set_error_handler(function () {});
        $content = file_get_contents($this->path);
        restore_error_handler();

        if ($content === false) {
            throw new Exception('Can\'t read '.$this->path.'.', 1001);
        }

        $this->to_cache($content);
        return $content;
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
