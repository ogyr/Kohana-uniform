<?php defined('SYSPATH') or die('No direct script access.');

/*
 * Uniform Form Class
 */
class Uniform_Form_Core extends Uniform_Fieldset {

    protected $_open = array();
    protected $_template_form = '_uniform/form';
    private $_fieldsets = array();

    protected $fieldsets = array();

    public function __construct( $bind=NULL )
    {
        //add fields
        foreach( $this->fieldsets as $fieldset => $fields )
        {
            $this->add_fieldset($fieldset, $fields);
        }

        //default Form::open params
        $this->open(
                NULL,
                array(
                    "method" => 'POST',
                    'id' => strtolower(array_pop(explode('_', get_class($this)))).'_form'
                )
            );

        //initialization hook
        $this->initialize();

        //possibly already bind values
        $this->bind($bind);

        return $this;
    }


    /*
     * a hook to do initialization work on the form
     */
    public function initialize()
    {
        return $this;
    }


    /*
     * Adds $fields of $fieldset to this form
     * If no $fields are specified, all fields are added
     */
    public function add_fieldset($fieldset_name, $fields=NULL)
    {
        $fieldset_class = 'Uniform_Fieldset_' . ucfirst($fieldset_name);
        $fieldset = new $fieldset_class();

        //add $fieldset to list of fieldsets
        if( !isset($this->_fieldsets[$fieldset_name]) )
            $this->_fieldsets[$fieldset_name] = array();

        //specifying fieldnames in a string separated by blanks is possible
        if( is_string($fields) )
            $fields = preg_split("/[\s,]+/", $fields, -1, PREG_SPLIT_NO_EMPTY);

        //if no fields are specified, add all fieldset fields
        if( is_null($fields) )
            $fields = array_keys($fieldset->_fields);

        foreach($fields as $f)
        {
            $field = $fieldset->_fields[$f];

            //catch nonexisting fields
            if( ! $field instanceof Uniform_Field )
            {
                throw new Exception("The field '$f' is not available in the Uniform_Fieldset '$fieldset_name' !");
            }

            $this->_fields[$f] = $field;
            $this->_fieldsets[strtolower($fieldset_name)][$f] = $this->_fields[$f];
        }

        return $this;
    }


    /*
     * removes all the fields that were added from the fieldset $fieldset
     */
    public function remove_fieldset($fieldset)
    {
        foreach( $this->_fieldsets[$fieldset] as $fieldname )
        {
            unset($this->_fields[$fieldname]);
        }
        unset($this->_fieldsets[$fieldset]);
        return $this;
    }

    public function render_field($fname)
    {
        return $this->_fields[$fname]->render();
    }


    public function render_fields( $fields=NULL )
    {
        return View::factory($this->_template_fieldset)
            ->set(array(
                'fields'    => is_null($fields) ? $this->_fields :
                    array_intersect_key($this->_fields, array_flip($fields)),
                'form'      => $this
            ));
    }

    public function field($fname)
    {
        return $this->_fields[$fname];
    }

    public function render()
    {
        return View::factory($this->_template_form)
            ->set('form', $this)->render();
    }

    public function open( $args=NULL )
    {
        if( func_num_args() === 0 )
            return call_user_func_array('Form::open', $this->_open);

        $this->_open = func_get_args();
        return $this;
    }


    public function bind($bind=NULL, $use_filter=False)
    {
        if( is_null($bind) )
            return $this;

        if( $use_filter )
            $bind = $this->in_filter($bind);

        foreach($bind as $k => $v)
        {
            if( isset($this->_fields[$k]) )
            {
                //check if field is an object (a la Jelly Model for relations)
                if( is_object($v) )
                    $this->field($k)->value($v->id()); //just suppose for now it has a primary key method;
                else
                    $this->field($k)->value($v);
            }
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

        return $this->out_filter($out);
    }


    public function __toString()
    {
        return $this->render();
    }


    /*
    * prepares data when it comes in - not field based
    */
    public function in_filter($data)
    {
        return $data;
    }

    /*
    * prepares data when it goes out
    */
    public function out_filter($data)
    {
        return $data;
    }



}

