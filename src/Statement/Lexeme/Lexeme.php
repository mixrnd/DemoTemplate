<?php


namespace mixrnd\DemoTemplate\Statement\Lexeme;


class Lexeme
{
    const TYPE_TEXT = 'text';
    const TYPE_IF = 'if';
    const TYPE_ELSE = 'else';
    const TYPE_END_IF = 'end_if';

    public $type;
    public $value;

    /**
     * Lexeme constructor.
     * @param $type
     * @param $value
     */
    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }


}