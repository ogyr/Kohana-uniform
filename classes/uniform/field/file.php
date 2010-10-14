<?php

class Uniform_Field_File extends Uniform_Field {

    public $_form_method  = 'file';

    public function render_input()
    {
        //using this instead!
        $form_method = $this->_form_method;
        return Form::$form_method($this->name(), $this->params());
    }

}