<?Php
$starttime=microtime(true);
require 'vendor/autoload.php';
$pifu=new \askommune\pifu_parser\parser_cache();

try {
    $pifu->load_xml();
}
catch (Exception $e)
{
    die($e->getMessage());
}

foreach($pifu->schools() as $school)
{
	$groups=$pifu->ordered_groups((string)$school->sourcedid->id);
	if(empty($groups))
		continue;

	foreach($groups as $group)
	{
		echo (string)$group->comments."\n";
		$members=$pifu->group_members($group->sourcedid->id);
		$pifu->group_members($group->sourcedid->id,0);
		if(empty($members))
			continue;
		foreach($members as $member)
		{
			$person=$pifu->person($key=(string)$member->sourcedid->id);
			$pifu->person_memberships($member->sourcedid->id);
			$pifu->person_memberships($member->sourcedid->id,1);
		}
	}
}