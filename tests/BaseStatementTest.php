<?php


namespace tests;


use mixrnd\DemoTemplate\Statement\Node\Condition;
use mixrnd\DemoTemplate\Statement\Node\Text;
use PHPUnit\Framework\TestCase;

abstract class BaseStatementTest extends TestCase
{
    protected function createCondition(string $variableName, array $ifBranch, array $elseBranch) : Condition
    {
        $condition = new Condition();
        $condition->variableName = $variableName;
        $condition->ifBranchStatements = $ifBranch;
        $condition->elseBranchStatements = $elseBranch;

        return $condition;
    }

    protected function createText(string $text) : Text
    {
        $t= new Text();
        $t->text = $text;

        return $t;
    }
}