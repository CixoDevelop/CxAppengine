<?php

namespace cx_appengine;

require_once(__DIR__.'/validator.php');
require_once(__DIR__.'/string_builder.php');
require_once(__DIR__.'/activity.php');

/** 
 * This class is activity with build in validation methods. It could be used
 * to simplify request validation.
 */
abstract class validable_activity extends activity {

    /**
     * This function all content in the activity. It get inputs which would
     * be found in the received content, that mean inputs from init_inputs()
     * or from the inside_inputs(), and trying to valiate all of its. When 
     * any input had not been received, then null would be passed to the 
     * validator. When item pass the validation, then it would be parsed and
     * places in the array with validated content.
     *
     * @return self This to chain processing.
     */
    public function validate() : self {
        if ($this->is_activity_validated()) {
            $case = activity_exception_case::already_validated;
            throw new activity_exception($case);
        }

        $this->validated = [];
        $this->validation_list = $this->inside_inputs();

        if ($this->is_first_render()) {
            $this->validation_list = $this->init_inputs();
        }

        foreach ($this->validation_list as $name => $type) {
            $this->validate_item($name, $type);
        }

        return $this;
    }

    /** 
     * This function validating single item. It get name of the item, and 
     * their type. When item pass validation, then it would be places in the
     * validated array.
     *
     * @param string $name Name of the item to validate.
     * @param sring $type Stringify type of the validator.
     * 
     * @return self This for chain processing.
     */
    private function validate_item(string $name, string $type) : self {
        $validator = new validator($type);
        $content = $this->get_received($name);

        if ($validator->validate($content)) {
            $this->validated[$name] = $validator->parse($content);
        }

        return $this;
    }
   
    /** 
     * This function load validated content of the param. Validated content is
     * also parsed, that mean for example int type received content would be
     * returned as integer.
     *
     * @param string $name Name of the validated parameter.
     *
     * @return mixed Content of the validated parameter.
     */
    protected final function get_validated(string $name) : mixed {
        if (!$this->is_activity_validated()) {
            $case = activity_exception_case::not_validated_yet;
            throw new activity_exception($case);
        }

        if (!$this->is_validated($name)) {
            $case = activity_exception_case::param_not_validated;

            if ($this->exists_on_validation_list($name)) {
                $case = activity_exception_case::param_not_exists;
            }
            
            throw new activity_exception($case);
        }

        return $this->validated[$name];
    }

    /**
     * This function check that item pass the validation. 
     * 
     * @param string $name Name of the item to check.
     *
     * @return bool True whe item pass validation or false when not.
     */
    protected final function is_validated(string $name) : bool {
        if (!$this->is_activity_validated()) {
            $case = activity_exception_case::not_validated_yet;
            throw new activity_exception($case);
        }
        
        return array_key_exists($name, $this->validated);
    }

    /**
     * This function check that item doesn not pass the validation. 
     * 
     * @param string $name Name of the item to check.
     * @param bool $received (optional) When it is false, then param is not
     *                                  validated when it is not received and
     *                                  not on the validated list. When it is
     *                                  true, then item is not validated only
     *                                  when it is not on validated list, that
     *                                  mean could be not received, but not
     *                                  required. Then it is not received and
     *                                  validated. SET IT TO TRUE COULD MAKE
     *                                  MISTAKE, IF YOU DO NOT KNOW WHAT YO
     *                                  DO.
     *
     * @return bool True whe item does not pass validation or false when pass.
     */
    protected final function is_not_validated(
        string $name, 
        bool $received = false
    ) : bool {
        if (!$received) {
            return !$this->is_received($name) or !$this->is_validated($name);
        }

        return $this->is_received($name) and !$this->is_validated($name);
    }
    
    /**
     * This function check that item exists on the list of the items to
     * validation.
     *
     * @param string $name Name of the item to check.
     * 
     * @return bool True when item would be validated or false when not.
     */
    protected final function exists_on_validation_list(string $name) : bool {
        if (!$this->is_activity_validated()) {
            $case = activity_exception_case::not_validated_yet;
            throw new activity_exception($case);
        }

        return isset($this->validation_list[$name]);
    }

    /** 
     * This function check that activity had been already validated.
     *
     * @return bool True when activity had been already activated.
     */
    private function is_activity_validated() : bool {
        return $this->validated !== null and $this->validation_list !== null;
    }

    /**
     * @var ?array<string, mixed>
     * This array store validated and parsed items. Before activity validation
     * it store null.
     */
    private ?array $validated = null;
    
    /**
     * @var ?array<string, string>
     * This array store list of the items which would be validated. Before
     * activity validation it store null.
     */
    private ?array $validation_list = null;

}

?>
