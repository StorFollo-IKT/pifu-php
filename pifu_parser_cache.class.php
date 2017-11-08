<?Php
require_once 'pifu_parser.class.php';
class pifu_parser_cache extends pifu_parser
{
	public $cachedir;
	function __construct()
	{
		$this->cachedir=__DIR__.'/cache';
		if(!file_exists($this->cachedir))
			mkdir($this->cachedir);
	}
	function cache($data,$cachefile)
	{
		$json=json_encode($data);
		if(file_put_contents($this->cachedir.'/'.$cachefile,$json)===false)
		{
			trigger_error('Failed to create cache file');
			return $data;
		}
		return json_decode(file_get_contents($this->cachedir.'/'.$cachefile));
	}
	function load_xml($output=false)
	{
		if($this->xml!==false)
			return;
		$processed_file=__DIR__.'/pifuData_processed.xml';
		$original_time=filemtime(__DIR__.'/pifuData.xml');

		if(!file_exists($processed_file))
			$processed_time=0;
		else
			$processed_time=filemtime($processed_file);
		if($original_time>$processed_time)
		{
			echo "Cache is outdated\n";
			array_map('unlink', glob($this->cachedir.'/*.json'));
			unlink($processed_file);
			parent::load_xml();
			$this->xml->asXML($processed_file);
		}
		else
		{
			if($output)
				echo "Cache is valid\n";
			$this->xml=simplexml_load_file($processed_file);
		}
	}
	function groups($school,$level=1)
	{
		$cachefile=sprintf('%s/groups_%s_%s.json',$this->cachedir,$school,$level);
		if(!file_exists($cachefile))
		{
			$this->load_xml();
			$groups=parent::groups($school,$level);
			$json=json_encode($groups);
			if(file_put_contents($cachefile,$json)===false)
			{
				trigger_error('Failed to create cache file');
				return $groups;
			}
		}
		return json_decode(file_get_contents($cachefile));
	}
	function schools()
	{
		if(!file_exists($this->cachedir.'/schools.json'))
		{
			$this->load_xml();
			$schools=parent::schools();
			$json=json_encode($schools);
			if(file_put_contents($this->cachedir.'/schools.json',$json)===false)
			{
				trigger_error('Failed to create cache file');
				return $schools;
			}
		}
		return json_decode(file_get_contents($this->cachedir.'/schools.json'));
	}
	function members($group)
	{
		$cachefile=sprintf('%s/group_members_%s.json',$this->cachedir,$group);
		if(!file_exists($cachefile))
		{
			$this->load_xml();
			$members=parent::members($group);
			if(file_put_contents($cachefile,json_encode($members))===false)
			{
				trigger_error('Failed to create cache file');
				return $members;
			}
		}
		return json_decode(file_get_contents($cachefile));
	}
	function person($id)
	{
		/*$this->load_xml();
		return parent::person($id);*/
		$cachefile=sprintf('%s/%s.json',$this->cachedir,$id);
		if(!file_exists($cachefile))
		{
			$this->load_xml();
			$person=parent::person($id);
			if(file_put_contents($cachefile,json_encode($person))===false)
			{
				trigger_error('Failed to create cache file');
				return $person;
			}
		}
		return json_decode(file_get_contents($cachefile));
	}
	
	function ordered_groups($school)
	{
		$cachefile=sprintf('%s/ordered_groups_%s.json',$this->cachedir,$school);
		if(!file_exists($cachefile))
		{
			$this->load_xml();
			$ordered_groups=parent::ordered_groups($school);
			if(file_put_contents($cachefile,json_encode($ordered_groups))===false)
			{
				trigger_error('Failed to create cache file');
				return $ordered_groups;
			}
		}
		return json_decode(file_get_contents($cachefile));
	}
	
}