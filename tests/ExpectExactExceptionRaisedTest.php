<?php

namespace ExceptionsExpectationsTraitTest;


class ExceptionsExpectationsTraitTest extends \PHPUnit\Framework\TestCase
{
    use \Haijin\Testing\ExceptionsExpectationsTrait;

    public function testExpectSingleExceptionWithNoAssertionClosure()
    {
        $this->tested_closure_was_called = false;

        $this->expectExactExceptionRaised(
            \ExceptionsExpectationsTraitTest\CustomException::class,
            function(){
                $this->tested_closure_was_called = true;
                throw new CustomException( 123 );
            }
        );

        $this->assertTrue( $this->tested_closure_was_called );
    }

    public function testExpectSingleExceptionWithAssertionClosure()
    {
        $this->tested_closure_was_called = false;
        $this->assertion_closure_was_called = false;

        $this->expectExactExceptionRaised(
            \ExceptionsExpectationsTraitTest\CustomException::class,
            function(){
                $this->tested_closure_was_called = true;
                throw new CustomException( 123 );
            },
            function( $exception ){ 
                $this->assertion_closure_was_called = true;
                $this->assertEquals( 123, $exception->value );
            }
        );

        $this->assertTrue( $this->tested_closure_was_called );
        $this->assertTrue( $this->assertion_closure_was_called );        
    }

    public function testRaisingADifferentExceptionFromTheExpectedOne()
    {
        $this->tested_closure_was_called = false;
        $this->assertion_closure_was_called = false;
        $this->original_exception_was_reraised = false;

        try {
            $this->expectExactExceptionRaised(
                \ExceptionsExpectationsTraitTest\CustomException::class,
                function(){
                    $this->tested_closure_was_called = true;
                    throw new \Exception();
                },
                function( $exception ){ 
                    $this->assertion_closure_was_called = true;
                }
            );
        } catch(\Exception $exception) {
            $this->original_exception_was_reraised = true;
        }

        $this->assertTrue( $this->tested_closure_was_called );
        $this->assertFalse( $this->assertion_closure_was_called );        
        $this->assertTrue( $this->original_exception_was_reraised );        
    }
}

class CustomException extends \Exception
{
    public function __construct($value)
    {
        $this->value = $value;
    }
}
