<?php

namespace Frankie\Core;

class Exceptions
{
    public const UNDEFINED_KEY = 'Undefined key: %s.';
    public const NO_PARSERS = 'No registered parsers, the get method of the %s class returns an empty array. Add a minimum one parser.';
    public const INVALID_AFTER_ACTION = "%s isn't instance of %s.";
    public const BOOTSTRAP_ERROR = 'Error occurred during execution %s.';
}
