<?php
/**
 * Created by PhpStorm.
 * User: garethellis
 * Date: 24/02/15
 * Time: 22:04
 */

namespace Gumbercules\MysqlSlow\test;
use Gumbercules\MysqlSlow\LogEntry;


class LogEntryTest extends \PHPUnit_Framework_TestCase
{

    public $sampleEntry;

    public function setUp()
    {
        $this->sampleEntry = file_get_contents("test/sample/sample_entry.txt");
    }

    public function testValidData()
    {
        $entry = new LogEntry($this->sampleEntry);

        $this->assertEquals($entry->datetime, new \DateTime("@1395521698"));

        $this->assertEquals($entry->user, "root");

        $this->assertEquals($entry->host, "localhost");

        $this->assertEquals($entry->queryTime, 0.000303);

        $this->assertEquals($entry->lockTime, 0.000090);

        $this->assertEquals($entry->rowsSent, 15);

        $this->assertEquals($entry->rowsExamined, 20);

        $this->assertEquals($entry->query, "SELECT * FROM users WHERE name = 'Jesse';");
    }

}