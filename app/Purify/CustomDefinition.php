<?php

namespace App\Purify;

use Stevebauman\Purify\Definitions\Html5Definition;

class CustomDefinition extends Html5Definition
{
    public static function apply($definition)
    {
        parent::apply($definition);

        $definition->addAttribute('li', 'data-list', 'Text');
    }
}
