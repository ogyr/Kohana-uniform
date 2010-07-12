<?php

class Uniform_Field_Select extends Uniform_Field {

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

        if(!isset($params['options']))
            throw new Exception('The Select field needs an array of possible
                values=>names under the "options" key in the field params!');

        return Form::select($this->name(), $params['options'], $this->value(),
            array_diff_key(array('options'=>1), $this->params()));
    }

}