<?php
/**
 * Parses contents of whole log
 */

namespace Gumbercules\MysqlSlow;
use Gumbercules\MysqlSlow\LogEntry;


class LogParser
{

    //raw contents of whole log
    protected $contents;

    //this will be filled up with an array of objects for each entry
    protected $entries = [];

    public function __construct($logContents)
    {
        $this->contents = $logContents;
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return array
     */
    protected function getEntries()
    {
        return $this->entries;
    }

    //parse the contents of the log
    public function parseEntries()
    {
        //split log into separate entries
        $entries = $this->splitLogIntoEntries();

        //loop entries and add to $this->entries property
        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }

        return $this->getEntries();
    }

    /*
     * take whole raw log data and split into individual entries
     * @returns array of entries
     */
    protected function splitLogIntoEntries()
    {
        $pattern = "/(# Time(?:.*\n))/";
        $entries = preg_split($pattern, $this->contents, -1);

        if (count($entries) == 1) {
            throw new Exception\ParseErrorException("Failed to parse file");
        }
        return array_filter($entries);
    }

    //take individual entry raw data and create entry object to add to $this->entries property
    protected function addEntry($entryData)
    {
        $this->entries[] = new LogEntry($entryData);
    }

}