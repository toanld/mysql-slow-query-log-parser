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
    protected $datetimestemp;

    protected $user;

    protected $host;

    protected $queryTime;

    protected $lockTime;

    protected $rowsSent;

    protected $rowsExamined;

    protected $query;

    protected $timezone;

    protected $time;
    protected $FileCall;

    /*
     * @param string $data: raw data for one log entry
     */
    public function __construct($data, \DateTimeZone $timezone)
    {
        $this->data = $data;
        $this->timezone = $timezone;
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
        $this->datetimestemp = $this->parseTimestamp();
        $this->time = $this->parseTime();

        //final line - the query itself
        $this->query = $this->parseQuery();
        $this->FileCall = $this->parseFile();
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
        if (preg_match("/User@Host: .*@([\s\[]+)([0-9\.]+)/", $this->data, $matches) === false) {
            throw new Exception\ParseErrorException("Couldn't parse host");
        }
        return $matches[2];
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
        return $matches[1];
    }

    protected function parseTime()
    {
        if (preg_match("/\s([0-9\:\s]*)/", $this->data, $matches)) {
            return $matches[1];
        }

    }

    protected function parseQuery()
    {
        //query is on the lines after the timestamp
        //break up into lines and find the timestamp line
        $lines = array_filter(explode("\n", $this->data));

        foreach ($lines as $k => $line) {
            if (!preg_match("/SET timestamp=([0-9]*);/", $line, $matches)) {
                unset($lines[$k]);
                continue;
            }
            unset($lines[$k]);
            break;
        }
        $string = trim(implode("\n", $lines));
        $string = preg_replace("/\/\*(.*)\*\//","",$string);
        return $string;
    }

    protected function parseFile()
    {
        //query is on the lines after the timestamp
        //break up into lines and find the timestamp line
        $lines = array_filter(explode("\n", $this->data));

        foreach ($lines as $k => $line) {
            if (!preg_match("/SET timestamp=([0-9]*);/", $line, $matches)) {
                unset($lines[$k]);
                continue;
            }
            unset($lines[$k]);
            break;
        }
        $string = trim(implode("\n", $lines));
        if(preg_match("/\/\*([0-9\.\s\-]+)(.*)\*\//",$string,$matches)){
            $string = base64_decode(trim($matches[2]));
            return str_replace(" - ",chr(13),$string);
        }else{
            return '';
        }
    }
}