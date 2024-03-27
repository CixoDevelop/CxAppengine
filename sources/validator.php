<?php

namespace cx_appengine;

/**
 * This class is simple data validator. This could be used to validate content
 * from the request in the activity. 
 *
 * This functon has types like:
 * - custom => Always pass validation.
 * - bool => Pass validation on '1', '0', 'true', 'false'.
 * - numeric => Pass validation when content is only numbers.
 * - int => Pass validation when content is only integer number.
 * - string => Pass validation when content is string.
 * - noempty => Pass validation when content is noempty string.
 * - email => Pass validation when content is email.
 * - phone => Pass validation when content is phone number.
 * - url => Pass validation when content is url
 * - domain => Pass validation when content is domain name.
 * - up => Pass validation when content is ip adderss.
 *
 * This function could also parse all of the types:
 * - custom => Return without changes.
 * - bool => Return bool value.
 * - numeric => Return float.
 * - int => Return integer.
 * - string => Return without changes.
 * - noempty => Return without changes.
 * - email => Return without changes.
 * - phone => Return without changes.
 * - url => Return without changes.
 * - domain => Return without changes.
 * - ip => Return without changes.
 */
class validator {
    
    /** 
     * This function build new validator. Validator must get their type when
     * it is create, and that type could not been change after that.
     *
     * @param string $type Type of the validator.
     *
     * @return validator New validator for given type. 
     */
    public function __construct(string $type) {
        $type = trim($type);
        
        if (!$this->is_type_required($type)) {
            $this->type = $this->remove_optional_marker($type);
            $this->required = false;
        } else {
            $this->type = $type;
            $this->required = true;
        }
    }

    /**
     * This function return validator type given when it gad been created.
     *
     * @return string Validator type.
     */
    public function get_type() : string {
        return ($this->required ? '' : '?').$this->type;
    }

    /** 
     * This function check that validating item is required or not, it is
     * specified by first char of the type. Items which is not required has
     * '?' as first char.
     *
     * @return bool True when item is required or false if not.
     */
    public function is_required() : bool {
        return $this->required;
    }

    /**
     * This function validating content passed as the param. When passed
     * argument is null, then validator mean that param is not exists, and
     * check that is required or not. When it is required, then it can not
     * pass validation with null argument, but when it is not required param
     * then it pass validation with null. Otherwise, when passed content is
     * string, then it validate it as given type.
     *
     * @param ?string $content Content to validate by validator. When it is
     *                         null, that mean param that would be validated
     *                         not exists.
     * 
     * @return bool True when content pass validation or false if not.
     */
    public function validate(?string $content) : bool {
        if (!$this->is_param_passed($content)) {
            return !$this->is_required();
        }

        return match ($this->type) {
            'custom' => $this->is_custom($content),
            'bool' => $this->is_bool($content),
            'numeric' => $this->is_numeric($content),
            'int' => $this->is_int($content),
            'string' => $this->is_string($content),
            'noempty' => $this->is_noempty($content),
            'email' => $this->is_email($content),
            'phone' => $this->is_phone($content),
            'url' => $this->is_url($content),
            'domain' => $this->is_domain($content),
            'ip' => $this->is_ip($content),
        };
    }

    /** 
     * This function parse given content to form provided by given type. For 
     * example, when type is numeric, then it parse given string as float.
     * Be warn, because when param which had not passed validation would be 
     * given there, it could trigger unexpecte error.
     * 
     * @param ?string $content Content to parse by validator.
     *
     * @return mixed Parsed content.
     */
    public function parse(?string $content) : mixed {
        if (!$this->is_param_passed($content)) {
            return $content;
        }

        return match ($this->type) {
            'custom' => $this->parse_custom($content),
            'bool' => $this->parse_bool($content),
            'numeric' => $this->parse_numeric($content),
            'int' => $this->parse_int($content),
            'string' => $this->parse_string($content),
            'noempty' => $this->parse_noempty($content),
            'email' => $this->parse_email($content),
            'phone' => $this->parse_phone($content),
            'url' => $this->parse_url($content),
            'domain' => $this->parse_domain($content),
            'ip' => $this->parse_ip($content),
        };
    }

    /** 
     * This function check that given type string is requirer or not. When 
     * type name contain '?' character, then it is not required.
     * 
     * @param string $type Name of the type 
     *
     * @return bool True when type is required or false when not.
     */
    private function is_type_required(string $type) : bool {
        return $type[0] !== '?';
    }

    /**
     * This function return type name without optional marker, that mean 
     * without '?' character when it is passed.
     *
     * @param string $type Type with optional marker.
     *
     * @return string Type name without optional marker.
     */
    private function remove_optional_marker(string $type) : string {
        return substr($type, 1);
    }

    /**
     * This function check that param exists, or not. That mean param is not
     * null.
     *
     * @param ?string $content Content to check that exists.
     * 
     * @return bool True when content is not null or false if it is null.
     */
    private function is_param_passed(?string $content) : bool {
        return $content !== null;
    }

    /** 
     * @var bool 
     * This variable store that type is required.
     */
    private bool $required;

    /**
     * @var string 
     * This variable store type name for the validator.
     */
    private string $type;


    /*** BELOW THAT LINE ALL FUNCTIONS IS VALIDATORS FOR SPECIFIED TYPE ***/


    private function is_custom(string $content) : bool {
        return true;
    }

    private function is_bool(string $content) : bool {
        return match (strtolower($content)) {
            'true' => true,
            'false' => true,
            '1' => true,
            '0' => true,
            default => false,
        };
    }

    private function is_numeric(string $content) : bool {
        return is_numeric($content);
    }

    private function is_int(string $content) : bool {
        if (!is_numeric($content)) {
            return false;
        }

        return floatval(intval($content)) === floatval($content);
    }

    private function is_string(string $content) : bool {
        return true;
    }

    private function is_noempty(string $content) : bool {
        return !empty($content);
    }

    private function is_email($content) : bool {
        return filter_var($content, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function is_phone($content) : bool {
        $content = trim($content);

        if ($content[0] === '+') {
            $content = substr($content, 1);
            $length = strlen($content);

            return $length >= 10 and $length <= 12;
        }       

        return strlen($content) === 9;
    }

    private function is_url(string $content) : bool {
        return filter_var($content, FILTER_VALIDATE_URL) !== false;
    }
    
    private function is_domain(string $content) : bool {
        return filter_var($content, FILTER_VALIDATE_DOMAIN) !== false;
    }
    
    private function is_ip(string $content) : bool {
        return filter_var($content, FILTER_VALIDATE_IP) !== false;
    }
    

    /*** BELOW THAT LINE ALL FUNCTIONS IS PARSES FOR SPECIFIED TYPES */


    private function parse_custom(string $content) : string {
        return $content;
    }

    private function parse_bool(string $content) : bool {
        return match (strtolower($content)) {
            'true' => true,
            'false' => false,
            '0' => false,
            '1' => true
        };
    }

    private function parse_numeric(string $content) : float {
        return floatval($content);
    }

    private function parse_int(string $content) : int {
        return intval($content);
    }
    
    private function parse_string(string $content) : string {
        return $content;
    }

    private function parse_noempty(string $content) : string {
        return $content;
    }

    private function parse_email(string $content) : string {
        return trim($content);
    }

    private function parse_phone(string $content) : string {
        return trim($content);
    }

    private function parse_url(string $content) : string {
        return trim($content);
    }

    private function parse_domain(string $content) : string {
        return trim($content);
    }

    private function parse_ip(string $content) : string {
        return trim($content);
    }

}

?>
