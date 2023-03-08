<?php

namespace Frankie\Console\Validator;

use Rakit\Validation\Validation;

interface InputValidator
{
    public function rules(): array;

    public function messages(): array;

    public function aliases(): array;

    public function translations(): array;

    public function get(): Validation;
}
