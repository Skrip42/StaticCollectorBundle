<?php
namespace Skrip42\Bundle\StaticCollectorBundle\Twig;

use Twig\Node\Node;
use Twig\Compiler;
use Skrip42\Bundle\StaticCollectorBundle\StaticCollector;

class StaticResolveNode extends Node
{
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->raw(
                "echo \"<!-- static collector place"
                . " type=" . $this->getAttribute('type')
            );
        if ($this->hasAttribute('group')) {
            $compiler->raw(" group=" . $this->getAttribute('group'));
        } else {
            $compiler->raw(" group=default");
        }
        $compiler->raw(" -->\";\n");
    }
}
