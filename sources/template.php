<?php

namespace cx_appengine;

require_once(__DIR__.'/cache.php');
require_once(__DIR__.'/string_builder.php');

/**
 * This class is used to render content from the template. It require content
 * which is cache object to read template from it, and templates object to 
 * create sub-templates when any sub-template is used in template. It also get
 * dictionary from the templates object. Other, more render-specific data must
 * be parsed by array to render function. All sentenses which must be replaced
 * by variables must be close in {{ }}  or {{$ }}. Address in array coud be 
 * parsed by '.', like ['A' => ['B' => 'C']] => A.B, to place content of the 
 * other file use {{> file }}, and to translate sentense use {{? sentense }}.
 *
 * Examples:
 * - {{ variable }} => ['variable' => 'new content']
 * - {{ variable.x }} => ['variable' => ['x' => 'new content']]
 * - {{$ variable }} => ['variable' => 'new content']
 * - {{> dir/file.html }} => content of the dir/file.html
 * - {{? sentense }} => translated('sentense')
 */
class template {
    
    /** 
     * This function build new template object.
     * 
     * @param cache $content Template file.
     * @param templates $templates Templates manager which create template.
     *
     * @return template New template.
     */
    public function __construct(cache $content, templates $templates) {
        $this->content = $content;
        $this->templates = $templates;
    }

    /** 
     * This function read content of the file.
     *
     * @return string_builder Template file content.
     */
    public function get_content() : string_builder {
        return $this->content->read();
    }

    /** 
     * This function is render engine. Is get options to place render-specific
     * data, and return string_builder with rendered content.
     *
     * @param array<string, array|string> $options Render-specific options.
     * 
     * @return string_builder Rendered content.
     */
    public function render(array $options) : string_builder {
        $start_char = '{{';
        $stop_char = '}}';
        
        $rendered = new string_builder();
        $content = $this->get_content();

        while (true) {
            $first = $content->divide($start_char, false);

            if ($first->right->empty()) {
                return $rendered->push($content);
            }

            $second = $first->right->divide($stop_char, false);
           
            if ($second->right->empty()) {
                return $rendered->push($content);
            }

            $option_name = $second->left;
            $before = $first->left;
            $after = $second->right;

            $option = $this->get_option($option_name, $options);

            $rendered->push($before)->push($option);
            $content = $after;
        }
    }

    /**
     * This function render option from the template.
     * 
     * @param string_builder $option Option name.
     * @param array<string, string|array> $options Options to render.
     *
     * @return string_builder Rendered option.
     */
    private function get_option(
        string_builder $option, 
        array $options
    ) : string_builder {
        $command = $option->letter(0);
        $option->cut(1, true)->trim();

        switch ($command) {
            case '>':
                return $this->get_file_option($option, $options);

            case '?':
                return $this->get_dictionary_option($option);

            default:
            case '$':
                return $this->get_var_option($option, $options);
        }
    }

    /**
     * This function render option which is nested file.
     *
     * @param string_builder $option Option file name.
     * @param array<string, string|array> $options Options to render file.
     * 
     * @return string_builder Rendered content.
     */
    private function get_file_option(
        string_builder $option,
        array $options
    ) : string_builder {
        $directory = $this->content->get_directory();
        $templates = $this->templates->copy($directory);

        return $templates->prepare($option)->render($options);
    }

    /**
     * This function translate sentense with templates dictionary.
     *
     * @param string_builder $name Sentense to translate.
     *
     * @return string_builder Translated sentense.
     */
    private function get_dictionary_option(
        string_builder $name
    ) : string_builder {
        return $this->templates->get_dictionary()->translate($name);
    }

    /**
     * This function get option which is variable.
     *
     * @param string_builder $option Name of the option.
     * @param array<string, string|array> $options Options to get from.
     *
     * @return string_builder Content from the variable.
     */
    private function get_var_option(
        string_builder $option, 
        array $options
    ) : string_builder {
        $parts = $option->split('.');
        $content = $options;

        foreach ($parts as $part) {
            $part = $part->get();

            if (isset($content[$part])) {
                $content = $content[$part];
                continue;
            }

            $content = '';
            break;
        }

        return new string_builder($content);
    }

    /**
     * @var cache $content
     * Handler to the template file.
     */
    private cache $content;

    /**
     * @var templates $templates
     * Templates to render sub-template from it.
     */
    private templates $templates;

}

?>
