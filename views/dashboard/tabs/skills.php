<?php
$section = 'skills';
$heading = 'Skills';
$description = 'Teaching, subject-matter, and technical skills with proficiency level.';
$fields = [
    ['key'=>'name','label'=>'Skill','type'=>'text','tip'=>'e.g. Classroom Management, MS Excel, Curriculum Design'],
    ['key'=>'level','label'=>'Proficiency %','type'=>'number','tip'=>'A number from 0 to 100, e.g. 85'],
];
require __DIR__ . '/_repeater.php';
