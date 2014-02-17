<?php

class SlogTest extends PHPUnit_Framework_TestCase
{
  /**
   * Tests the flat file logger
   */
  public function testLogWithTextFile()
  {
    $logger = new \Slog\Slog();
    $this->assertTrue(true, $logger->write('First string'));
    $this->assertTrue(true, $logger->write('Second string'));
    $this->assertEquals("First string\nSecond string\n", $logger->read());
    $logger->clear();
    $this->assertFalse(file_exists('/tmp/slog.log'));
  }
}