<?php

namespace ExpectObjectToBeExactlyTest;


class ExpectObjectToBeExactlyTest extends \PHPUnit\Framework\TestCase
{
    use \Haijin\Testing\ObjectsExpectationsTrait;

    public function testAssertingEqualityOfConstantValues()
    {
        $object = [
            "name" => "Lisa",
            "last_name" => "Simpson"
        ];

        $this->expectObjectToBeExactly( $object, [
            "name" => "Lisa",
            "last_name" => "Simpson"
        ]);
    }

    public function testAssertingEqualityOfConstantValuesFails()
    {
        $object = [
            "name" => "Lisa",
            "last_name" => "Simpson"
        ];

        $this->assertion_failed = false;

        try{
            $this->expectObjectToBeExactly( $object, [
                "name" => "Lis",
                "last_name" => "Simpson"
            ]);            
        } catch( \PHPUnit\Framework\ExpectationFailedException $e ) {
            $this->assertEquals(
                "At 'name' expected 'Lis' but got 'Lisa'\nFailed asserting that two strings are equal.",
                $e->getMessage()
            );

            $this->assertion_failed = true;
        }

        $this->assertEquals( true, $this->assertion_failed );
    }

    public function testAssertingWithClosures()
    {
        $object = [
            "name" => "Lisa",
            "last_name" => "Simpson"
        ];

        $this->closure_calls_counter = 0;

        $this->expectObjectToBeExactly( $object, [
            "name" => function($value, $attribute_path) {
                $this->assertEquals( "Lisa", $value );
                $this->assertEquals( "name", $attribute_path);

                $this->closure_calls_counter += 1;
            },
            "last_name" => function($value, $attribute_path) {
                $this->assertEquals( "Simpson", $value );
                $this->assertEquals( "last_name", $attribute_path);

                $this->closure_calls_counter += 1;
            }
        ]);

        $this->assertEquals( 2, $this->closure_calls_counter );
    }

    public function testAssertingWithClosuresFails()
    {
        $object = [
            "name" => "Lis",
            "last_name" => "Simpson"
        ];

        $this->assertion_failed = false;

        try{
            $this->expectObjectToBeExactly( $object, [
                "name" => function($value) { $this->assertEquals( "Lisa", $value ); },
                "last_name" => function($value) { $this->assertEquals( "Simpson", $value ); }
            ]);            
        } catch( \PHPUnit\Framework\ExpectationFailedException $e ) {
            $this->assertEquals(
                "Failed asserting that two strings are equal.",
                $e->getMessage()
            );

            $this->assertion_failed = true;
        }

        $this->assertEquals( true, $this->assertion_failed );
    }

    public function testAssertingThroughArraysNestedAttributes()
    {
        $object = [
            "name" => "Lisa",
            "last_name" => "Simpson",
            "address" => [
                "street" => "Evergreen 742"
            ]
        ];

        $this->nested_attribute_was_asserterd = false;

        $this->expectObjectToBeExactly( $object, [
            "name" => "Lisa",
            "last_name" => "Simpson",
            "address" => [
                "street" => function($value, $attribute_path) { 
                    $this->assertEquals( "Evergreen 742", $value );
                    $this->assertEquals( "address.street", $attribute_path );

                    $this->nested_attribute_was_asserterd = true;
                }
            ]
        ]);

        $this->assertEquals( true, $this->nested_attribute_was_asserterd );
    }

    public function testAssertingThroughObjectsNestedAttributes()
    {
        $object = new \stdclass();
        $object->name = "Lisa";
        $object->last_name = "Simpson";
        $object->address = new \stdclass();
        $object->address->street = "Evergreen 742";

        $this->nested_attribute_was_asserterd = false;

        $this->expectObjectToBeExactly( $object, [
            "name" => "Lisa",
            "last_name" => "Simpson",
            "address" => [
                "street" => function($value, $attribute_path) { 
                    $this->assertEquals( "Evergreen 742", $value );
                    $this->assertEquals( "address.street", $attribute_path );

                    $this->nested_attribute_was_asserterd = true;
                }
            ]
        ]);

        $this->assertEquals( true, $this->nested_attribute_was_asserterd );
    }

    public function testAssertingThroughObjectsGetters()
    {
        $object = new ClassWithGetter();

        $this->expectObjectToBeExactly( $object, [
            "getter()" => 123,
        ]);
    }

    public function testMissingExpectedAttribute()
    {
        $object = [
            "last_name" => "Simpson"
        ];

        $this->assertion_failed = false;

        try{
            $this->expectObjectToBeExactly( $object, [
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]);            
        } catch( \PHPUnit\Framework\AssertionFailedError $e ) {
            $this->assertEquals(
                "The object was expected to have the attributes [{name}].",
                $e->getMessage()
            );

            $this->assertion_failed = true;
        }

        $this->assertEquals( true, $this->assertion_failed );
    }

    public function testNotExpectedAttributes()
    {
        $object = [
            "name" => "Lisa",
            "last_name" => "Simpson",
            "address" => "Evergreen 472",
            "phone" => ""
        ];

        $this->assertion_failed = false;

        try{
            $this->expectObjectToBeExactly( $object, [
                "name" => "Lisa",
                "last_name" => "Simpson"
            ]);            
        } catch( \PHPUnit\Framework\AssertionFailedError $e ) {
            $this->assertEquals(
                "The was expected not to have the attributes [{address, phone}].",
                $e->getMessage()
            );

            $this->assertion_failed = true;
        }

        $this->assertEquals( true, $this->assertion_failed );
    }
}

class ClassWithGetter
{
    public function getter()
    {
        return 123;
    }
}
