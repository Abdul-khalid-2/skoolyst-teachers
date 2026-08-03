<?php
$section = 'awards';
$heading = 'Awards & Recognition';
$description = 'Honors, awards, or recognitions you have received.';
$fields = [
    ['key'=>'title','label'=>'Award Title','type'=>'text','tip'=>'e.g. Best Teacher Award 2022'],
    ['key'=>'issuer','label'=>'Given By','type'=>'text','tip'=>'Organization or institution'],
    ['key'=>'date','label'=>'Date','type'=>'text','tip'=>'e.g. 2022'],
    ['key'=>'description','label'=>'Description','type'=>'textarea','tip'=>'Optional details about the award.','full'=>true],
];
require __DIR__ . '/_repeater.php';
