<?php


namespace mixrnd\DemoTemplate\Statement\Lexeme;


class LexemeParser
{
    private $template;

    /**
     * @var int
     */
    private $lexemeIndex;

    private $prepared;

    /**
     * LexemeParser constructor.
     * @param $template
     */
    public function __construct($template)
    {
        $this->template = $template;
        preg_match_all('/({if\s\w*})|({else\})|(\{\/if\})/', $template, $lexemsInOrder,
            PREG_OFFSET_CAPTURE);

        $this->lexemeIndex = 0;
        $this->prepared = $this->prepare($lexemsInOrder[0]);
    }

    public function hasLexemes() : bool
    {
        return $this->lexemeIndex < count($this->prepared);
    }

    public function nextLexeme() : ?Lexeme
    {
        return $this->prepared[$this->lexemeIndex++];
    }

    public function putLexemeBack() : void
    {
        $this->lexemeIndex--;
    }

    private function prepare(array $statementsInOrder) : array
    {
        if (!$statementsInOrder) {
            return [new Lexeme(Lexeme::TYPE_TEXT, $this->template)];
        }

        $prepared = [];

        if ($statementsInOrder[0][1] !== 0) {
            $prepared[] = $this->createTextLexeme(0, $statementsInOrder[0][1]);
        }

        for ($i = 0; $i < count($statementsInOrder); $i++) {
            $prepared[] = $this->createLexemeByStatement($statementsInOrder[$i][0]);

            if ($i + 1 < count($statementsInOrder)) {
                if ($statementsInOrder[$i][1] + 1 == $statementsInOrder[$i + 1][1]) {
                    continue;
                }

                $tl = $this->createTextLexeme($this->endPos($statementsInOrder[$i]), $statementsInOrder[$i + 1][1] - $this->endPos($statementsInOrder[$i]));
                if ($tl) {
                    $prepared[] = $tl;
                }
            } else {
                if ($this->endPos($statementsInOrder[$i]) < strlen($this->template)) {
                    $tl = $this->createTextLexeme($this->endPos($statementsInOrder[$i]), strlen($this->template) - $this->endPos($statementsInOrder[$i]));
                    if ($tl) {
                        $prepared[] = $tl;
                    }
                }
            }
        }

        return $prepared;
    }

    private function createLexemeByStatement(string $statement)
    {
        if ($this->isIf($statement)) {
            return new Lexeme(Lexeme::TYPE_IF, $statement);
        }

        if ($this->isElse($statement)) {
            return new Lexeme(Lexeme::TYPE_ELSE, $statement);
        }

        if ($this->isEndIf($statement)) {
            return new Lexeme(Lexeme::TYPE_END_IF, $statement);
        }
    }

    private function createTextLexeme($start, $length) : ?Lexeme
    {
        $str = substr($this->template,$start, $length);
        if (!$str) {
            return null;
        }

        return new Lexeme(Lexeme::TYPE_TEXT, $str);
    }

    private function isIf(string $str) : bool
    {
        return strpos($str, '{if') !== false;
    }

    private function isEndIf(string $str) : bool
    {
        return strpos($str, '{/if}') !== false;
    }

    private function isElse(string $str) : bool
    {
        return strpos($str, '{else}') !== false;
    }

    private function endPos($item) : int
    {
        return $item[1] + strlen($item[0]);
    }

}