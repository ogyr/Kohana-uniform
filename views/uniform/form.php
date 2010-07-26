<?php defined('SYSPATH') or die('No direct script access.');

echo $form->open();

echo "<div class='uniform_form'>";
    echo $form->render_fields();
echo '</div>';

echo $form->submit();

echo $form->close();

