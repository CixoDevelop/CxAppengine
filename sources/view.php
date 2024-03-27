<?php

namespace cx_appengine;

require_once(__DIR__.'/string_builder.php');
require_once(__DIR__.'/activity.php');
require_once(__DIR__.'/landing_activity.php');
require_once(__DIR__.'/validable_activity.php');
require_once(__DIR__.'/cased_exception.php');

/** 
 * This is enum for the exceptions in the view.
 */
enum view_exception_case:string {
    case default_not_set =
        'Add activity which is default, that mean return null from the '.
        'show_after_button(), or which extends landing_activity.';
    
    case default_already_set =
        'Default activity had been already set, and can not be set twice.';
}

/** 
 * This class is the exception that had been thrown in the view.
 */
class view_exception extends cased_exception {}

/**
 * This class is view in the system. It is the container for the activities,
 * which could also choose which of them would be process.
 */
class view {
    
    /** 
     * This function is the view constructor. It build new view, from the
     * received data which is necessary for the activity chooser function.
     *
     * @param array<string, string> $received Content received from the used.
     *
     * @return view New ready to adding activity view.
     */
    public function __construct(array $received) {
        $this->received = $received;
        $this->activities = [];
        $this->default = null;
    }

    /**
     * This function add single activity to the view. When activity is landing
     * activity, then it would set is as default. When any other default had
     * just been set, throws exception.
     *
     * @param activity $activity New activity in the view.
     *
     * @return self This for chain processing.
     */
    public function add_activity(activity $activity) : self {
        if ($activity->is_default_in_view()) {
            if ($this->is_default_activity_set()) {
                $case = view_exception_case::default_already_set;
                throw new view_exception($case);
            }

            $this->default = $activity;
        } else {       
            array_push($this->activities, $activity);
        }

        return $this;
    }

    /** 
     * This function prepare view, that mean initializated all activities.
     *
     * @return self This for chain processing.
     */
    public function prepare() : self {
        if (!$this->is_default_activity_set()) {
            $case = view_exception_case::default_view_not_set;
            throw new view_exception($case);
        }

        $this->default->receive($this->received);

        foreach ($this->activities as $activity) {
            $activity->receive($this->received);
        }   

        return $this;
    }

    /** 
     * This function would choose which activity would be processed now, and
     * return it.
     * 
     * @return activity Activity which would be processed now.
     */
    public function choose() : activity {
        if (!$this->is_default_activity_set()) {
            $case = view_exception_case::default_view_not_set;
            throw new view_exception($case);
        }

        foreach ($this->activities as $activity) {
            if ($activity->would_be_process()) {
                return $activity;
            }
        }   

        return $this->default;
    }

    /**
     * This check that default activity is already set.
     *
     * @return bool True when default activity had been already set.
     */
    private function is_default_activity_set() : bool {
        return $this->default !== null;
    }

    /**
     * @var ?activity 
     * Default activity, null when not set.
     */
    private ?activity $default;
    
    /**
     * @var array<string, string> 
     * Content received from the user.
     */
    private array $received;
   
    /** 
     * @var array<int, activity>
     * This store all activities in the view.
     */
    private array $activities;

}

?>
