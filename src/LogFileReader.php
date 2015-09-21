<?php

namespace Gumbercules\MysqlSlow;
use Gumbercules\MysqlSlow\Exception\FileNotFoundException;
use Gumbercules\MysqlSlow\Exception\InvalidFileException;
use Gumbercules\MysqlSlow\LogParser;


class LogFileReader
{

    //SplFileInfo object for the file
    protected $file;

    //SplFileObject object
    protected $fileContents;

    //instance of LogParser
    protected $logParser;

    /*
     * @param string $file - file path for log file - file with .log expected
     * @throws FileNotFoundException if file is not found
     */
    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new Exception\FileNotFoundException("File not found");
        }
        $this->loadFile($file);
    }

    /*
     * creates SplFileInfo object and sets as $this->file
     * @param string $file - file path
     * @throws InvalidFileException if file extension is not .log
     * @returns void
     */
    protected function loadFile($file)
    {
        $this->file = new \SplFileInfo($file);
        if ($this->file->getExtension() != "log") {
            throw new Exception\InvalidFileException("Please provide a .log file");
        }
    }

    /*
     * read the contents of the file
     * @throws FileNotReadableException if file is not readable
     * @returns string with file contents
     */
    protected function readFile()
    {
        if (!$this->file->isReadable()) {
            throw new Exception\FileNotReadableException("File is not readable");
        }
        $this->fileContents = $this->file->openFile("r");
        return $this->fileContents->fread($this->file->getSize());
    }

    /*
     * parse the actual file, returning an array of LogEntry objects
     * @returns array of LogEntry objects
     */
    public function parseFile(\DateTimeZone $timezone = null)
    {
        if (!$timezone) {
            $timezone = new \DateTimeZone(date_default_timezone_get());
        }
        $this->logParser = new \Gumbercules\MysqlSlow\LogParser($this->readFile(), $timezone);
        return $this->logParser->parseEntries();
    }

}