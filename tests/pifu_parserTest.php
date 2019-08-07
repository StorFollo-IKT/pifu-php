<?php


use PHPUnit\Framework\TestCase;
use askommune\pifu_parser\parser;

class pifu_parserTest extends TestCase
{
    /**
     * @var parser
     */
    public $pifu;
    public function setUp(): void
    {
        $this->pifu = new parser(__DIR__.'/sample_data/PIFU-IMS_SAS_eksempel.xml');
    }

    public function testGroup_members()
    {
        $members = $this->pifu->group_members('global_ID_org_17');
        $this->assertIsArray($members);
        $this->assertEquals('Janne Stor sitt medlemskap i Måneflekken skole', $members[0]->{'comments'});
        $this->assertEquals('Ola Nordmann sitt medlemskap i Måneflekken skole', $members[1]->{'comments'});
    }


    public function testPerson_memberships()
    {
        $memberships = $this->pifu->person_memberships('global_ID_01235');
        $this->assertIsArray($memberships);
        $this->assertEquals('Janne Stor sitt medlemskap i Måneflekken skole', $memberships[1]->{'comments'});
    }

    public function testGroup_info_id()
    {
        $group = $this->pifu->group_info_id('global_ID_org_17');
        $this->assertEquals('Informasjon om organisasjonsenheten Måneflekken skole', $group->{'comments'});
    }

    public function testSchools()
    {
        $schools = $this->pifu->schools();
        $this->assertIsArray($schools);
        $school = $schools[0];
        $this->assertEquals('Informasjon om organisasjonsenheten Måneflekken skole', $school->{'comments'});
        $this->assertEquals('global_ID_org_17', $school->sourcedid->id);
    }

    public function testGroup_info()
    {
        $group = $this->pifu->group_info('global_ID_org_17','Basisgruppe 7A ved Måneflekken skole');
        $this->assertEquals('Informasjon om basisgruppa 7A ved Måneflekken skole', $group->{'comments'});
    }

    public function testOrdered_groups()
    {
        $groups = $this->pifu->ordered_groups('global_ID_org_17');
        $this->assertIsArray($groups);
        $keys = array_keys($groups);
        $this->assertEquals('Basisgruppe 7A ved Måneflekken skole', $keys[0]);
    }

    public function testPerson_by_userid()
    {
        $person = $this->pifu->person_by_userid('17097055655', 'personNIN');
        $this->assertEquals('Informasjon om Janne Stor', $person->{'comments'});
    }

    /*public function testPhone()
    {

    }

    public function testValidate()
    {

    }

    public function testGroups()
    {

    }*/

    public function testPerson()
    {
        $person = $this->pifu->person('global_ID_01235');
        $this->assertEquals('Informasjon om Janne Stor', $person->{'comments'});
    }

    /*public function testOrdered_members()
    {

    }*/
}
