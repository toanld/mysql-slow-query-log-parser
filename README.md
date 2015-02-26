# mysql-slow-query-log-parser
PHP lib for parsing a MySQL slow query log

# Usage

Install with composer:
```
composer require gumbercules/mysqlslow
```

Include the namespace:
```
use Gumbercules\MysqlSlow;
```

Instantiate new file reader:
```
$parser = new LogParser($file_path);
```
`$file_path` must be the path to a .log file

Get the log entries with:
```
$parser->getEntries();
```

This returns an array of `LogEntry` objects for your .log file. See `LogEntry.php` for the methods available for accessing various properties. For example: `$entry->getQueryTime();` will return the query duration for an entry.


# Other

This library is used by my tool [MySQL Slow Query Log Viewer](https://github.com/garethellis36/MySQL-slow-query-log-viewer). Check that out if you fancy!

# Contribute

Feel free to fork and PR. I haven't finished writing tests for this yet, and also I'd like to hear if people have issues with their own logs; all my own tests were using real logs from a production server but I honestly don't know if there are variations across logs which could cause issues.

# Contact
@garethellis on Twitter <3
