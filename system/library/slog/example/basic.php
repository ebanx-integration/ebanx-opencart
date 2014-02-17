<?php

require_once dirname(__DIR__) . '/src/autoload.php';

// Create a new text file log on /tmp/slog.log
$logger = new \Slog\Slog();

// Write to the log
$logger->write("Foobar");
$logger->write("Foobaz");

// Read from the log
var_dump($logger->read());

// Clear the log file (deletes it)
$logger->clear();