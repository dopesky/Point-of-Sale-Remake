<?php

class MY_Form_validation extends CI_Form_validation{
	/**
	* An instance of the class. This is used for form validation with hmvc as described by wiredesignz in
	* https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc/src/codeigniter-3.x/readme.md.
	*/

    function run($module = '', $group = '') {
        (is_object($module)) AND $this->CI = &$module;
        return parent::run($group);
    }
}
?>