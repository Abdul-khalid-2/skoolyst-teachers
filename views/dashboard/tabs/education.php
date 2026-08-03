<?php
$section = 'educations';
$heading = 'Education';
$description = 'Add your degrees, certifications from institutes, and academic background.';
$fields = [
    ['key'=>'degree','label'=>'Degree / Program','type'=>'text','tip'=>'e.g. M.Phil Mathematics, BSc Computer Science'],
    ['key'=>'institute','label'=>'Institute / University','type'=>'text','tip'=>'Name of the school, college or university'],
    ['key'=>'start_date','label'=>'Start Date','type'=>'text','tip'=>'e.g. 2018 or Sep 2018'],
    ['key'=>'end_date','label'=>'End Date','type'=>'text','tip'=>'e.g. 2022 or Present'],
    ['key'=>'description','label'=>'Description','type'=>'textarea','tip'=>'Optional details: honors, coursework, GPA.','full'=>true],
];
require __DIR__ . '/_repeater.php';
