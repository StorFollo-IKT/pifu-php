<?php
//https://github.com/iktsenteret/pifu/blob/master/pifu-ims/docs/spesifikasjon.md
class pifu_parser
{
    /**
     * @var SimpleXMLElement
     */
	public $xml=null;
	function __construct($xml_file=false)
	{
		$this->load_xml($xml_file);
	}
	function load_xml($xml_file=false)
	{
		if($xml_file===false)
			$xml_file=__DIR__.'/pifuData.xml';
		if(!file_exists($xml_file))
			throw new Exception('Could not find XML file '.$xml_file);
		$xml_string=file_get_contents($xml_file);
		$xml_string=str_replace(' xmlns="http://pifu.no/xsd/pifu-ims_sas/pifu-ims_sas-1.1"','',$xml_string); //Remove namespace
		$this->xml=simplexml_load_string($xml_string);
	}
	function groups($school,$level=1)
	{
		$xpath_klasser_skole=sprintf('/enterprise/group/relationship/sourcedid/id[.="%s"]/ancestor::group/grouptype/typevalue[@level=%d]/ancestor::group',$school,$level);
		return $this->xml->xpath($xpath_klasser_skole);
	}
	function schools()
	{
		$xpath='/enterprise/group/grouptype[scheme="pifu-ims-go-org" and typevalue[@level=2]]/ancestor::group';
		return $this->xml->xpath($xpath);
	}
	/**
     * @deprecated To be removed, use group_members instead
     **/
	function members($group)
	{
		if(is_object($group))
			$group=$group->sourcedid->id;
		if(empty($group))
			throw new InvalidArgumentException('Empty argument');

		$xpath=sprintf('/enterprise/membership/sourcedid/id[.="%s"]/ancestor::membership/member',$group);
		return $this->xml->xpath($xpath);
	}

    /**
     * Get members of a group
     * @param string|SimpleXMLElement $group
     * @param array $options
     * @return mixed
     */
	function group_members($group,$options=array('status'=>1,'roletype'=>false))
	{
		if(is_object($group) && !empty($group->sourcedid->id))
			$group=(string)$group->sourcedid->id;
		else
			$group=(string)$group;

		if(empty($group))
			throw new InvalidArgumentException('Empty argument');
		if(!is_string($group))
			throw new InvalidArgumentException('Invalid argument');
		if(isset($options['roletype']) && $options['roletype']!==false && !is_string($options['roletype']))
			throw new InvalidArgumentException('roletype must be string');

		$xpath=sprintf('/enterprise/membership/sourcedid/id[.="%s"]/ancestor::membership/member',$group);
		if(isset($options['roletype']))
			$xpath=sprintf('%s/role[@roletype="%s"]',$xpath,$options['roletype']);
		else
			$xpath.='/role';

		if(isset($options['status']) && $options['status']!==false)
			$xpath.=sprintf('/status[.="%d"]',$options['status']);

		$xpath.='/ancestor::member';

		return $this->xml->xpath($xpath);
	}

	function person_memberships($person,$status=false)
	{
		if(empty($person) || !is_string($person))
			throw new InvalidArgumentException('Empty or invalid argument');
		if($status===false)
			$xpath=sprintf('/enterprise/membership/member/sourcedid/id[.="%s"]/parent::sourcedid/parent::member',$person);
		else
			$xpath=sprintf('/enterprise/membership/member/sourcedid/id[.="%s"]/parent::sourcedid/parent::member/role/status[.="%d"]/parent::role/parent::member',$person,$status);
		return $this->xml->xpath($xpath);
	}
	function person($id)
	{
		$xpath=sprintf('/enterprise/person/sourcedid/id[.="%s"]/ancestor::person',$id);
		$person=$this->xml->xpath($xpath);
		if(empty($person))
			return false;
		else
			return $person[0];
	}
	function person_by_userid($id,$type)
	{
		$xpath=sprintf('/enterprise/person/userid[@useridtype="%s" and .="%s"]/ancestor::person',$type,$id);
		$person=$this->xml->xpath($xpath);
		if(empty($person))
			return false;
		else
			return $person[0];
	}
	function phone($person,$teltype)
	{
		$xpath=sprintf('.//tel[@teltype="%s"]',$teltype);
		$result=$person->xpath($xpath);
		if(!empty($result))
			return (string)$result[0];
	}
	function ordered_groups($school)
	{
		foreach($this->groups($school) as $group)
		{
			$id=(string)$group->sourcedid->id;
			$sort_parameter=(string)$group->description->short;
			$groups[$sort_parameter]=$group;
		}
		if(!isset($groups))
			return false;
		ksort($groups,SORT_NATURAL);
		return $groups;
	}
	//Order group members by name
	function ordered_members($group,$order_by_name='given')
	{
		foreach($this->members($group) as $member)
		{
			preg_match('/Schoolclass member (.+?), (.+)/',$member->comments,$name);

			if($order_by_name==='given')
				$key=2;
			elseif($order_by_name==='family')
				$key=1;
			else
				throw new Exception('Invalid sort');
			if(!isset($name[$key]))
				$members[]=$member;
			else
				$members[$name[$key]]=$member;
		}
		if(empty($members))
			return false;
		ksort($members,SORT_NATURAL);
		return $members;
	}
	
}