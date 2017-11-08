<?php
//https://github.com/iktsenteret/pifu/blob/master/pifu-ims/docs/spesifikasjon.md
class pifu_parser
{
	public $xml=false;
	function __construct()
	{
		$this->load_xml();
	}
	function load_xml()
	{
		$xml_string=file_get_contents(__DIR__.'/pifuData.xml');
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
	function members($group)
	{
		if(empty($group))
			throw new Exception('Empty argument');
		$xpath=sprintf('/enterprise/membership/sourcedid/id[.="%s"]/ancestor::membership/member',$group);
		return $this->xml->xpath($xpath);
	}
	function person($id)
	{
		$xpath=sprintf('/enterprise/person/sourcedid/id[.="%s"]/ancestor::person',$id);
		return $this->xml->xpath($xpath)[0];
	}
	function person_by_userid($id,$type)
	{
		$xpath=sprintf('/enterprise/person/userid[@useridtype="%s" and .="%s"]/ancestor::person',$type,$id);
		return $this->xml->xpath($xpath)[0];
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