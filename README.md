# Haijin Testing

Extensions to PHPUnit to ease testing and improve tests expressiveness.

[![Latest Stable Version](https://poser.pugx.org/haijin/testing/version)](https://packagist.org/packages/haijin/testing)
[![Latest Unstable Version](https://poser.pugx.org/haijin/testing/v/unstable)](https://packagist.org/packages/haijin/testing)
[![Build Status](https://travis-ci.com/haijin-development/php-testing.svg?branch=v0.0.2)](https://travis-ci.com/haijin-development/php-testing)
[![License](https://poser.pugx.org/haijin/testing/license)](https://packagist.org/packages/haijin/testing)

### Version 0.0.2

This library is under active development and no stable version was released yet.

If you like it a lot you may contribute by [financing](https://github.com/haijin-development/support-haijin-development) its development.

## Table of contents

1. [Installation](#c-1)
2. [Usage](#c-2)
    1. [Add the trait to the test class](#c-2-1)
    2. [Expectations](#c-2-2)
        1. [expectExactExceptionRaised](#c-2-2-1)
        2. [expectObjectToBeLike](#c-2-2-2)
        3. [expectObjectToBeExactly](#c-2-2-3)
        4. [expectFileContents](#c-2-2-4)
3. [Running the tests](#c-3)

<a name="c-1"></a>
## Installation

Include this library in your project `composer.json` file:

```json
{
    ...

    "require-dev": {
        ...
        "haijin/testing": "^0.0.2",
        ...
    },

    ...
}
```
<a name="c-2"></a>
## Usage

<a name="c-2-1"></a>
### Add the trait to the test class

In the TestCase class add the following:

```php
class YourTest extends TestCase
{
    use \Haijin\Testing\AllExpectationsTrait;

    // ...
}
```

<a name="c-2-2"></a>
### Expectations

Use any of the following expectations:

<a name="c-2-2-1"></a>
#### expectExactExceptionRaised

Expects a tested closure to raise the exact  expected Exception class.

Example of use with no assertion closure on the exception:

```php
class YourTest extends TestCase
{
    use \Haijin\Testing\ExceptionsExpectationsTrait;

    function someTest() {
        // test code here ...

         // Expects the closure (second parameter) to raise an exception named SomeException.
        $this->expectExactExceptionRaised(
            'SomeException',
            function() {
                $this->some_object->do_something_that_may_raise_the_exception();
            }
        );

        // more test code here ...
    }
}
```

Example of use with an assertion closure on the exception:

```php
class YourTest extends TestCase
{
    use \Haijin\Testing\ExceptionsExpectationsTrait;

    function someTest() {
        // test code here ...

         // Expects the closure (second parameter) to raise an exception named SomeException.
        $this->expectExactExceptionRaised(
            'SomeException',
            function() {
                $this->some_object->do_something_that_may_raise_the_exception();
            },
            function($raised_exception) {
                $this->assertEquals( 123, $raised_exception->get_value() );
            }
        );

        // more test code here ...
    }
}
```

<a name="c-2-2-2"></a>
#### expectObjectToBeLike

Expects an object or dictionary to be like a given spec, recursively asserting on each attribute of the object.


The expectations can be constant values to assert equality with `assertEquals`:

```php
$this->expectObjectToBeLike( $object, [
    "name" => "Lisa",
    "last_name" => "Simpson",
    "address" => [
        "street" => "Evergreen 742"
    ]
]);
```

or closures to use any assertion:

```php
$this->expectObjectToBeLike( $object, [
    "name" => function($value) { $this->assertEquals( "Lisa", $value ); },
    "last_name" => "Simpson",
    "address" => [
        // the closure also accepts an optional parameter with the attribute path:
        "street" => function($value, $attribute_path) { $this->assertEquals( "Evergreen 742", $value ); }
    ]
]);
```

The accessors can be array attributes, object public properties or object public getter methods:

```php
$this->expectObjectToBeLike( $object, [
    "get_name()" => function($value) { $this->assertEquals( "Lisa", $value ); },
    "get_last_name()" => "Simpson",
    "get_address()" => [
        "street" => "Evergreen 742"
    ]
]);
```

<a name="c-2-2-3"></a>
#### expectObjectToBeExactly


Just like `expectObjectToBeLike` but if the validated object is an array with attributes not expected in the spec the assertion fails.

<a name="c-2-2-4"></a>
#### expectFileContents

Asserts that a file has the expected contents.

```php
class YourTest extends TestCase
{
    use \Haijin\Testing\FilesExpectationsTrait;

    function someTest() {
        // test code here ...

        $this->expectFileContents(
            'File contents',
            $file_path
        );

        // more test code here ...
    }
}
```

or using a closure

```php
class YourTest extends TestCase
{
    use \Haijin\Testing\FilesExpectationsTrait;

    function someTest() {
        // test code here ...

        $this->expectFileContents(
            function($file_contents) {
                $this->assertEquals( "File contents", $file_contents );
            },
            $file_path
        );

        // more test code here ...
    }
}
```

<a name="c-3"></a>
## Running the tests

```
composer test
```