<?php defined('SYSPATH') or die('No direct script access.');

class Uniform_Field_Core {

    protected $_template = 'uniform/field';
    protected $_form_method  = 'input';
    public $_params = array();

    public function __construct( $params )
    {
        $this->_params = $params;
        $this->label(True);
    }

    public static function factory($field_class, $params = array())
    {
        $class = 'Uniform_Field_' . ucfirst($field_class);
        return new $class($params);
    }

    //general getter - setter method
    protected function setget($property, $input=NULL)
    {
        //print ">>>".$property.'---';
        //print $this->_params[$property]."<<<";

        if(is_null($input))
        {
            return isset($this->_params[$property]) ?
                $this->_params[$property] : NULL;
        }

        $this->_params[$property] = $input;
        return $this;
    }

    //field names
    public function name($input=NULL)
    {
        return $this->setget('name', $input);
    }


    public function hname($input=NULL)
    {
        return $this->setget('hname', $input);
    }

    public function mysqltype($input=NULL)
    {
        return $this->setget('mysqltype', $input);
    }


    //various params
    public function params($input=NULL)
    {
        if( !isset($this->_params['params']) )
            $this->_params['params'] = array();

        if( is_null($input) )
            return  array_merge($this->default_params(), $this->_params['params']);

        $this->_params['params'] = array_merge($this->_params['params'], $input);

        return $this;
    }



    public function set_param($param=NULL, $val=NULL)
    {
        if(!is_null($param))
        {
            if(!is_array($param))
                $param = array($param => $val);
            //merge into existing params array
            $this->params(array_merge($this->params(), $param));
        }

        return $this;
    }

    public function unset_param($param=NULL)
    {
        if(!is_null($param))
        {
            if(!is_array($param))
                $param = array($param => '');
            //remove keys from existing params array
            $this->params(array_diff_key($this->params(), $param));
        }

        return $this;
    }

    public function defaults($input=NULL)
    {
        return $this->setget('defaults', $input);
    }

    public function value($input=NULL)
    {
        if(is_null($input) && is_null($this->setget('value', NULL)))
            return $this->defaults();

        return $this->setget('value', $input);
    }

    public function length($input=NULL)
    {
        return $this->size($input, 'length');
    }

    public function size($input=NULL, $field='size')
    {
        if( is_null($input) )
            return @$this->_params['params'][$field];

        return $this->set_param($field, $input);
    }

    public function input($input=NULL)
    {
        return $this->setget('input', $input);
    }

    public function errors($input=NULL)
    {
        //so we always get arrays
        if( is_null($this->setget('errors')) )
               $this->setget('errors', array());

        if( !is_null($input) AND !is_array($input) )
            $input = array($input);

        return $this->setget('errors', $input);
    }

    public function add_errors($input=NULL)
    {
        if( !is_null($input) AND !is_array($input) )
            $input = array($input);

        return $this->setget('errors', array_merge(
              $this->errors(),
              $input
        ));
    }

    public function template($template)
    {
        $this->_template = $template;
    }

    public function prefix($input=NULL)
    {
        return $this->setget('prefix', $input);
    }

    public function suffix($input=NULL)
    {
        return $this->setget('suffix', $input);
    }

    protected function default_params()
    {
        return array();
    }

    //validation
    public function validation($input=NULL)
    {
        if(is_null($input) && is_null($this->setget('validation', NULL)))
        {
            $this->validation(Validate::factory(array())
                ->label($this->name(), $this->hname()));
        }
        return $this->setget('validation', $input);
    }

    public function rule($callback, $parameter=NULL)
    {
        $this->validation()->rule($this->name(), $callback, $parameter);

        return $this;
    }

    public function callback($callback)
    {
        $this->validation()->callback($this->name(), $callback);
        return $this;
    }

    public function filter($filter, $params=NULL)
    {
        $this->validation()->filter($this->name(), $filter, $params);
        return $this;
    }

    public function check( $allow_empty=FALSE )
    {
        //do validation on
        if(is_null($this->validation()))
        {
            return True;
        }

        $validate = $this->validation();
        $validate[$this->name()] = $this->value();
        //echo Kohana::debug($validate);
        $success = $validate->check($allow_empty);

        if( $success )
            return True;

        $this->add_errors($validate->errors($this->messages()));
        return False;
    }

    //rendering
    public function render_input()
    {
        //
        // DOES NOT WORK ONLINE WITH PHP5.2.9 !
        //
        //return call_user_func_array(array("Form", $this->_form_method),
        //    array($this->name(), $this->value(), $this->params()));

        //using this instead!
        $form_method = $this->_form_method;
        return Form::$form_method($this->name(), $this->value(), $this->params());
    }

    public function label( $label=NULL )
    {
        return $this->setget('label', $label);
    }

    public function render_label()
    {
        if( $this->label() )
            return Form::label($this->name(), $this->hname());

        return '';
    }

    public function render($prefix=NULL, $suffix=NULL)
    {
        $params = $this->params();
        if(isset($params['type']) && strtolower($params['type'])=='hidden')
            $this->label(False)->prefix('')->suffix('');

        return View::factory($this->_template)
            ->set(array(
                'field' => $this,
                'prefix'    => is_null($prefix) ? $this->prefix() : $prefix,
                'suffix'    => is_null($suffix) ? $this->suffix() : $suffix,
            ));
    }

    public function clone_to($field_class)
    {
        return self::factory($field_class, $this->_params);
    }

    /*
    * error messages file to use for translating error messages regarding this field
    */
    public function messages( $input=NULL )
    {
        return $this->setget('messages', $input);
    }

}


