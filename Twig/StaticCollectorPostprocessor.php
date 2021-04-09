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
            "~<!-- static collector place type=(\w+) group=(\w+) -->~",
            function ($mathes) {
                if (empty($mathes[1]) || empty($mathes[2])) {
                    return $mathes[0];
                }
                if ($mathes[1] == 'style') {
                    return $mathes[0] . "\n"
                        . implode("\n", $this->staticCollector->getStyleTags($mathes[2]));
                }
                if ($mathes[1] == 'script') {
                    return $mathes[0] . "\n"
                        . implode("\n", $this->staticCollector->getScriptTags($mathes[2]));
                }
                if ($mathes[1] == 'all') {
                    return $mathes[0] . "\n"
                        . implode("\n", $this->staticCollector->getStyleTags($mathes[2])) . "\n"
                        . implode("\n", $this->staticCollector->getScriptTags($mathes[2]));
                }
                return $mathes[0];
            },
            $content
        );
        return $content;
    }
}
