<?php defined('SYSPATH') or die('No direct script access.');

echo "<div class='fooForm'>";
foreach( $fields as $f )
{
    if( $f->errors() )
        echo join('<br />', $f->errors()).'<br />');
    echo $f->render();
}
echo '</div>';