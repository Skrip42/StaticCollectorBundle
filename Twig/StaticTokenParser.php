<?php
namespace Skrip42\Bundle\StaticCollectorBundle\Twig;

use Twig\TokenParser\AbstractTokenParser;
use Twig\Node\Node;
use Twig\Token;

class StaticTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $attributes = [];
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $attributes['entryName'] = $expr->getAttribute('value');
        $stream = $this->parser->getStream();
        while ($stream->getCurrent()->getType() != /* Token::BLOCK_END_TYPE */3) {
            $name = $stream->getCurrent()->getValue();
            $stream->next();
            $value = $stream->getCurrent()->getValue();
            $stream->next();
            $attributes[$name] = $value;
        }
        $stream->expect(/* Token::BLOCK_END_TYPE */ 3);
        return new DefineStaticNode(
            $attributes,
            $token->getLine(),
            $this->getTag()
        );
    }

    public function getTag(): string
    {
        return 'static';
    }
}
