<?Php
$starttime=microtime(true);
require 'pifu_parser_cache.class.php';
//printf("%3f sekund(er) linje %d\n",microtime(true)-$starttime,__LINE__);
$pifu=new pifu_parser_cache;
$pifu->load_xml();

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