<?php

require_once(__DIR__.'/../require.php');

class a extends \cx_appengine\landing_activity {
    public function render() : \cx_appengine\string_builder {
        $options = [];
        $options['name'] = 'Enter you name...';

        if ($this->is_received('welcome')) {
            $options['name'] = $this->get_validated('name');
        }

        return $this->get_templates()->prepare('view_a')->render($options);
    }

    public function inside_inputs() : array {
        return [
            'name' => 'string'
        ];
    }       

    public function inside_buttons() : array {
        return ['welcome'];
    }

    public function process() : self {
        return $this;
    }
}

class b extends \cx_appengine\validable_activity {
    public function render() : \cx_appengine\string_builder {
        $options = [];

        if ($this->is_received('calc_age')) {
            if (!$this->is_validated('which_age')) {
                $options['age'] = 'Enter correct age!';
            } else {
                $options['age'] = 'Happy '.$this->get_validated('which_age').'th!';
            }
        }

        if ($this->is_first_render()) {
            $options['greeter'] = 'Hello '.$this->get_received('name');
        }

        return $this->get_templates()->prepare('view_b')->render($options);
    }

    public function inside_inputs() : array {
        return [
            'which_age' => 'int'
        ];
    }       

    public function inside_buttons() : array {
        return ['calc_age'];
    }

    public function process() : self {
        return $this;
    }

    public function show_after_button() : string {
        return 'age';
    }

    public function init_inputs() : array {
        return [
            'name' => 'string'
        ];
    }
}

$dir = new \cx_appengine\directory(__DIR__.'/test_templates', 'html');
$templates = new \cx_appengine\templates($dir);

$view = new \cx_appengine\view($_POST);

$view->add_activity(new a($templates))->add_activity(new b($templates));

echo($view->prepare()->choose()->validate()->process()->render());

?>
