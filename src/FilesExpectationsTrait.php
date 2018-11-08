<?php

namespace Haijin\Testing;

/**
 * Trait to extend TestCase with expectations on files.
 *
 * Example of use:
 *
 *      class YourTest extends TestCase
 *      {
 *          use \Haijin\Testing\FilesExpectationsTrait;
 *
 *          // your test code here ...
 *      }
 */
trait FilesExpectationsTrait {
    /**
     * Expects a file to have the given contents
     *
     * The expectations can be constant values:
     *
     *      $this->expectFileContents(
     *                  "File contents,
     *                  $file_path,
     *                );
     *
     * or closures:
     *
     *      $this->expectFileContents(
     *                  function($file_contents) {
     *                      $this->assertEquals( "Contents", $file_contents ); },
     *                  $file_path,
     *                );
     *
     * @param string|closure $$expected_file_contents The expected file contents.
     * @param string $file_path The full path of the file.
     */
    public function expectFileContents($expected_file_contents, $file_path)
    {
        $actual_file_contents = file_get_contents( $file_path );

        if( is_callable( $expected_file_contents ) ) {
            $expected_file_contents->call( $this, $actual_file_contents );
        } else {
            $this->assertEquals( $expected_file_contents, $actual_file_contents );
        }
    }
}