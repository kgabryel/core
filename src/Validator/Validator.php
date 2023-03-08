<?php

namespace Frankie\Console\Validator;

use Frankie\Core\App;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator as Val;

class Validator
{
    public static function make(
        array $input,
        array $rules,
        array $messages = [],
        array $aliases = [],
        array $translations = []
    ): Validation {
        $validator = new Val();
        $validator->allowRuleOverride(true);
        if ($translations !== []) {
            $validator->setTranslations($translations);
        }
        if ($messages !== []) {
            $validator->setMessages($messages);
        }
        $container = App::get()->getDIContainer();
        foreach (App::get()->getRulesProvider()->get() as $key => $val) {
            if (!$container->hasKey($val)) {
                $container->setNewObject($val);
            }
            $validator->addValidator($key, $container->get($val));
        }
        $validation = $validator->make($input, $rules);
        $validation->setAliases($aliases);

        return $validation;
    }
}
