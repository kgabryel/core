<?php

namespace Frankie\Console\Validator;

use Frankie\Request\Request\RequestInterface;
use Rakit\Validation\Validation;

abstract class RequestValidator implements InputValidator
{
    protected array $input;

    public function __construct(RequestInterface $request)
    {
        $this->input = $request->getData()->getAllData()->toArray();
    }

    public function get(): Validation
    {
        return Validator::make(
            $this->input,
            $this->rules(),
            $this->messages(),
            $this->aliases(),
            $this->translations()
        );
    }

    abstract public function rules(): array;

    public function messages(): array
    {
        return [];
    }

    public function aliases(): array
    {
        return [];
    }

    public function translations(): array
    {
        return [];
    }
}
