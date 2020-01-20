<?php


namespace mixrnd\DemoTemplate\Statement\Node;


class Text extends AbstractNode
{
    public $text;

    function interpret(array $variables) : string
    {
        return $this->text;
    }
}