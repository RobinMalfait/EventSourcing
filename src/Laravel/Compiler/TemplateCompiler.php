<?php namespace EventSourcing\Laravel\Compiler;

class TemplateCompiler
{
    public function compile($template, $data)
    {
        foreach ($data as $key => $value) {
            $template = preg_replace("/\\$$key\\$/i", $value, $template);
        }

        return $template;
    }
}
