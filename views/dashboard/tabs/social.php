<?php
$section = 'social_links';
$heading = 'Social & Professional Links';
$description = 'LinkedIn, GitHub, personal website, or other professional profiles.';
$fields = [
    ['key'=>'platform','label'=>'Platform','type'=>'text','tip'=>'e.g. LinkedIn, Facebook, ResearchGate, YouTube'],
    ['key'=>'url','label'=>'Profile URL','type'=>'text','tip'=>'Full link, e.g. https://linkedin.com/in/yourname'],
];
require __DIR__ . '/_repeater.php';
