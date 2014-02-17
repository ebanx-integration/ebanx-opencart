<?php

namespace Slog;

/**
 * The logger class
 */
class Slog
{
  /**
   * Constructor. Accepts a Writer instance, if empty creates a flat file
   * logger on /tmp/slog.log
   * @param \Slog\Writer\Writer $writer The writer instance
   */
  public function __construct(\Slog\Writer\Writer $writer = null)
  {
    $this->_writer = ($writer == null) ? new Writer\File() : $writer;
  }

  /**
   * Writes text to log. Throws exception when the text is empty.
   * @param  string $text The text to be logged
   * @return bool
   */
  public function write($text)
  {
    if (strlen($text) == 0)
    {
      throw new \RuntimeException("The empty string cannot be logged.");
    }

    return $this->_writer->write($text);
  }

  /**
   * Reads everything from the log
   * @return string
   */
  public function read()
  {
    return $this->_writer->read();
  }

  /**
   * Clears the log
   * @return bool
   */
  public function clear()
  {
    return $this->_writer->clear();
  }
}