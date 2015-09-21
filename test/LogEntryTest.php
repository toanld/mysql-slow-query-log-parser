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
        $entry = new LogEntry($this->sampleEntry, new \DateTimeZone(date_default_timezone_get()));

        $this->assertEquals($entry->getDatetime(), new \DateTime("@1395521698"));

        $this->assertEquals($entry->getUser(), "root");

        $this->assertEquals($entry->getHost(), "localhost");

        $this->assertEquals($entry->getQueryTime(), 0.000303);

        $this->assertEquals($entry->getLockTime(), 0.000090);

        $this->assertEquals($entry->getRowsSent(), 15);

        $this->assertEquals($entry->getRowsExamined(), 20);

        $this->assertEquals($entry->getQuery(), "SELECT * FROM users WHERE name = 'Jesse';");
    }

}