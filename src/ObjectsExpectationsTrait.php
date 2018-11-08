<?php

namespace Haijin\Testing;

/**
 * Trait to extend TestCase with expectations on objects.
 *
 * Example of use:
 *
 *      class YourTest extends TestCase
 *      {
 *          use \Haijin\Testing\ObjectsExpectationsTrait;
 *
 *          // your test code here ...
 *      }
 */
trait ObjectsExpectationsTrait{
    /**
     * Expects a nested object or dictionary to be like a given spec, recursively asserting on each attribute of the object.
     *
     * The expectations can be constant values:
     *
     *      $this->expectObjectToBeLike( $object, [
     *                  "name" => "Lisa",
     *                  "last_name" => "Simpson",
     *                  "address" => [
     *                      "street" => "Evergreen 742"
     *                  ]
     *              ]);
     *
     * or closures:
     *
     *              $this->expectObjectToBeLike( $object, [
     *                  "name" => function($value) { $this->assertEquals( "Lisa", $value ); },
     *                  "last_name" => "Simpson",
     *                  "address" => [
     *                      // the closure also accepts an optional parameter with the attribute path:
     *                      "street" => function($value, $attribute_path) { $this->assertEquals( "Evergreen 742", $value ); }
     *                  ]
     *              ]);
     *
     * @param object $object The object being tested
     * @param array $expected_object An associative array with a expectation defined for each object attribute.
     *      Expected attributes can be nested with any depth.
     * @param string $attribute_path Private - The initial attribute path of the object being tested from the root object.
     */
    public function expectObjectToBeLike($object, $expected_object, $attribute_path = "")
    {
        // If $expected_object is a callable call it
        if( is_callable( $expected_object ) ) {
            $expected_object->call( $this, $object, $attribute_path );

            return;
        }

        // If $expected_object is a constant assert for equality
        if( ! is_array( $expected_object ) && ! is_object( $expected_object ) ) {

            if( is_string( $expected_object ) || is_numeric( $expected_object ) || is_bool( $expected_object ) ) {
                $message = "At '{$attribute_path}' expected '{$expected_object}' but got '{$object}'";
            } else {
                $message = "Assertion failed at '{$attribute_path}'";
            }

            $this->assertEquals( $expected_object, $object, $message );

            return;
        }

        // Othewise iterate $expected_object attributes
        foreach( $expected_object as $expected_key => $expected_value) {

            if( empty( $attribute_path ) )
                $child_attribute_path = $expected_key;
            else
                $child_attribute_path = $attribute_path . "." . $expected_key;


            $child_value = AttributeReader::read_attribute( $object, $expected_key );

            if( $child_value === AttributeReader::$missing_attribute ) {
                $this->fail( "The object was expected to have an attribute defined at '{$child_attribute_path}'." );
                return;
            }

            $this->expectObjectToBeLike( $child_value, $expected_value, $child_attribute_path );
        }
    }

    /**
     * Expects a nested object or dictionary to be exactly like a given spec, recursively asserting
     * on each attribute of the object.
     *
     * The expectations can be constant values:
     *
     *      $this->expectObjectToBeLike( $object, [
     *                  "name" => "Lisa",
     *                  "last_name" => "Simpson",
     *                  "address" => [
     *                      "street" => "Evergreen 742"
     *                  ]
     *              ]);
     *
     * or closures:
     *
     *              $this->expectObjectToBeLike( $object, [
     *                  "name" => function($value) { $this->assertEquals( "Lisa", $value ); },
     *                  "last_name" => "Simpson",
     *                  "address" => [
     *                      // the closure also accepts an optional parameter with the attribute path:
     *                      "street" => function($value, $attribute_path) { $this->assertEquals( "Evergreen 742", $value ); }
     *                  ]
     *              ]);
     *
     * @param object $object The object being tested
     * @param array $expected_object An associative array with a expectation defined for each object attribute.
     *      Expected attributes can be nested with any depth.
     * @param string $attribute_path Private - The initial attribute path of the object being tested from the root object.
     */
    public function expectObjectToBeExactly($object, $expected_object, $attribute_path = "")
    {
        // If $expected_object is a callable call it
        if( is_callable( $expected_object ) ) {
            $expected_object->call( $this, $object, $attribute_path );

            return;
        }

        // If $expected_object is a constant assert for equality
        if( ! is_array( $expected_object ) && ! is_object( $expected_object ) ) {

            if( is_string( $expected_object ) || is_numeric( $expected_object ) || is_bool( $expected_object ) ) {
                $message = "At '{$attribute_path}' expected '{$expected_object}' but got '{$object}'";
            } else {
                $message = "Assertion failed at '{$attribute_path}'";
            }

            $this->assertEquals( $expected_object, $object, $message );

            return;
        }

        // Othewise iterate $expected_object attributes

        if( is_array( $object ) ) {
            $expected_keys = array_keys( $expected_object );
            $actual_keys = array_keys( $object );

            $missing_keys = array_diff( $expected_keys, $actual_keys );
            if( count( $missing_keys ) > 0 ) {
                $this->fail(
                    "The object was expected to have the attributes [{" . join(", ", $missing_keys) . "}]." 
                );
                return;
            }

            $exceding_keys = array_diff( $actual_keys, $expected_keys );
            if( count( $exceding_keys ) > 0 ) {
                $this->fail(
                    "The was expected not to have the attributes [{" . join(", ", $exceding_keys) . "}]." 
                );
                return;
            }
        }

        foreach( $expected_object as $expected_key => $expected_value) {

            if( empty( $attribute_path ) )
                $child_attribute_path = $expected_key;
            else
                $child_attribute_path = $attribute_path . "." . $expected_key;

            $child_value = AttributeReader::read_attribute( $object, $expected_key );

            if( $child_value === AttributeReader::$missing_attribute ) {
                $this->fail( "The object was expected to have an attribute defined at '{$child_attribute_path}'." );
                return;
            }

            $this->expectObjectToBeLike( $child_value, $expected_value, $child_attribute_path );
        }
    }
}