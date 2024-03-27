<?php

namespace cx_appengine;

require_once(__DIR__.'/string_builder.php');
require_once(__DIR__.'/cased_exception.php');

/** 
 * This enum stores exception cases for all activity like classes.
 */
enum activity_exception_case:string {
    case param_not_exists = 
        'Param which trying to load not exists.';

    case param_not_validated = 
        'Param which tryint to load not pass validation.';

    case already_validated = 
        'Activity had been already validated.';

    case not_initialized = 
        'Activity had not been initializated yet. Activity must be '.
        'initializated before trying to process it.';

    case not_validated = 
        'Activity had not been validated yet. Activity must be validated '.
        'before trying to process it.';
    
    case already_initialized = 
        'Activity had been already initializated.';
}

/** 
 * This class is used to provide exceptions which occur when working with 
 * activity.
 */
class activity_exception extends cased_exception {}

/** 
 * This is activity class. It is abstract, because all of the activities in
 * the system is different classes, which must provide functions belowe to 
 * process it in the system.
 * - show_after_button -    Return which button would trigger activity.
 * - inside_buttons -       Return array of buttons which are used in 
 *                          activity.
 * - init_inputs -          Return array of inputs and their types which 
 *                          would be provided when activity had been 
 *                          triggered.
 * - inside_inputs -        Return array of inputs used in the activity.
 * - render -               Return string builder which contain rendered 
 *                          activity.
 * - process -              It would process data received from the request
 *                          and return activity self to make possible chain
 *                          functions calling.
 */
abstract class activity {
    
    /** 
     * This function would return name of the button which would trigger
     * that activity. When activity would be default, it would return 
     * null.
     * 
     * @return ?string Name of the button which would trigger actovity or
     *                 null when activity would be default in the view.
     */
    public abstract function show_after_button() : ?string;
    
    /**
     * This function would return array of the buttons which are used in the
     * activity. It is important, because on this base view decide which 
     * of the activities would be choosen to process and render.
     *
     * @return array<int, string> Array of the button used in the activity.
     */
    public abstract function inside_buttons() : array;
    
    /**
     * This function would return array of names of the inputs passed to the
     * activity when it is triggered by the show_after_button() button. That
     * array must contain names of the inputs as keys and its types to 
     * validate as values.
     * 
     * @return array<string, string> Array with the init activity inputs.
     */
    public abstract function init_inputs() : array;
    
    /** 
     * This function would to return array of the inputs used inside the 
     * activity. This array must be like init_inputs() array, that mean 
     * contain name - type pairs when name of the input is key in the array
     * and type of the inputs is value passed to that key.
     *
     * @return array<string, string> Array with inputs used inside activity.
     */
    public abstract function inside_inputs() : array;
    
    /**
     * This function would render activity and return rendered view which
     * could be send to the used.
     *
     * @return string_builder Rendered form of the activity.
     */
    public abstract function render() : string_builder;
    
    /**
     * This function would process the activity. It will be used to all
     * actions which activity do, like saving content in the database or
     * updating its etc.
     *
     * @return activity This for the chain processing.
     */
    public abstract function process() : self;

    /**
     * This would build new activity.
     * 
     * @param templates $templates Base for the render function.
     *
     * @return activity New activity.
     */
    public function __construct(templates $templates) {
        $this->templates = $templates;
    }

    /**
     * This function would receive content from the user.
     * 
     * @param array<string, string> Content received from the user.
     * 
     * @return activity This for the chain processing.
     */
    public function receive(array $content) : self {
        if ($this->is_it_initialized()) {
            $case = activity_exception_case::already_initialized;
            throw new activity_exception($case);
        }

        $this->received = $content;
        return $this;
    }

    /** 
     * This function check that activity is default in the view.
     * 
     * @return bool True when it is default or false when not.
     */
    public final function is_default_in_view() : bool {
        return $this->show_after_button() === null;
    }

    /**
     * This function check that activity would be process or not.
     * 
     * @return bool True when that activity would be process.
     */
    public function would_be_process() : bool {
        if ($this->any_inside_button_pressed()) {
            return true;
        }

        if ($this->show_after_button() === null) {
            return false;
        }

        if ($this->is_received($this->show_after_button())) {
            return true;
        }

        return false;
    }

    /**
     * This function check that activity was triggered from the 
     * show_after_button() or from the inside buttons. When inside buttons
     * had been triggered then it is not the first render of the activity, 
     * because used already had that activity rendered, worked with it and
     * return passed data to the server. When show_after_button() input had
     * been triggered, then is is first render.
     *
     * @return bool True when is is first render or false when not.
     */
    protected final function is_first_render() : bool {
        if ($this->any_inside_button_pressed()) {
            return false;
        }   

        if ($this->show_after_button() === null) {
            return true;
        }

        if ($this->is_received($this->show_after_button())) {
            return true;
        }

        return false;
    }

    /**  
     * This function check that any buttons used inside the activity had been
     * pressed.
     *
     * @return bool True when any button inside activity had been pressed or
     *              false when not.
     */
    protected final function any_inside_button_pressed() : bool {
        return $this->which_inside_button_pressed() !== null;
    }

    /**
     * This function check which inside button had been pressed. It get all
     * buttons which had been declarated as buttons inside activity, and 
     * search for its in received data. When found it, then return its name
     * but when any button had not been pressed, then return null.
     *
     * @return ?string Name of pressed button or null when anything had 
     *                 not been pressed.
     */
    protected final function which_inside_button_pressed() : ?string {
        foreach ($this->inside_buttons() as $button) {
            if ($this->is_received($button)) {
                return $button;
            }
        }

        return null;
    }

    /**
     * This function trying to load received data by their name. When param
     * with given name is not exists in the received content, then it return
     * value passed in not_found param, default null.
     *
     * @param string $name Name of the param in received data.
     * @param (optional) ?string $not_found It would be returned when param 
     *                                      had not been found on received 
     *                                      params list. Default null.
     *
     * @return ?string Content of the received data.
     */
    protected final function get_received(
        string $name, 
        ?string $not_found = null
    ) : ?string {
        if (!$this->is_it_initialized()) {
            $case = activity_exception_case::not_initialized;
            throw new activity_exception($case);
        }

        if ($this->is_received($name)) {
            return $this->received[$name];     
        }

        return $not_found;
    }

    /**
     * This function check that parameter with given name had been received
     * or not. 
     *
     * @param string $name Name of the param to check.
     *
     * @return bool True when param had been received or false if not.
     */
    protected final function is_received(string $name) : bool {
        if (!$this->is_it_initialized()) {
            $case = activity_exception_case::not_initialized;
            throw new activity_exception($case);
        }

        return isset($this->received[$name]);
    }

    /**
     * This function return read only templates, set up by constructor.
     * 
     * @return templates Templates loaded when creates activity.
     */
    protected final function get_templates() : templates {
        return $this->templates;
    }

    /** 
     * This function check that activity had been initializated.
     *
     * @return bool True when activity had been initializated or false if not.
     */
    private function is_it_initialized() : bool {
        return is_array($this->received);
    }
   
    /** 
     * @var ?array<string, string>
     * This variable store received content from the user, or null before 
     * activity initialization.
     */
    private ?array $received = null;
    
    /**
     * @var templates
     * This variable store templates loaded when activity creates.
     */
    private templates $templates;

}

?>
