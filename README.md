Uniform - Lightweight Form generation module for [Kohana](http://kohanaframework.org)
=========================================================================

This module is experimental.

Uniform builds on the premises that

* forms should be fun again
* HTML generation follows DRY principles (ie. no code duplication)
* as much work as possible should be left to the framework (eg. Form and Validate classes)

Examples
----------

Once you added the Uniform module, making forms is easy:

1.    Create a Uniform_Fieldset class in 'application/classes/uniform/fieldset/':

    class Uniform_Fieldset_User extends Uniform_Fieldset {

        public function __construct()
        {
            //atm we suppose it's a mysql database
            parent::__construct('user_table', 'id');

            /*
              The fields of user_table are added to our fieldset automatically
              In this simple table we only have 4 fields: id, name, password, email)
              These fields are now available to the fieldset.
              They are available as Uniform_Field according to the MySQL type they have.
            */

            //we can customize some of them if we like

            $this->field('name')
                    ->hname('Username')
                    ->params(array(   //the arguments array that is passed to Kohana's Form::input
                        'size'=> 30
                    ))

                  ->field('email')
                    ->rule('email'); //the rule is set in Kohana's Validate library
        }

    }

2.    Once you have the Fieldset, you can create Forms from it. Create a Uniform_Form class in 'application/classes/uniform/form/':

    class Uniform_Form_Login extends Uniform_Form {

        //array of fieldset classes and the selection of fields to use from them
        public $fieldsets = array(
            'user' => array('name', 'password')
        );

        public function initialize()
        {
            //here you can customize some more
            $this->field('name')
                      ->rule('not_empty');
        }

    }

3.    Done, you can now render and use your form:

In your controller:

    public action_login()
    {
        $form = Uniform::factory('Login');

        if( $_POST $form->bind($_POST)->check() )
        {
            $data = $form->as_array();
            //now you can use your form data here.
        }

        //the form displays entered data, as well as validation errors
        //render the whole form, a selection of fields or single fields
        //rendering is easily customisable and uses Kohana Views
        $this->request->response = '<html><body>' .
            $form .
            '</body></html>';
    }
  
  
Installation
-----------

Like with other Kohana modules you either 

* download the archive and extract it to a module folder, eg. modules/uniform
* or even better (because you can easily follow the latest releases) clone the git repository. 
From your project root (assuming you have git installed) simply do:
    
    git clone git://github.com/rafsoaken/Kohana-uniform.git modules/uniform

* or add it as a submodule to your git project ([Kohana with git submodules](http://kohanaframework.org/guide/tutorials.git)). 
From your project root do:

    git submodule add git://github.com/rafsoaken/Kohana-uniform.git module/uniform;
    git submodule init; git submodule update;
    
You are done after enabling the module in your bootstrap.php file. 
Under the section "Enable modules ..." add a line to the array in the Kohana::modules() call: 

    Kohana::modules(array(
        ...
        'uniform'  => MODPATH.'uniform',  // Uniforms form library
    ));

    






















