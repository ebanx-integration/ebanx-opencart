<?php

namespace Slog\Writer;

/**
 * Abstract log writer. All writers must extend this class.
 */
abstract class Writer
{
  /**
   * The options array
   * @var array
   */
  protected $_options = array();

  /**
   * Writes text to log
   * @param  string $text The text to be logged
   * @return bool
   */
  abstract function write($text);

  /**
   * Reads everything from the log
   * @return string
   */
  abstract function read();

  /**
   * Clears the log
   * @return bool
   */
  abstract function clear();

  /**
   * Constructor. Accepts writer options.
   * @param array $options The options array (key => value)
   */
  public function __construct($options = null)
  {
    if (is_array($options))
    {
      foreach ($options as $option => $value)
      {
        $this->_options[$option] = $value;
      }
    }
  }
}