<?php
namespace Skrip42\Bundle\StaticCollectorBundle\Twig;

use Skrip42\Bundle\StaticCollectorBundle\StaticCollector;
use Twig\Extension\AbstractExtension;

class StaticCollectorExtension extends AbstractExtension
{
    private $staticCollector;

    public function __construct(StaticCollector $staticCollector)
    {
        $this->staticCollector = $staticCollector;
    }

    public function getTokenParsers()
    {
        return [
            new StaticTokenParser($this->staticCollector),
            new ScriptPlaceTokenParser($this->staticCollector),
            new StylePlaceTokenParser($this->staticCollector)
        ];
    }
}
