<?php defined('SYSPATH') or die('No direct script access.');

foreach( $fields as $f )
{
    echo $f->render() . "\n";
}
