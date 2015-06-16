<?php namespace EventSourcing\Test\Laravel\Compiler;

use EventSourcing\Laravel\Compiler\TemplateCompiler;
use PHPUnit_Framework_TestCase;

class TemplateCompilerTest extends PHPUnit_Framework_TestCase
{
    private $compiler;

    public function setUp()
    {
        $this->compiler = new TemplateCompiler();
    }

    /**
     * @test
     */
    public function it_can_replace_keys_with_according_values()
    {
        $template = 'This is a $variable$ in a template.';

        $this->assertEquals("This is a test in a template.", $this->compiler->compile($template, [
            'variable' => 'test'
        ]));
    }
}
