<?php

namespace cx_appengine;

require_once(__DIR__.'/cache.php');

/** 
 * This is template parser. It manage views in the application. It rendering
 * HTML templates to show it on the user screen. In templates all attributes
 * which would to be replace by this template manager would be included in
 * the '<<' '>>' tags. Between '<<' and '>>' must be path to the param in the
 * params array. When array is nested, path to param can contain '.'. 
 * For example to get ['a']['b']['c'] use '<<a.b.c>>'.
 *
 * In the params can be used white chars.
 * For example '<<a.b.c>> == '<< a.b.c >>'.
 */
class template {

    /**
     * This function is constructor for the templates manager. It get storage
     * where templates files are located. 
     *
     * @param string $storage Path to directory with templates.
     *
     * @throws Exception code: 2000 (Storage is not directory).
     *
     * @return template New templates parser.
     */
    public function __construct(string $storage) {
        if (!is_dir($storage)) {
            throw new Exception($storage.' is not directory.', 2000);
        }

        $this->storage = $storage;
    }

    /** 
     * This function render template from directory. It get name of the 
     * template. Default extension for the file is .html, and when not 
     * specified any extension, then .html is used. 
     *
     * @param string $view Name of the view file.
     * @param array $params Params to replace marks in the template.
     * 
     * @return string Rendered content.
     */
    public function render(string $view, array $params) : string {
        $handler = new cache($this->get_view_file($view));
        
        return $this->render_content($handler->read(), $params);
    }

    /** 
     * This function return path to the file with the template.
     * 
     * @param string $view Name of the view.
     *
     * @return string Path to the template file on disk.
     */
    private function get_view_file(string $view) : string {
        if (strpos($view, '.') === false) {
            $view .= '.html';
        }

        return $this->storage.'/'.$view;
    }

    /** 
     * This function render content from the template in variable.
     *
     * @param string $content Content to render.
     * @param array $params Params to replace marks.
     * 
     * @return string Rendered content.
     */
    public function render_content(string $content, array $params) : string {
        $start = '<<';
        $stop = '>>';
        $length = strlen($start);

        $rendered = '';
        $last = 0;

        while (true) {
            $current = strpos($content, $start);
            
            if ($current === false) {
                return $rendered.$content;
            }

            $current_end = strpos($content, $stop, $current);
            
            if ($current_end === false) {
                return $rendered.$content;
            }
            
            $param_length = $current_end - $current - $length;
            $param_name = substr($content, $current + $length, $param_length);
            $param = $this->get_param($params, $param_name);

            $rendered .= substr($content, 0, $current);
            $rendered .= $param;
            $content = substr($content, $current_end + $length);
        }
    }
    
    /** 
     * This funciton pulls param out of the params array by its name. 
     *
     * @param array $params Params list to pulls from.
     * @param string $param_name Name of the param to pulls ot out.
     * 
     * @return Content of the param or '' when it not exists in the array.
     */
    private function get_param(array $params, string $param_name) : string {
        $param_name = trim($param_name);
        $path = $this->string_split($param_name, '.');
        $param = $params;

        for ($count = 0; $count < count($path); $count++) {
            $current = $path[$count];

            if (!isset($param[$current])) {
                return '';
            }

            $param = $param[$current];
        }

        if (!is_string($param)) return '';

        return $param;
    }

    /** 
     * This function split string into array by given sign.
     * For example 'A.B', '.' => ['A', 'B'].
     *
     * @param string $content Content to split.
     * @param string $search Mark to split by it.
     *
     * @return array Splited parts of the string.
     */
    public function string_split(string $content, string $search) : array {
        $splited = [];
        $length = strlen($search);

        while (true) {
            $current = strpos($content, $search);

            if ($current === false) {
                if (strlen($content) !== 0) {
                    array_push($splited, $content);
                }

                return $splited;
            }
            
            $item = substr($content, 0, $current);

            if (strlen($item) !== 0) {
                array_push($splited, $item);
            }

            $content = substr($content, $current + $length);
        }
    }

    /**
     * @var string $storage This variable store views directory name.
     */
    private string $storage;

}

?>
