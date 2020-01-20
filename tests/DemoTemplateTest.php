<?php


namespace tests;


use PHPUnit\Framework\TestCase;
use \mixrnd\DemoTemplate\DemoTemplate;

class DemoTemplateTest extends TestCase
{
    public function testEmptyTemplate()
    {
        $this->assertEquals('', $this->parserResult('', []));
    }

    public function testNoVariablesAndIfs()
    {
        $this->assertEquals('<div>my_var</div>', $this->parserResult('<div>my_var</div>', []));
    }

    public function testOneVariable()
    {
        $this->assertEquals('<div>test</div>', $this->parserResult('<div>{my_var}</div>', ['my_var' => 'test']));
    }

    public function testManyVariable()
    {
        $template = '<div>{my_var}</div>{you_var} <p>{p_var}</p>';
        $variables = ['my_var' => 'test', 'you_var' => 'test2', 'p_var' => 'test3'];

        $this->assertEquals('<div>test</div>test2 <p>test3</p>', $this->parserResult($template, $variables));
    }

    public function testIfElse()
    {
        $template =
            '<div>{my_var}</div>'.
            '{if bool_var}'.
                'True'.
            '{else}'.
                'False'.
            '{/if}';

        $variables = ['my_var' => 'test', 'bool_var' => true ];

        $this->assertEquals('<div>test</div>True', $this->parserResult($template, $variables));
    }

    public function testIfElseNested()
    {
        $template =
            '<div>{my_var}</div>'.
            '{if bool_var}'.
                'True'.
                '{if bool_var2}'.
                    'True'.
                '{else}'.
                    'False'.
                '{/if}'.
            '{else}'.
                'False'.
            '{/if}';

        $variables = ['my_var' => 'test', 'bool_var' => true, 'bool_var2' => false];

        $this->assertEquals('<div>test</div>TrueFalse', $this->parserResult($template, $variables));
        $variables = ['my_var' => 'test', 'bool_var' => true, 'bool_var2' => true];

        $this->assertEquals('<div>test</div>TrueTrue', $this->parserResult($template, $variables));
    }

    private function parserResult(string $template, array $variables) : string
    {
        $parser = new DemoTemplate();
        return $parser->parse($template, $variables);
    }
}