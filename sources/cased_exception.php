<?php

namespace cx_appengine;

/** 
 * This class is used to build exceptions that accepts enum case as 
 * parameter which provide what had happend.
 */
class cased_exception extends \Exception {
    
    /** 
     * This function build new exception with case that provide what had 
     * happend and additional content for log.
     *
     * @param enum<string> $case Case of the errors enum.
     * @param (optional) string $info More custom info about error.
     */
    public function __construct(mixed $case, ?string $info = null) {
        $content = $this->string_from_case($case);

        if ($info !== null) {
            $content .= ' ';
            $content .= 'Exception happend when processing: ';
            $content .= $info;
        }

        $this->case = $case;

        parent::__construct($content);
    }

    /** 
     * This function return case that trigger exception.
     *
     * @return enun<string> Exception case that trigger exception.
     */
    public function get_case() : mixed {
        return $this->case;
    }

    /**
     * This function would make string from the enum exception case.
     *
     * @param enum<string> $case Case which trigger this exception.
     *
     * @return string Stringify content of the case.
     */
    protected function string_from_case(mixed $case) : string {
        return $case->value;
    }

    /** 
     * @var enum<string> 
     * This variable would store case with string where string is readable
     * content of the exception.
     */
    private mixed $case;

}

?>
