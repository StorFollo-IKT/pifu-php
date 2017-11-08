<?Php
$starttime=microtime(true);
require 'pifu_parser_cache.class.php';
//printf("%3f sekund(er) linje %d\n",microtime(true)-$starttime,__LINE__);
$pifu=new pifu_parser_cache;
$pifu->load_xml(true);

foreach($pifu->schools() as $school)
{
	$groups=$pifu->ordered_groups((string)$school->sourcedid->id);
	foreach($groups as $group)
	{
		echo (string)$group->comments."\n";
		$members=$pifu->members($group->sourcedid->id);
		if(empty($members))
			continue;
		foreach($members as $member)
		{
			$person=$pifu->person($key=(string)$member->sourcedid->id);
		}
	}
}