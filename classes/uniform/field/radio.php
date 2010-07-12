<?php

class Uniform_Field_Radio extends Uniform_Field {

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
        if(!isset($params['enum']))
            throw new Exception('The Radio field needs an array of possible
                values under the "enum" key in the field params!');

        foreach($params['enum'] as $val => $val_name)
        {
            $out[] = "$val_name:" . Form::radio(
                $this->name(),
                $val,
                $this->value() == $val,
                array_diff_key(array('enum'=>1), $this->params())
            );
        }
        return join('&nbsp;&nbsp;', $out);
    }

}