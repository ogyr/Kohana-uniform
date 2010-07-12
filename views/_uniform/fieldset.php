<?php defined('SYSPATH') or die('No direct script access.');

echo "<div class='fooForm'>";
foreach( $fields as $f )
{
    if($f->errors())
        echo View::factory('alert')->set('msg', "Achtung! " . print_r($f->errors(), True));
    echo $f->render();
}
echo '</div>';