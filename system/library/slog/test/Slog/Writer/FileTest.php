<?php

use Slog\Writer\File as Writer;

class FileTest extends PHPUnit_Framework_TestCase
{
    public function teardown()
    {
      // Remove the default log file
      if (file_exists('/tmp/slog.log')) unlink('/tmp/slog.log');
    }

    public function testCanRead()
    {
      $writer = new Writer();
      $writer->write('Test string');
      $str = $writer->read();

      $this->assertEquals("Test string\n", $str);
    }

    public function testCantRead()
    {
      $writer = new Writer(array(
        'path' => '/foo'
      ));
      $this->assertEquals('', $writer->read());
    }

    public function testCanWrite()
    {
      $writer = new Writer();
      $this->assertTrue($writer->write('Test string'));
    }

    public function testCantWrite()
    {
      $this->setExpectedException('RuntimeException', 'The directory /foo cannot be written.');

      $writer = new Writer(array(
        'path' => '/foo'
      ));
      $writer->write('Test string');
    }

    public function testClearRealFile()
    {
      $writer = new Writer();
      $writer->write('Test string');
      $this->assertTrue($writer->clear());
      $this->assertFalse(file_exists('/tmp/slog.log'));
    }

    public function testClearNoFile()
    {
      $writer = new Writer();
      $this->assertFalse($writer->clear());
    }
}