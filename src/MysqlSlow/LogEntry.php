<?php
/**
 * Abstraction class to represent each entry in the slow query log
 */

namespace Gumbercules\MysqlSlow;


class LogEntry
{
    //raw data from log will be stored here
    protected $data;

    //query datetime as DateTime object
    public $datetime;

    //user
    public $user;

    //host
    public $host;

    //query duration
    public $queryTime;

    //lock duration
    public $lockTime;

    //rows sent
    public $rowsSent;

    //rows examined
    public $rowsExamined;

    public $query;

    /*
     * @param string $data: raw data for one log entry
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->parseEntry();
    }

    /*
     * parse entry and set object properties
     */
    protected function parseEntry()
    {
        //split raw entry data into lines
        $lines = explode("\n", $this->data);

        //remove unwanted lines
        $lines = $this->removeUnwantedLines($lines);

        //first line is time & date - ignore, we'll use the SET timestamp line instead
        //second line is user & host
        $this->host = $this->parseHost($lines[1]);
        $this->user = $this->parseUser($lines[1]);

        //third line is query time, lock time, rows sent and rows exampined
        $this->queryTime = $this->parseQueryTime($lines[2]);
        $this->lockTime = $this->parseLockTime($lines[2]);
        $this->rowsSent = $this->parseRowsSent($lines[2]);
        $this->rowsExamined = $this->parseRowsExamined($lines[2]);

        //fourth line time stamp
        $this->datetime = $this->parseTimestamp($lines[3]);

        //final line - the query itself
        $this->query = $lines[4];
    }

    /*
     * removes unwanted lines from entry, e.g. "use profile sampling"
     */
    protected function removeUnwantedLines($lines)
    {
        foreach ($lines as $k => $line) {
            if ($line == "use profile_sampling;") {
                unset($lines[$k]);
                continue;
            }
        }
        return array_values($lines);
    }

    protected function parseUser($line)
    {
        preg_match("/User@Host: (.*)\[.*\] @/", $line, $matches);
        return $matches[1];
    }

    protected function parseHost($line)
    {
        preg_match("/User@Host: .*@ (.*) /", $line, $matches);
        return $matches[1];
    }

    protected function parseQueryTime($line)
    {
        preg_match("/Query_time: (([0-9]|\.)*)/", $line, $matches);
        return $matches[1];
    }

    protected function parseLockTime($line)
    {
        preg_match("/Lock_time: (([0-9]|\.)*)/", $line, $matches);
        return $matches[1];
    }

    protected function parseRowsSent($line)
    {
        preg_match("/Rows_sent: (([0-9])*)/", $line, $matches);
        return $matches[1];
    }

    protected function parseRowsExamined($line)
    {
        preg_match("/Rows_examined: (([0-9])*)/", $line, $matches);
        return $matches[1];
    }

    protected function parseTimestamp($line)
    {
        preg_match("/SET timestamp=([0-9]*);/", $line, $matches);
        return new \DateTime("@".$matches[1]);
    }

}