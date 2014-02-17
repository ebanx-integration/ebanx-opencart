Slog [![Build Status](https://travis-ci.org/guhemama/slog.png?branch=master)](https://travis-ci.org/guhemama/slog)
====

A simple PHP logger.

## Usage
Clone the repo or install it via Packagist.

``` json
{
  "require" : {
    "slog/slog": "dev-master"
  }
}
```

The default log writes to _/tmp/slog.log_.

``` php
<?php

require_once dirname(__DIR__) . '/src/autoload.php';

// Create a new text file log on /tmp/slog.log
$logger = new \Slog\Slog();

// Write to the log
$logger->write("Foobar");
$logger->write("Foobaz");

// Read from the log
$logger->read();

// Clear the log
$logger->clear();
```

Options can be set by creating a writer instance:

``` php
<?php

require_once dirname(__DIR__) . '/src/autoload.php';

// Create a text file log writer
$writer = new \Slog\Slog\File(array(
    'filename'  => 'slog'
  , 'extension' => 'log'
  , 'path'      => '/tmp'
));

// Create a new logger
$logger = new \Slog\Slog($writer);
```