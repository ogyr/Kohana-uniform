<?php defined('SYSPATH') or die('No direct script access.');

echo $form->open();

echo $form->render_fields();
echo Form::submit('submit', "Abschicken");

echo Form::close();