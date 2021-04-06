<?php
namespace Skrip42\Bundle\StaticCollectorBundle\Twig;

use Skrip42\Bundle\StaticCollectorBundle\StaticCollector;
use Skrip42\Bundle\TwigPostprocessorBundle\PostprocessorInterface;

class StaticCollectorPostprocessor implements PostprocessorInterface
{
    private $staticCollector;

    public function __construct(StaticCollector $staticCollector)
    {
        $this->staticCollector = $staticCollector;
    }

    public function postProcess(
        string $content,
        string $name,
        array $context
    ): string {
        $content = preg_replace_callback(
            "~<!-- static collector (\w+) place -->~",
            function ($mathes) {
                if (empty($mathes[1])) {
                    return $mathes[0];
                }
                if ($mathes[1] == 'style') {
                    return $mathes[0]
                        . implode('', $this->staticCollector->getStyleTags());
                }
                if ($mathes[1] == 'script') {
                    return $mathes[0]
                        . implode('', $this->staticCollector->getScriptTags());
                }
                return $mathes[0];
            },
            $content
        );
        return $content;
    }
}
