<?php

namespace Haijin\Testing;

/**
 * Private class to read an attribute from an object.
 * The object can be an array or an object.
 * The attribute can be an array key, an object public property or an object public getter function.
 */
class AttributeReader {

    static public $missing_attribute;

    static public function read_attribute($object, $attribute)
    {
        return ( new self( $object, $attribute ) )->read();
    }

    public function __construct($object, $attribute)
    {
        $this->object = $object;
        $this->attribute = $attribute;
    }

    public function read()
    {
        if( ! $this->has_attribute_defined() ) {
            return self::$missing_attribute;
        }

        return $this->read_value();
   }

    public function has_attribute_defined()
    {
        if( $this->reading_array_attribute() ) {
            return array_key_exists( $this->attribute, $this->object );
        }

        if( $this->reading_object_property() ) {
            return property_exists( $this->object, $this->attribute );
        }

        if( $this->reading_object_getter() ) {
            return method_exists( $this->object, substr( $this->attribute, 0, -2 ) );
        }

        return false;
    }

    protected function read_value()
    {
        if( $this->reading_array_attribute() ) {
            return $this->object[ $this->attribute ];
        }

        if( $this->reading_object_property() ) {
            $property = $this->attribute;

            return $this->object->$property;
        }

        if( $this->reading_object_getter() ) {
            $getter = substr( $this->attribute, 0, -2 );

            return $this->object->$getter();
        }

        throw new \Exception();
    }

    protected function reading_array_attribute()
    {
        return is_array( $this->object );
    }

    protected function reading_object_property()
    {
        return is_object( $this->object ) && ! $this->reading_object_getter();
    }

    protected function reading_object_getter()
    {
        return is_object( $this->object ) && substr( $this->attribute, -2 ) == "()";
    }
}

// Create a unique object to use its identity to flag a missing attribute object when returning the attribute value.
AttributeReader::$missing_attribute = new \stdclass();