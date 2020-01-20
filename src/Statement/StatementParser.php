<?php


namespace mixrnd\DemoTemplate\Statement;


use mixrnd\DemoTemplate\Statement\Lexeme\Lexeme;
use mixrnd\DemoTemplate\Statement\Lexeme\LexemeParser;
use mixrnd\DemoTemplate\Statement\Node\AbstractNode;
use mixrnd\DemoTemplate\Statement\Node\Condition;
use mixrnd\DemoTemplate\Statement\Node\Text;

class StatementParser
{
    /**
     * @var LexemeParser
     */
    private $lexemeParser;

    /**
     * @param string $template
     * @return AbstractNode[] array
     */
    public function parse(string $template): array
    {
        $this->lexemeParser = new LexemeParser($template);

        return $this->parse0();
    }

    private function parse0() : array
    {
        /** @var AbstractNode[] $result */
        $result = [];
        while ($this->lexemeParser->hasLexemes()) {

            $currentLexeme = $this->lexemeParser->nextLexeme();

            switch ($currentLexeme->type) {
                case Lexeme::TYPE_TEXT:
                    $text = new Text();
                    $text->text = $currentLexeme->value;
                    $result[] = $text;
                    break;
                case Lexeme::TYPE_IF:
                    $result[] = $this->parseIf($currentLexeme);
                    break;
                case Lexeme::TYPE_ELSE:
                case Lexeme::TYPE_END_IF:
                    $this->lexemeParser->putLexemeBack();
                    return $result;
            }
        }

        return $result;
    }

    private function parseIf(Lexeme $lexeme) : ?Condition
    {
        if ($lexeme->type === Lexeme::TYPE_IF) {

            $condition = $this->createCondition($lexeme->value);

            $condition->ifBranchStatements = $this->parse0();

            $currentLexeme = $this->lexemeParser->nextLexeme();

            if ($currentLexeme->type === Lexeme::TYPE_ELSE) {
                $condition->elseBranchStatements = $this->parse0();
                $currentLexeme = $this->lexemeParser->nextLexeme();
            }

            if ($currentLexeme->type === Lexeme::TYPE_END_IF) {
                return $condition;
            }
        }

        return null;
    }

    private function createCondition($text) : Condition
    {
        preg_match('/{if\s(\w*)}/', $text, $varNameMatches);

        $condition = new Condition();
        $condition->variableName = $varNameMatches[1];

        return $condition;
    }
}