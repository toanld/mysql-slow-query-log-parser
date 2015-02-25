<?php
/**
 * Abstraction class to represent each entry in the slow query log
 */

namespace Gumbercules\MysqlSlow;
use Gumbercules\MysqlSlow\Exception\ParseErrorException;


class LogEntry
{
    //raw data from log will be stored here
    protected $data;

    //query datetime as DateTime object
    protected $datetime;

    protected $user;

    protected $host;

    protected $queryTime;

    protected $lockTime;

    protected $rowsSent;

    protected $rowsExamined;

    protected $query;

    /*
     * @param string $data: raw data for one log entry
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->parseEntry();
    }

    /**
     * @return mixed
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function getQueryTime()
    {
        return $this->queryTime;
    }

    /**
     * @return mixed
     */
    public function getLockTime()
    {
        return $this->lockTime;
    }

    /**
     * @return mixed
     */
    public function getRowsSent()
    {
        return $this->rowsSent;
    }

    /**
     * @return mixed
     */
    public function getRowsExamined()
    {
        return $this->rowsExamined;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /*
     * parse entry and set object properties
     */
    protected function parseEntry()
    {
        $this->host = $this->parseHost();
        $this->user = $this->parseUser();
        $this->queryTime = $this->parseQueryTime();
        $this->lockTime = $this->parseLockTime();
        $this->rowsSent = $this->parseRowsSent();
        $this->rowsExamined = $this->parseRowsExamined();

        //fourth line time stamp
        $this->datetime = $this->parseTimestamp();

        //final line - the query itself
        $this->query = $this->parseQuery();
    }

 
    protected function parseUser()
    {
        if (preg_match("/User@Host: (.*)\[.*\] @/", $this->data, $matches) === false) {
            throw new Exception\ParseErrorException("Couldn't parse user");
        }
        return $matches[1];
    }

    protected function parseHost()
    {
        if (preg_match("/User@Host: .*@ (.*) /", $this->data, $matches) === false) {
            throw new Exception\ParseErrorException("Couldn't parse host");
        }
        return $matches[1];
    }

    protected function parseQueryTime()
    {
        if (preg_match("/Query_time: (([0-9]|\.)*)/", $this->data, $matches) === false) {
            throw new Exception\ParseErrorException("Couldn't parse query time");
        }
        return $matches[1];
    }

    protected function parseLockTime()
    {
        if (preg_match("/Lock_time: (([0-9]|\.)*)/", $this->data, $matches) === false) {
            throw new Exception\ParseErrorException("Couldn't parse lock time");
        }
        return $matches[1];
    }

    protected function parseRowsSent()
    {
        if (preg_match("/Rows_sent: (([0-9])*)/", $this->data, $matches) === false) {
            throw new Exception\ParseErrorException("Couldn't parse rows sent");
        }
        return $matches[1];
    }

    protected function parseRowsExamined()
    {
        if (preg_match("/Rows_examined: (([0-9])*)/", $this->data, $matches) === false) {
            throw new Exception\ParseErrorException("Couldn't parse rows examined");
        }
        return $matches[1];
    }

    protected function parseTimestamp()
    {
        if (preg_match("/SET timestamp=([0-9]*);/", $this->data, $matches) === false) {
            throw new Exception\ParseErrorException("Couldn't parse timestamp");
        }
        return new \DateTime("@".$matches[1]);
    }

    protected function parseQuery()
    {
        //query is on the last line of the entry
        $lines = array_filter(explode("\n", $this->data));
        return array_pop($lines);
    }
}