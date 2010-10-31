<?php defined('SYSPATH') or die('No direct script access.');

echo $form->open();

echo "<div class='uniform_form'>";
    echo $form->render_fields();
echo '</div>';

if( !$form->no_submit() )
{
    echo $form->submit();
}
else
{
    //so we can submit without submit input field and $form->sent() will still work
    echo Uniform_Field::factory('hidden',
        array('name' => $form->submit_name, 'value' => 1 )
    )->render();
}

echo $form->close();

