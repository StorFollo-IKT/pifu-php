<?php

use askommune\pifu_parser\parser;

require 'vendor/autoload.php';
$pifu=new parser($argv[1]);
$schools = $pifu->schools();
$students = [];
foreach ($schools as $school)
{
    $members = $pifu->group_members($school);
    foreach($members as $member)
    {
        foreach($member->{'role'} as $role)
        {
            $attributes = $role->attributes();
            if($attributes['roletype'] == '01')
            {
                $person = $pifu->person($member->{'sourcedid'}->{'id'});
                $ssn = (string)$person->{'userid'}[0];
                $students[$ssn] = $person;
            }
        }
    }
}
file_put_contents($argv[2], json_encode($students));