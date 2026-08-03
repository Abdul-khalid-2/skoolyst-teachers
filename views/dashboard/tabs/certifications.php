<?php
$section = 'certifications';
$heading = 'Certifications';
$description = 'Professional certifications, licenses, and training courses.';
$fields = [
    ['key'=>'title','label'=>'Certification Title','type'=>'text','tip'=>'e.g. Certified Teacher Training Program'],
    ['key'=>'issuer','label'=>'Issued By','type'=>'text','tip'=>'Organization that issued the certificate'],
    ['key'=>'issue_date','label'=>'Issue Date','type'=>'text','tip'=>'e.g. Mar 2023'],
    ['key'=>'credential_url','label'=>'Credential URL','type'=>'text','tip'=>'Optional link to verify the certificate'],
];
require __DIR__ . '/_repeater.php';
