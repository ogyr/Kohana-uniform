<?php

class Uniform_Field_Boolean extends Uniform_Field {

    public $_form_method  = 'checkbox';

    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function default_params()
    {
        return array();
    }

    public function render_input()
    {
        $params = $this->params();
        $value = 1;
        if(isset($params['value']))
            $value = $params['value'];

        return Form::checkbox($this->name(), $value, $this->value()==$value,
            array_diff_key(array('value'=>1), $params));
    }

}