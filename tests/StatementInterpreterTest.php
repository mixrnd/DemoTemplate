<?php


namespace tests;


use mixrnd\DemoTemplate\Statement\StatementInterpreter;

class StatementInterpreterTest extends BaseStatementTest
{
    public function testText()
    {
        //text;
        $text = $this->createText('text');

        $this->assertEquals('text', $this->compilerResult([$text], []));
    }

    public function testIfElse()
    {
        //{if var1} if_body {else} else_body {/if};
        $condition = $this->createCondition('var1', [$this->createText(' if_body ')], [$this->createText(' else_body ')]);

        $this->assertEquals(' if_body ', $this->compilerResult([$condition], ['var1' => true]));
        $this->assertEquals(' else_body ', $this->compilerResult([$condition], ['var1' => false]));
    }

    public function testNestedIf()
    {
        //{if var1} if_body {if var2} nested_if_body {else} nested_else_body {/if} end_if_body {else} else_body {/if};
        $condition = $this->createCondition('var1', [
            $this->createText(' if_body '),
            $this->createCondition('var2', [
                $this->createText(' nested_if_body ')
            ], [
                $this->createText(' nested_else_body ')
            ]),
            $this->createText(' end_if_body ')
        ], [
            $this->createText(' else_body ')
        ]);

        $this->assertEquals(' if_body  nested_if_body  end_if_body ', $this->compilerResult([$condition], ['var1' => true, 'var2' => true]));
        $this->assertEquals(' if_body  nested_else_body  end_if_body ', $this->compilerResult([$condition], ['var1' => true, 'var2' => false]));
        $this->assertEquals(' else_body ', $this->compilerResult([$condition], ['var1' => false, 'var2' => false]));
        $this->assertEquals(' else_body ', $this->compilerResult([$condition], ['var1' => false, 'var2' => true]));
    }

    private function compilerResult(array $statements, array $variables) : string
    {
        $sc = new StatementInterpreter();
        return $sc->interpret($statements, $variables);
    }
}