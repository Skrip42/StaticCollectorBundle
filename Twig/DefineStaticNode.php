<?php
namespace Skrip42\Bundle\StaticCollectorBundle\Twig;

use Twig\Node\Node;
use Twig\Compiler;
use Skrip42\Bundle\StaticCollectorBundle\StaticCollector;

class DefineStaticNode extends Node
{
    // public function __construct(
    //     array $attributes,
    //     $line,
    //     $tag = null
    // ) {
    //     parent::__construct([], $attributes, $line, $tag);
    // }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->raw(
                "echo \"<!-- static collector call static "
                . $this->getAttribute('entryName')
                . " -->\";\n"
            )
            ->write(
                '$this->env->getRuntime(\''
                . StaticCollector::class
                . '\')->addStatic("'
                . $this->getAttribute('entryName')
                . '"'
            );
        if ($this->hasAttribute('group')) {
            $compiler->write(',"' . $this->getAttribute('group') . '"');
        } else {
            $compiler->write(',"default"');
        }
        if ($this->hasAttribute('order')) {
            $compiler->write(',"' . $this->getAttribute('order') . '"');
        } else {
            $compiler->write(",1000");
        }
        if ($this->hasAttribute('package')) {
            $compiler->write(',"' . $this->getAttribute('package') . '"');
        } else {
            $compiler->write(",''");
        }
        if ($this->hasAttribute('entrypointName')) {
            $compiler->write(",'" . $this->getAttribute('entrypointName') . "'");
        } else {
            $compiler->write(",'_default'");
        }
        if ($this->hasAttribute('attributes')) {
            $compiler->write(',')
                ->write(json_encode($this->getAttribute('attributes')));
        } else {
            $compiler->write(',[]');
        }
        $compiler->write(')');
        $compiler->raw(";\n");
    }
}
