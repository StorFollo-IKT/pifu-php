<?Php
require_once 'pifu_parser.class.php';
class pifu_parser_cache extends pifu_parser
{
	public $cachedir;
	public $xml_file;
	function __construct($xml_file=false,$cachedir=false)
	{
		if(file_exists(__DIR__.'/config.php'))
		{
			require __DIR__.'/config.php';
			$this->cachedir=$config['pifu_cache_dir'];
			$this->xml_file=$config['pifu_xml_file'];
		}
		else
		{
			$this->cachedir=__DIR__.'/cache';
			$this->xml_file=__DIR__.'/pifuData.xml';
		}

		if(!file_exists($this->cachedir))
		{
			if(@mkdir($this->cachedir)===false)
			{
				//$this->cachedir=sys_get_temp_dir().'/pifu-php_cache';
				throw new Exception('Unable to create cache folder '.$this->cachedir);
				//mkdir($this->cachedir);
			}
		}
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
	function load_xml($xml_file=false)
	{
		if($this->xml!==false)
			return;
		if($xml_file!==false)
			$this->xml_file=$xml_file;

		$processed_file=$this->cachedir.'/pifuData_processed.xml';
		if(!file_exists($this->xml_file))
			throw new Exception('Could not find XML file '.$this->xml_file);
		$original_time=filemtime($this->xml_file);

		if(!file_exists($processed_file))
			$processed_time=0;
		else
			$processed_time=filemtime($processed_file);
		if($original_time>$processed_time)
		{
			array_map('unlink', glob($this->cachedir.'/*.json'));
			if(file_exists($processed_file))
				unlink($processed_file);
			parent::load_xml($this->xml_file);
			$this->xml->asXML($processed_file);
		}
		else
		{
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
	function group_members($group,$status=1)
	{
		$cachefile=sprintf('%s/group_members_%s_%s.json',$this->cachedir,$group,$status);
		if(!file_exists($cachefile))
		{
			$this->load_xml();
			$members=parent::group_members($group,array('status'=>1));
			if(file_put_contents($cachefile,json_encode($members))===false)
			{
				trigger_error('Failed to create cache file');
				return $members;
			}
		}
		return json_decode(file_get_contents($cachefile));
	}
	function person_memberships($person,$status=false)
	{
		if($status===false)
			$status='false';
		$cachefile=sprintf('%s/person_memberships_%s_%s.json',$this->cachedir,$person,$status);
		if(!file_exists($cachefile))
		{
			$this->load_xml();
			$memberships=parent::person_memberships($person,$status);
			if(file_put_contents($cachefile,json_encode($memberships))===false)
			{
				trigger_error('Failed to create cache file');
				return $memberships;
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