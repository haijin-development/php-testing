<?php

namespace Haijin\Testing;

/**
 * Trait to extend TestCase with expectations on raised Exceptions.
 *
 * Example of use:
 *
 *      class YourTest extends TestCase
 *      {
 *          use \Haijin\Testing\ExceptionsExpectationsTrait;
 *
 *          // your test code here ...
 *      }
 */
trait ExceptionsExpectationsTrait{
    /**
     * Expects the $tested_closure to raise the exact $expected_exception_class.
     *
     * Exact means that if a subclass of the $expected_exception_class is raised instead the expectation will fail.
     * If an $assertion_closure is given it will be evaluated with the raised exception object as its parameter,
     * and you can perform further assertions on that object.
     *
     * Example of use with no assertion closure on the exception:
     *
     *      class YourTest extends TestCase
     *      {
     *          use \Haijin\Testing\ExceptionsExpectationsTrait;
     *
     *          function someTest() {
     *              // test code here ...
     *
     *              // Expects the closure (second parameter) to raise an exception named SomeException.
     *              $this->expectExactExceptionRaised(
     *                  'SomeException',
     *                  function() {
     *                      $this->some_object->do_something_that_may_raise_the_exception();
     *                  }
     *              );
     *
     *              // more test code here ...
     *          }
     *      }
     *
     * Example of use with an assertion closure on the exception:
     *
     *      class YourTest extends TestCase
     *      {
     *          use \Haijin\Testing\ExceptionsExpectationsTrait;
     *
     *          function someTest() {
     *              // test code here ...
     * 
     *              // Expects the closure (second parameter) to raise an exception named SomeException.
     *              $this->expectExactExceptionRaised(
     *                  'SomeException',
     *                  function() {
     *                      $this->some_object->do_something_that_may_raise_the_exception();
     *                  },
     *                  function($raised_exception) {
     *                      $this->assertEquals( 123, $raised_exception->get_value() );
     *                  }
     *              );
     *
     *              // more test code here ...
     *          }
     *      }
     *
     * @param $expected_exception_class string The fully qualified name of the expected exception.
     * @param $tested_closure closure The closure with the code you expect to raise the expected exception.
     * @param $assertion_closure closure Optional - A closure of the form function($exception){} where you
     *          can run assertions on the $exception object.
     */
    public function expectExactExceptionRaised($expected_exception_class, $tested_closure, $assertion_closure = null)
    {
        try {
            $tested_closure->call( $this );

            $this->fail( "A {$expected_exception_class} was expected but none was raised." );

        } catch( \Exception $raised_exception ) {
            switch( get_class( $raised_exception ) ) {
                case $expected_exception_class:
                    if( $assertion_closure !== null ) {
                        $assertion_closure->call( $this, $raised_exception );
                    }
                    break;

                default:
                    throw $raised_exception;
            }
        }
    }
}