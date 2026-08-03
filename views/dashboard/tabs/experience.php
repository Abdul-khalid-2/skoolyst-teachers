<?php
$section = 'experiences';
$heading = 'Teaching Experience';
$description = 'List the schools, colleges, or institutions you have worked at.';
$fields = [
    ['key'=>'title','label'=>'Job Title','type'=>'text','tip'=>'e.g. Senior Mathematics Teacher'],
    ['key'=>'institute','label'=>'Institution','type'=>'text','tip'=>'Name of the school/college/university/company'],
    ['key'=>'start_date','label'=>'Start Date','type'=>'text','tip'=>'e.g. Jan 2020'],
    ['key'=>'end_date','label'=>'End Date','type'=>'text','tip'=>'e.g. Present, or Dec 2023'],
    ['key'=>'description','label'=>'Responsibilities','type'=>'textarea','tip'=>'Key responsibilities and achievements in this role.','full'=>true],
];
require __DIR__ . '/_repeater.php';
