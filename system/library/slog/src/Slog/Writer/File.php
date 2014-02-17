<?php

namespace Slog\Writer;

/**
 * Flat file writer
 */
class File extends Writer
{
  /**
   * Writer options:
   * - filename: the log filename
   * - extension: the log file extension
   * - path: the log file location
   * @var array
   */
  protected $_options = array(
      'filename'  => 'slog'
    , 'extension' => 'log'
    , 'path'      => '/tmp'
  );

  /**
   * Writes the string to the log file
   * @param  string $text The text to be logged
   * @return bool
   */
  public function write($text)
  {
    $this->_isWritable($this->_options['path']);

    $filepath = $this->_fullpath();

    $handle  = fopen($filepath, 'a+');
    $written = fwrite($handle, $text . "\n");
    fclose($handle);

    if (!$written)
    {
      throw new \RuntimeException("The file {$filepath} could not be written.");
    }

    return true;
  }

  /**
   * Eagerly reads the log file
   * @return string
   */
  public function read()
  {
    // If the file is readable return it, otherwise return an empty string
    if ($this->_isReadable($this->_fullpath()))
    {
      return file_get_contents($this->_fullpath());
    }

    return '';
  }

  /**
   * Deletes the log file
   * @return bool
   */
  public function clear()
  {
    if (file_exists($this->_fullpath()))
    {
      return unlink($this->_fullpath());
    }

    return false;
  }

  /**
   * Checks if a directory/file is writable, throws exception when it's not
   * @param  string  $path The target path
   * @return bool
   */
  protected function _isWritable($path)
  {
    if (!is_writable($path))
    {
      throw new \RuntimeException("The directory {$path} cannot be written.");
    }

    return true;
  }

  /**
   * Checks if a directory/file is readable, throws exception when it's not
   * @param  string  $path The target path
   * @return bool
   */
  protected function _isReadable($path)
  {
    if (!is_readable($path))
    {
      return false;
    }

    return true;
  }

  /**
   * Gets the file full pathname
   * @return string
   */
  protected function _fullpath()
  {
    return $this->_options['path'] . '/' . $this->_options['filename']
                . '.' . $this->_options['extension'];
  }
}