<?php


namespace mixrnd\DemoTemplate\Statement;

use mixrnd\DemoTemplate\Statement\Node\AbstractNode;

class StatementInterpreter
{
    /**
     * @param AbstractNode[] $statements
     * @param array $variables
     * @return string
     */
    public function interpret(array $statements, array $variables) : string
    {
        $result = '';

        foreach ($statements as $statement) {
            $result .= $this->interpretStatement($statement, $variables);
        }

        return $result;
    }

    private function interpretStatement(AbstractNode $statement, array $variables) : string
    {
        return $statement->interpret($variables);
    }
}