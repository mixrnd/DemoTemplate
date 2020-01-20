<?php


namespace mixrnd\DemoTemplate;


use mixrnd\DemoTemplate\Statement\StatementInterpreter;
use mixrnd\DemoTemplate\Statement\StatementParser;

class DemoTemplate
{
    public function parse(string $template, array $variables) : string
    {
        $statementParser = new StatementParser();
        $statements = $statementParser->parse(
            $this->substituteVariables($template, $variables)
        );

        $statementInterpreter = new StatementInterpreter();

        return $statementInterpreter->interpret($statements, $variables);
    }

    private function substituteVariables(string $template, array $variables) : string
    {
        $result = $template;

        foreach ($variables as $name => $value) {
            $result = str_replace('{' . $name . '}', $value, $result);
        }

        return $result;
    }
}