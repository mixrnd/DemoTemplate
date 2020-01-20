<?php


namespace mixrnd\DemoTemplate\Statement\Node;


class Condition  extends AbstractNode
{
    public $variableName;
    public $ifBranchStatements = [];
    public $elseBranchStatements = [];

    function interpret(array $variables) : string
    {
        return $variables[$this->variableName]?
            $this->interpretBranch($this->ifBranchStatements, $variables) :
            $this->interpretBranch($this->elseBranchStatements, $variables);
    }

    private function interpretBranch(array $branch, array $variables) : string
    {
        $result = '';
        foreach ($branch as $node) {
            $result .= $node->interpret($variables);
        }

        return $result;
    }
}