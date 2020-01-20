<?php


namespace mixrnd\DemoTemplate\Statement\Node;


abstract class AbstractNode
{
    abstract function interpret(array $variables) : string;
}