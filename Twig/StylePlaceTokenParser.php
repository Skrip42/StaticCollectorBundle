<?php
namespace Skrip42\Bundle\StaticCollectorBundle\Twig;

use Twig\TokenParser\AbstractTokenParser;
use Twig\Node\Node;
use Twig\Token;

class StylePlaceTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();
        $stream->expect(/* Token::BLOCK_END_TYPE */ 3);
        return new StyleResolveNode(
            [],
            [],
            $token->getLine(),
            $this->getTag()
        );
    }

    public function getTag(): string
    {
        return 'style_place';
    }
}
