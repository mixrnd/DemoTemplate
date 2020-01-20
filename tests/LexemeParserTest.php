<?php


namespace tests;


use mixrnd\DemoTemplate\Statement\Lexeme\Lexeme;
use mixrnd\DemoTemplate\Statement\Lexeme\LexemeParser;
use PHPUnit\Framework\TestCase;

class LexemeParserTest extends TestCase
{
    public function testText()
    {
        $template = 'text node';

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, $template),
            $this->createLexemeParser($template)->nextLexeme()
        );

    }

    public function testSimpleIf()
    {
        $template = '{if var1} body {/if}';

        $parser = $this->createLexemeParser($template);

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_IF, '{if var1}'),
            $parser->nextLexeme()
        );
        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, ' body '),
            $parser->nextLexeme()
        );
        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_END_IF, '{/if}'),
            $parser->nextLexeme()
        );
        $this->assertFalse($parser->hasLexemes());
    }

    public function testLongTemplate()
    {
        $template = 'text {if var1} body {/if} m {if var1} b2 {if var1} b2 {else} b3 {/if} b4 {else} b5 {/if} b6';

        $parser = $this->createLexemeParser($template);

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, 'text '),
            $parser->nextLexeme()
        );

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_IF, '{if var1}'),
            $parser->nextLexeme()
        );
        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, ' body '),
            $parser->nextLexeme()
        );
        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_END_IF, '{/if}'),
            $parser->nextLexeme()
        );

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, ' m '),
            $parser->nextLexeme()
        );

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_IF, '{if var1}'),
            $parser->nextLexeme()
        );
        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, ' b2 '),
            $parser->nextLexeme()
        );

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_IF, '{if var1}'),
            $parser->nextLexeme()
        );
        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, ' b2 '),
            $parser->nextLexeme()
        );

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_ELSE, '{else}'),
            $parser->nextLexeme()
        );
        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, ' b3 '),
            $parser->nextLexeme()
        );

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_END_IF, '{/if}'),
            $parser->nextLexeme()
        );

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, ' b4 '),
            $parser->nextLexeme()
        );

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_ELSE, '{else}'),
            $parser->nextLexeme()
        );
        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, ' b5 '),
            $parser->nextLexeme()
        );

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_END_IF, '{/if}'),
            $parser->nextLexeme()
        );

        $this->assertEquals(
            new Lexeme(Lexeme::TYPE_TEXT, ' b6'),
            $parser->nextLexeme()
        );

        $this->assertFalse($parser->hasLexemes());
    }

    public function createLexemeParser($template) : LexemeParser
    {
        return new LexemeParser($template);
    }
}