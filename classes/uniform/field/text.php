<?php

class Uniform_Field_Text extends Uniform_Field {

    public $_form_method  = 'textarea';

    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function default_params()
    {
        return array('cols'=>55, 'rows'=>8);
    }

}