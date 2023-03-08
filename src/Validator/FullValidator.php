<?php

declare(strict_types=1);

namespace Frankie\Console\Validator;

use Frankie\Request\Request\RequestInterface;
use Rakit\Validation\Validation;

abstract class FullValidator implements InputValidator
{
    protected array $input;

    public function __construct(RequestInterface $request)
    {
        $this->input = $request->getData()->getAllData()->toArray();
        $this->input = array_merge($this->input, $_FILES);
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
