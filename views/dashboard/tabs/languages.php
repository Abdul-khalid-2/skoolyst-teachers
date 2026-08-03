<?php
$section = 'languages';
$heading = 'Languages';
$description = 'Languages you can teach or communicate in.';
$fields = [
    ['key'=>'name','label'=>'Language','type'=>'text','tip'=>'e.g. English, Urdu, Arabic'],
    ['key'=>'proficiency','label'=>'Proficiency','type'=>'text','tip'=>'e.g. Native, Fluent, Intermediate'],
];
require __DIR__ . '/_repeater.php';
