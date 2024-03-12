<?php

namespace cx_appengine;

/** 
 * This class is string builder for the apps on cx_appengine. Is simplify 
 * operations on string, thanks to it app could not use defaults php functions
 * which is not so clean in most operations on strings.
 */
class string_builder {
    
    /** 
     * This function create new string builder. It require init string or 
     * array. When init sentences is array, then all items of them is glued
     * with seperator between.
     *
     * @param array|string|int $init Init sentences of the builder.
     * @param string $separator Separator to insert it between array items
     *                          when $init is an array.
     *
     * @return string_builder New string builder.
     */
    public function __construct(
        array|string|int $init = '', 
        string $separator = ', '
    ) {
        if (is_string($init)) {
            $this->content = $init;
        } elseif (is_int($init)) {
            $this->content = strval($init);
        } else {
            $this->content = $this->from_array($init, $separator);
        }
    }

    /** 
     * This function clone builder to new builder object.
     *
     * @return string_builder Cloned builder object.
     */
    public function clone() : string_builder {
        return new self($this->get());
    }

    /** 
     * This function push new content to the end of the string in the builder.
     * New content could be any item which can be converted into string. That 
     * mean int, string or string_builder.
     * 
     * @param string|int|string_builder $new New content to push to string.
     *
     * @return string_builder Instanse of this element to make chain.
     */
    public function push(string|int|string_builder $new) : self {
        $this->content = $this->content.strval($new);
        return $this;
    }

    /** 
     * This function push new content to the start of the string in the 
     * builder. New content could be any item chich can be converted into
     * string. That mean int, string or string_builder.
     *
     * @param string|int|string_builder $new New content to push to string.
     *
     * @return string_builder Instanse of this element to make chain.
     */
    public function push_start(string|int|string_builder $new) {
        $this->content = strval($new).$this->content;
        return $this;
    }
    
    /** 
     * This function check that builder content contain string-like value.
     *
     * @param string|int|string_builder $what Content to check that exists.
     *
     * @return bool True when found or false when not.
     */
    public function contain(string|int|string_builder $what) : bool {
        return strpos($this->content, strval($what)) !== false;
    }

    /** 
     * This function build string from the array. It require array with parts
     * of the string, and separator to insert it between parts.
     * 
     * @param array $content Content to convert into string.
     * @param string $separator Separator to insert it between parts.
     *
     * @return string Converted array.
     */
    private function from_array(array $content, string $separator) : string {
        $result = '';

        if (empty($content)) {
            return $result;
        }

        foreach ($content as $item) {
            $result = $result.strval($item);
            $result = $result.$separator;
        }

        return substr($result, 0, strlen($result) - strlen($separator));
    }

    /**
     * This function cut given number of letters from string. When start
     * is true, then it remove its from start, else cut from end.
     *
     * @param int $lenght How much letters remove.
     * @param bool $start When true, the remove from start, else from end.
     *
     * @return string_builder Instance to make chain.
     */
    public function cut(int $length, bool $start = false) : self {
        if ($start) {
            $this->content = substr($this->content, $length);
        } else {
            $length = strlen($this->content) - $length;
            $this->content = substr($this->content, 0, $length); 
        }

        return $this;
    }
        
    /**
     * This function trim builder content.
     *
     * @return string_builder Instance to make chain.
     */
    public function trim() : self {
        $this->content = trim($this->content);
        return $this;
    }

    /**
     * This function return letter from given index.
     *
     * @param int $where Index of the letter.
     *
     * @return string Letter on index.
     */
    public function letter(int $where) : string {
        return $this->content[$where];
    }

    /**
     * This function check that string builder content is empty.
     * 
     * @return bool True when empty, false when not.
     */
    public function empty() : bool {
        return empty($this->content);
    }

    /** 
     * This function divide content into two parts by given separator. It 
     * divide by first occut of the separator, and when trim value is true
     * then both parts would be trimed.
     *
     * @param string $separator Separator to divide by it.
     * @param bool $trim When true, both parts would be trimed.
     *
     * @return object Divided parts of the string
     * @return string_builder return->left Part from content start to 
     *                                     separator.
     * @return string_builder return->right Part from separator to 
     *                                      content end.
     */
    public function divide(
        string $separator = ',', 
        bool $trim = true
    ) : object {
        $content = $this->content;
        $length = strlen($separator);
        $count = strpos($content, $separator);

        $result = new \stdClass();

        if ($count === false) {
            $result->left = new self($content);
            $result->right = new self();

            return $result;
        }
    
        $result->left = new self(substr($content, 0, $count));
        $result->right = new self(substr($content, $count + $length));

        return $result;
    }

    /**
     * This function split string into parts by given separator, and return
     * array of the string builders with base content parts. When trim flas is
     * set to true, then parts would be also trimed.
     *
     * @param string $separator Separator to split content by.
     * @param bool $trim When it is true, string parts would be trimed.
     *
     * @return array<int, string_builder> Splited parts of the content.
     */
    public function split(
        string $separator = ',', 
        bool $trim = true
    ) : array {
        $length = strlen($separator);
        $content = $this->content;

        $result = [];

        $append = function ($what) use (&$result, $trim) {
            if ($trim) {
                $new_builder = new self(trim($what));
            } else {
                $new_builder = new self($what);
            }
            
            array_push($result, $new_builder);
        };

        while (true) {
            $count = strpos($content, $separator);
            
            if ($count === false) {
                $append($content);
                break;
            }
            
            $first = substr($content, 0, $count);
            $content = substr($content, $count + $length);

            $append($first);
        }
        
        return $result;
    }

    /** 
     * This function return builded string.
     *
     * @return string Builder content.
     */
    public function get() : string {
        return $this->content;
    }

    /** 
     * This convert string builder into string.
     * 
     * @return string Builder content.
     */
    public function __toString() : string {
        return $this->content;
    }

    /**
     * @var string $content
     * Content of the builder.
     */
    private string $content;

}   

?>
