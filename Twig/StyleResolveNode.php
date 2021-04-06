<?php
namespace Skrip42\Bundle\StaticCollectorBundle\Twig;

use Twig\Node\Node;
use Twig\Compiler;

class StyleResolveNode extends Node
{
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->raw("echo \"<!-- static collector style place -->\";\n");
    }
}
