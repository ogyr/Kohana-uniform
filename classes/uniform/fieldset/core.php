<?php defined('SYSPATH') or die('No direct script access.');

class Uniform_Fieldset_Core {

    protected $_fields = array();
    protected $_current;
    protected $_table;
    protected $_pk;
    protected $_template_fieldset = 'uniform/fieldset';

    public function __construct($table=NULL, $pk=NULL)
    {
        if( !is_null($table) )
            $this->mysql_init($table, $pk);
    }

    public function mysql_init($table, $pk)
    {
        $this->_table = $table;
        $this->_pk = $pk;

        //default field types from db
        $field_types = array(
            'string'    => 'Char',
            'blob'      => 'Text',
            'int'       => 'Int',
            'bool'      => 'Boolean',
            'float'     => 'Float',
            'time'      => 'Time',
            'date'      => 'Date',
            'real'      => 'Float',
        );

        $result = Database::instance()->list_columns($this->_table);

        //echo Kohana::debug($result); die();

        foreach($result as $name => $prop)
        {
            $fname = $name;
            $ftype = $prop['type'];

            if( $fname != $pk ) //exclude primary key from forms
            {
                $this->add_field($fname)
                    ->type($field_types[$ftype])
                    ->length( isset($prop['character_maximum_length']) ?
                        $prop['character_maximum_length'] : 30
                    )
                    ->mysqltype($ftype);
                    //echo Kohana::debug($this->field());
            }
        }

        //echo Kohana::debug($this); die();
    }

    /*
    * Casts the Field to a new class
    */
    public function type( $field_type=NULL )
    {
        if( is_null($field_type) )
            return get_class($this->_fields[$this->_current]);

        $this->_fields[$this->_current] = $this->_fields[$this->_current]->clone_to($field_type);
        return $this;
    }


    /*
     *
     * Delegates to Field methods and returns again this Fieldset instance
     */
    public function __call($func, $args)
    {
        //echo Kohana::debug(get_class_methods($this->_fields[$this->_current]));

        if( method_exists($this->_fields[$this->_current], $func) )
        {
            call_user_func_array(array($this->_fields[$this->_current], $func), $args);
        }
        else
        {
            throw new Exception("Method '$func' does not exist on Uniform_Fieldset nor on Uniform_Field");
        }

        return $this;
    }

    /*
     * Sets a Field the current Field in the Fieldset
     */
    public function field( $fname=NULL )
    {
        if( is_null($fname) )
        {
            return $this->_fields[$this->_current];
        }

        if(!isset($this->_fields[$fname]))
        {
            throw new Exception(
                    "The form field \"$fname\" does not exist!<br />" .
                    "Try these instead:" .
                    Kohana::debug(array_keys($this->_fields))
            );
        }

        $this->_current = $fname;
        return $this;
    }


    /*
     * Returns the Field instance
     */
    public function get_field( $fname )
    {
        return $this->field($fname)->field();
    }


    /*
     * Adds a field to the fieldset
     */
    public function add_field( $fname )
    {

        if( isset($this->_fields[$fname]) )
        {
            throw new Exception(
                    "The form field \"$fname\" does already exist!<br />" .
                    "The following fields are already defined:" .
                    Kohana::debug(array_keys($this->_fields))
            );
        }

        $this->_fields[$fname] = Uniform_Field::factory('Generic', array('name' => $fname));
        $this->_current = $fname;

        //defaults
        $this->hname(ucfirst($fname))
            ->suffix("<br />\n")
            ->field_params(array());

        return $this;
    }

    /*
     * lets you set field params in initialize() from Uniform_form
     */
    public function field_params( $params )
    {
        $this->_fields[$this->_current]->params($params);
        return $this;
    }


    /*
     * removes a Field
     */
    public function remove_field( $fname )
    {
        unset($this->_fields[$fname]);
        return $this;
    }


    /*
    * sets error messages file to use on all fields available so far
    */
    public function messages_all( $file=NULL )
    {
        foreach( $this->_fields as $k => $f )
        {
            $this->_fields[$k]->messages( $file );
        }
        return $this;
    }

}
