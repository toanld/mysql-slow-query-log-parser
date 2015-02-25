<?php

namespace Gumbercules\MysqlSlow\test;
use Gumbercules\MysqlSlow\LogParser;
use Gumbercules\MysqlSlow\LogFileReader;


class LogParserTest extends \PHPUnit_Framework_TestCase
{

    public $sampleLog = "test/sample/sample_log.log";

    public $sampleLogContents;

    public $expectedData = [
        [
            "host" => "localhost",
            "user" => "app_main",
            "datetime" => null,
            "queryTime" => 1.671918,
            "lockTime" => 0.000000,
            "rowsSent" => 164,
            "rowsExamined" => 164,
            "query" => "SHOW TABLES FROM `app`;"
        ],
        [
            "host" => "localhost",
            "user" => "app_main",
            "datetime" => null,
            "queryTime" => 3.546966,
            "lockTime" => 0.000000,
            "rowsSent" => 1,
            "rowsExamined" => 4,
            "query" => "SELECT `User`.`id`, `User`.`windows_username`, `User`.`password`, `User`.`email`, `User`.`first_name`, `User`.`last_name` GROUP BY `User`.`id`;"
        ],
        [
            "host" => "localhost",
            "user" => "mysqlbackup",
            "datetime" => null,
            "queryTime" => 1.375035,
            "lockTime" => 0.000000,
            "rowsSent" => 28914,
            "rowsExamined" => 28914,
            "query" => "SELECT /*!40001 SQL_NO_CACHE */ * FROM `archived_projects`;"
        ],
        [
            "host" => "localhost",
            "user" => "mysqlbackup",
            "datetime" => null,
            "queryTime" => 2.265683,
            "lockTime" => 0.000000,
            "rowsSent" => 5713,
            "rowsExamined" => 5713,
            "query" => "SELECT /*!40001 SQL_NO_CACHE */ * FROM `audits`;"
        ],
    ];

    public $expectedTimestamps = [
        1424802716,
        1424804409,
        1424804416,
        1424804419
    ];

    public function setUp()
    {
        $this->sampleLogContents = file_get_contents($this->sampleLog);

        //create datetime objects for expected data
        foreach ($this->expectedData as $k => &$expectedEntry) {
            $expectedEntry["datetime"] = new \DateTime("@" . $this->expectedTimestamps[$k]);
        }
    }

    public function testMainClass()
    {
        $parser = new LogFileReader($this->sampleLog);
        $entries = $parser->parseFile();

        foreach ($entries as $k => $entry) {
            $this->assertEquals($entry->getDatetime(), $this->expectedData[$k]["datetime"]);

            $this->assertEquals($entry->getUser(), $this->expectedData[$k]["user"]);

            $this->assertEquals($entry->getHost(), $this->expectedData[$k]["host"]);

            $this->assertEquals($entry->getQueryTime(), $this->expectedData[$k]["queryTime"]);

            $this->assertEquals($entry->getLockTime(), $this->expectedData[$k]["lockTime"]);

            $this->assertEquals($entry->getRowsSent(), $this->expectedData[$k]["rowsSent"]);

            $this->assertEquals($entry->getRowsExamined(), $this->expectedData[$k]["rowsExamined"]);

            $this->assertEquals($entry->getQuery(), $this->expectedData[$k]["query"]);
        }
    }

    public function testLogParser()
    {
        $parser = new LogParser($this->sampleLogContents);
        $this->assertEquals($parser->getContents(), $this->sampleLogContents);

        $entries = $parser->parseEntries();

        foreach ($entries as $k => $entry) {
            $this->assertEquals($entry->getDatetime(), $this->expectedData[$k]["datetime"]);

            $this->assertEquals($entry->getUser(), $this->expectedData[$k]["user"]);

            $this->assertEquals($entry->getHost(), $this->expectedData[$k]["host"]);

            $this->assertEquals($entry->getQueryTime(), $this->expectedData[$k]["queryTime"]);

            $this->assertEquals($entry->getLockTime(), $this->expectedData[$k]["lockTime"]);

            $this->assertEquals($entry->getRowsSent(), $this->expectedData[$k]["rowsSent"]);

            $this->assertEquals($entry->getRowsExamined(), $this->expectedData[$k]["rowsExamined"]);

            $this->assertEquals($entry->getQuery(), $this->expectedData[$k]["query"]);
        }
    }

}