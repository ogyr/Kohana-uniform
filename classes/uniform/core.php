<?php defined('SYSPATH') or die('No direct script access.');

class Uniform_Core {

    public static function factory($form_class, $bind=array())
    {
        $class = 'Uniform_Form_'.$form_class;
        return new $class($bind);
    }
    
}
