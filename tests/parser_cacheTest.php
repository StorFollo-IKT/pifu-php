<?php
/**
 * Created by PhpStorm.
 * User: abi
 * Date: 06.08.2019
 * Time: 11:45
 */

namespace askommune\pifu_parser\tests;
use askommune\pifu_parser\parser_cache;
use Symfony\Component\Filesystem\Filesystem;

class parser_cacheTest extends pifu_parserTest
{
    public static function setUpBeforeClass(): void
    {
        copy(__DIR__.'/sample_data/test_config.php', __DIR__.'/config.php');
    }
    public static function tearDownAfterClass(): void
    {
        unlink(__DIR__.'/config.php');
    }

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        set_include_path(__DIR__);
        $this->pifu = new parser_cache();
    }
    public function tearDown(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->pifu->config['pifu_cache_dir']);
    }

    /**
     * Caching not implemented
     */
    public function testGroup_info()
    {
        $this->markTestSkipped();
    }

    /**
     * Caching not implemented
     */
    public function testGroup_info_id()
    {
        $this->markTestSkipped();
    }

    /**
     * Caching not implemented
     */
    public function testPerson_by_userid()
    {
        $this->markTestSkipped();
    }

    public function testPerson_memberships()
    {
        parent::testPerson_memberships();
    }
}
