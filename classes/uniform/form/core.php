<?php defined('SYSPATH') or die('No direct script access.');

class Uniform_Form_Core {

    private $_model;
    private $_fields = array();
    private $_output_filter = array();

    protected $model;
    protected $fields;
    protected $_template_fieldset = '_uniform/fieldset';

    public $open = array(NULL, array("name"=>"Abschicken", "method"=>'POST'));


    public function __construct($fieldset, $fields, $bind=array())
    {
        $fieldset_name = 'Uniform_Fieldset_' . ucfirst($fieldset);
        $fieldset = new $fieldset_name();

        foreach($fields as $f)
        {
            $field = $fieldset->fields[$f];
            //catch nonexisting fields
            if( ! $field instanceof Uniform_Field )
            {
                throw new Exception("The field '$f' is not available in the Uniform_Fieldset '$fieldset_name' !");
            }
            $this->_fields[$f] = $field;
        }

        //echo Kohana::debug($this->_fields);  die();

        $this->bind($bind);
    }


    public function field($fname)
    {
        return $this->_fields[$fname];
    }


    public function render_field($fname)
    {
        return $this->field($fname)->render();
    }


    public function render_fieldset()
    {
        return View::factory($this->_template_fieldset)
            ->set(array(
                'fields'    => $this->_fields,
                'form'      => $this
            ));
    }


    public function render()
    {
        return $this->open() .

            $this->render_fieldset() .

            Form::submit('submit', "Abschicken").
            Form::close();
    }

    public function open( $args=NULL )
    {
        if( is_null($args) )
            return call_user_func_array('Form::open', $this->open);

        $this->open = func_get_args();
        return $this;
    }


    public function bind($bind=array())
    {
        foreach($bind as $k => $v)
        {
            if($this->field($k))
                $this->field($k)->value($v);
        }
        return $this;
    }


    public function check()
    {
        $valid = True;
        $out = array();
        foreach($this->_fields as $fname=>$field)
        {

            if( $this->field($fname)->check() )
            {
                $out = array_merge($out, $this->field($fname)->validation()->as_array());
            }
            else
                $valid = False;
        }
        return $valid ? $out : False;
    }


    public function as_array()
    {
        $out = array();
        foreach($this->_fields as $fname=>$field)
        {
            $out[$fname] = $field->value();
        }
        return $out;
    }


    public function __toString()
    {
        return $this->render();
    }

    /*
     * removes a Field
     */
    public function remove_field( $fname )
    {
        unset($this->_fields[$fname]);
        return $this;
    }

}

