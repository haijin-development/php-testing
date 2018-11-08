<?php

namespace ExpectFileContentsTest;


class ExpectFileContentsTest extends \PHPUnit\Framework\TestCase
{
    use \Haijin\Testing\ExceptionsExpectationsTrait;
    use \Haijin\Testing\FilesExpectationsTrait;

    public function testExpectFileContentsWithStringPasses()
    {
        $this->expectFileContents(
            "Contents",
            __DIR__ . "/files/sample-file.txt"
        );

        $this->assertTrue( true );
    }

    public function testExpectFileContentsWithStringFails()
    {
        $this->expectExactExceptionRaised(
            "PHPUnit\Framework\ExpectationFailedException",
            function() {

                $this->expectFileContents(
                    "File contents",
                    __DIR__ . "/files/sample-file.txt"
                );

            },
            function($exception) {
                $this->assertEquals(
                    "Failed asserting that two strings are equal.", $exception->getMessage()
                );
            }
        );
    }

    public function testExpectFileContentsWithClosurePasses()
    {
        $this->expectFileContents(
            function($actual_file_contents) {
                $this->assertEquals( "Contents", $actual_file_contents );
            },
            __DIR__ . "/files/sample-file.txt"
        );

        $this->assertTrue( true );
    }

    public function testExpectFileContentsWithClosureFails()
    {
        $this->expectExactExceptionRaised(
            "PHPUnit\Framework\ExpectationFailedException",
            function() {

                $this->expectFileContents(
                    function($actual_file_contents) {
                        $this->assertEquals( "File contents", $actual_file_contents );
                    },
                    __DIR__ . "/files/sample-file.txt"
                );

            },
            function($exception) {
                $this->assertEquals(
                    "Failed asserting that two strings are equal.", $exception->getMessage()
                );
            }
        );
    }
}
