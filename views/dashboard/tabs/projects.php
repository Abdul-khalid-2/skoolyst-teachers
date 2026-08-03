<?php
$section = 'projects';
$heading = 'Projects & Publications';
$description = 'Research papers, curriculum projects, or notable classroom initiatives.';
$fields = [
    ['key'=>'title','label'=>'Title','type'=>'text','tip'=>'e.g. Interactive STEM Curriculum for Grade 9'],
    ['key'=>'url','label'=>'Link (optional)','type'=>'text','tip'=>'Link to the project, publication, or paper'],
    ['key'=>'description','label'=>'Description','type'=>'textarea','tip'=>'What it was, your role, and its impact.','full'=>true],
];
require __DIR__ . '/_repeater.php';
