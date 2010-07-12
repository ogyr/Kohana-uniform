<?php defined('SYSPATH') or die('No direct script access.');

class Uniform_Fieldset_Core {

    public $fields = array();
    private $current;
    private $table; 

    public function __construct($table=NULL, $pk=NULL)
    {
        if( !is_null($table) )
            $this->mysql_init($table, $pk);
    }

    public function mysql_init($table, $pk)
    {
        $this->table = $table;
        $this->pk = $pk;

        //default field types from db
        $this->field_types = array(
            'string'    => 'Char',
            'blob'      => 'Text',
            'int'       => 'Int',
            'bool'      => 'Boolean',
            'float'     => 'Float',
            'time'      => 'Time',
            'date'      => 'Date',
            'real'      => 'Float',
        );

        $result = Database::instance()->list_columns($this->table);

        //echo Kohana::debug($result); die();

        foreach($result as $name => $prop)
        {
            $fname = $name;
            $ftype = $prop['type'];

            $this->addfield($fname)
                ->type($this->field_types[$ftype])
                ->length( isset($prop['character_maximum_length']) ?
                    $prop['character_maximum_length'] : 30
                )
                ->mysqltype($ftype);
                //echo Kohana::debug($this->field());
        }

        //echo Kohana::debug($this); die();
    }

    /*
    * Casts the Field to a new class
    */
    public function type( $field_type=NULL )
    {
        if( is_null($field_type) )
            return get_class($this->fields[$this->current]);

        $this->fields[$this->current] = $this->fields[$this->current]->clone_to($field_type);
        return $this;
    }


    /*
     *
     * Delegates to Field methods and returns again this Fieldset instance
     */
    protected function __call($func, $args)
    {
        //echo Kohana::debug(get_class_methods($this->fields[$this->current]));

        if( method_exists($this->fields[$this->current], $func) )
        {
            call_user_func_array(array($this->fields[$this->current], $func), $args);
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
            return $this->fields[$this->current];
        }

        if(!isset($this->fields[$fname]))
        {
            throw new Exception(
                    "The form field \"$fname\" does not exist!<br />" .
                    "Try these instead:" .
                    Kohana::debug(array_keys($this->fields))
            );
        }

        $this->current = $fname;
        return $this;
    }


    /*
     * Sets a Field the current Field in the Fieldset
     */
    public function addfield( $fname )
    {

        if( isset($this->fields[$fname]) )
        {
            throw new Exception(
                    "The form field \"$fname\" does already exist!<br />" .
                    "The following fields are already defined:" .
                    Kohana::debug(array_keys($this->fields))
            );
        }

        $this->fields[$fname] = Uniform_Field::factory('Generic', array('name' => $fname));
        $this->current = $fname;

        //defaults
        $this->hname(ucfirst($fname))
            ->suffix("<br />\n")
            ->params(array());

        return $this;
    }
}
