<?php


namespace tests;

use mixrnd\DemoTemplate\Statement\Node\Condition;
use mixrnd\DemoTemplate\Statement\Node\Text;
use mixrnd\DemoTemplate\Statement\StatementParser;

class StatementParserTest extends BaseStatementTest
{
    public function testOnlyTextNode()
    {
        $template = 'text node';
        $statements = $this->parsedStatements($template);
        $text = $this->createText($template);

        $this->assertCount(1, $statements);
        $this->assertEquals($text, $statements[0]);
    }

    public function testSimpleIf()
    {
        $template = '{if var1} body {/if}';

        $statements = $this->parsedStatements($template);

        $this->assertCount(1, $statements);
        $this->assertEquals([
            $this->createCondition('var1', [$this->createText(' body ')], [])],
            $statements
        );
    }

    public function testSimpleIfElse()
    {
        $template = '{if var1} if_body {else} else_body {/if}';

        $statements = $this->parsedStatements($template);

        $condition = $this->createCondition('var1', [$this->createText(' if_body ')], [$this->createText(' else_body ')]);

        $this->assertCount(1, $statements);
        $this->assertEquals([$condition], $statements);
    }

    public function testSimpleIfElseBoundedText()
    {
        $template = 'begin {if var1} if_body {else} else_body {/if} end';

        $statements = $this->parsedStatements($template);

        $condition = $this->createCondition('var1', [$this->createText(' if_body ')], [$this->createText(' else_body ')]);

        $this->assertCount(3, $statements);
        $this->assertEquals([
            $this->createText('begin '),
            $condition,
            $this->createText(' end')
        ], $statements);
    }

    public function testSimpleNestedIf()
    {
        $template =
            '{if var1}' .
                '{if var2}' .
                    'body_nested' .
                '{/if}' .
            '{/if}';

        $statements = $this->parsedStatements($template);

        $condition = $this->createCondition('var1', [
            $this->createCondition('var2', [$this->createText('body_nested')], []),
        ], []);

        $this->assertCount(1, $statements);
        $this->assertEquals([$condition,], $statements);
    }

    public function testSimpleNestedIfElse()
    {
        $template =
            '{if var1}' .
                '{if var2}' .
                    'body_nested' .
                '{else}'.
                    'body_else' .
                '{/if}'.
            '{/if}';

        $statements = $this->parsedStatements($template);
        $condition = $this->createCondition('var1', [
            $this->createCondition('var2', [$this->createText('body_nested')], [$this->createText('body_else')]),
        ], []);

        $this->assertCount(1, $statements);
        $this->assertEquals([$condition,], $statements);
    }

    public function testSimpleNestedIfElseBoundedText()
    {
        $template =
            '{if var1}' .
                'begin if' .
                '{if var2}' .
                    'body_nested' .
                '{else}'.
                    'body_else'.
                '{/if}' .
                'end if' .
            '{/if}';

        $statements = $this->parsedStatements($template);

        $condition = $this->createCondition('var1', [
            $this->createText('begin if'),
            $this->createCondition('var2', [$this->createText('body_nested')], [$this->createText('body_else')]),
            $this->createText('end if')
        ], []);

        $this->assertCount(1, $statements);
        $this->assertEquals([$condition,], $statements);
    }

    public function testSimpleIfSequence()
    {
        $template =
            'begin'.
            '{if var2}'.
                'body_nested1'.
            '{/if}'.
            'text'.
            '{if var3}'.
                'body_nested2'.
            '{/if}'.
            'end';

        $statements = $this->parsedStatements($template);

        $condition1 = $this->createCondition('var2', [
            $this->createText('body_nested1')
        ], []);

        $condition2 = $this->createCondition('var3', [
            $this->createText('body_nested2')
        ], []);

        $this->assertCount(5, $statements);
        $this->assertEquals([
            $this->createText('begin'),
            $condition1,
            $this->createText('text'),
            $condition2,
            $this->createText('end')
        ], $statements);
    }

    public function testSimpleNestedIfSequence()
    {
        $template =
            'begin'.
            '{if var1}'.
                'body_begin'.
                '{if var2}'.
                    'body_nested1'.
                '{/if}'.
                    'text'.
                '{if var3}'.
                    'body_nested2'.
                '{/if}'.
                'body_end'.
            '{/if}'.
            'end';

        $statements = $this->parsedStatements($template);

        $conditionNested1 = $this->createCondition('var2', [
            $this->createText('body_nested1')
        ], []);

        $conditionNested2 = $this->createCondition('var3', [
            $this->createText('body_nested2')
        ], []);

        $condition = $this->createCondition('var1', [
            $this->createText('body_begin'),
            $conditionNested1,
            $this->createText('text'),
            $conditionNested2,
            $this->createText('body_end')
        ], []);

        $this->assertCount(3, $statements);
        $this->assertEquals([
            $this->createText('begin'),
            $condition,
            $this->createText('end')
        ], $statements);
    }

    public function testNestedIfElse()
    {
        $template = 'begin {if var1} body_begin {else} else_begin {if var2} level21 {else} level22 {/if} else_end {/if} end';
        $statements = $this->parsedStatements($template);

        $conditionNested = $this->createCondition('var2',
            [$this->createText(' level21 ')],
            [$this->createText(' level22 ')]
        );

        $condition = $this->createCondition('var1',
            [$this->createText(' body_begin ')],
            [
                $this->createText(' else_begin '),
                $conditionNested,
                $this->createText(' else_end '),
            ]
        );

        $this->assertCount(3, $statements);
        $this->assertEquals([
            $this->createText('begin '),
            $condition,
            $this->createText(' end')
        ], $statements);
    }

    public function testFullTemplate()
    {
        $template = <<<EOT
<h1>Template title</h1>
some text
{if bv1}
    <T>level1</T>
    {if bv2} level2 {/if}
{else}
    level12
    {if bv3} level22 {else} level23 {/if}
    level123
{/if}
 <p>paragraph</p>

EOT;
        $statements = $this->parsedStatements($template);

        $textTitle = $this->createText('<h1>Template title</h1>
some text
');
        $textParagraph = $this->createText('
 <p>paragraph</p>
');
        $conditionBv2 = $this->createCondition('bv2', [$this->createText(' level2 ')], []);
        $conditionBv3 = $this->createCondition('bv3', [$this->createText(' level22 ')], [$this->createText(' level23 ')]);

        $conditionBv1 = $this->createCondition('bv1', [
            $this->createText('
    <T>level1</T>
    '),
            $conditionBv2,
            $this->createText('
')
        ], [
            $this->createText('
    level12
    '),
            $conditionBv3,
            $this->createText('
    level123
')
        ]);

        $this->assertEquals([$textTitle, $conditionBv1, $textParagraph], $statements);
    }

    private function parsedStatements($template) : array
    {
        $sp = new StatementParser();
        return $sp->parse($template);
    }


}