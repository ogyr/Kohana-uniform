<?php

class Uniform_Field_Int extends Uniform_Field {

    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function default_params()
    {
        return array('size'=>8);
    }

}