<?php

namespace Haijin\Testing;

/**
 * Add all the extended behaviours for TestCase.
 */
trait AllExpectationsTrait {
    use ExceptionsExpectationsTrait;
    use ObjectsExpectationsTrait;
    use FilesExpectationsTrait;
}