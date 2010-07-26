<?php defined('SYSPATH') or die('No direct script access.');

if( $field->errors() )
        echo join('<br />', $field->errors()) . '<br />';

echo $prefix . $field->render_label() . $field->render_input() . $suffix;