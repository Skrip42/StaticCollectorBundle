<?php
namespace Skrip42\Bundle\StaticCollectorBundle;

use Symfony\WebpackEncoreBundle\Asset\TagRenderer;

class StaticCollector
{
    private $tagRenderer;
    private $asset_collector = [];
    private $js_src_collector = [];
    private $css_src_collector = [];

    public function __construct(TagRenderer $tagRenderer)
    {
        $this->tagRenderer = $tagRenderer;
    }

    public function clear()
    {
        $this->asset_collector = [];
        $this->js_src_collector = [];
        $this->css_src_collector = [];
    }

    /**
     * Get array of compiled style tags
     */
    public function getStyleTags()
    {
        $styleTags = [];
        $this->css_src_collector = array_unique($this->css_src_collector);
        //collect simple static tags
        foreach ($this->css_src_collector as $styleUrl) {
            $styleTags[] = '<link rel="stylesheet" href="' . $styleUrl . '">';
        }
        //collect asset link tags
        foreach ($this->asset_collector as $asset) {
            $styleTags = array_merge(
                $styleTags,
                preg_split(
                    '~(?=<link)~',
                    $this->tagRenderer->renderWebpackLinkTags(...$asset)
                )
            );
        }
        $styleTags = array_filter($styleTags, function ($style) {
            return !empty($style);
        });
        return $styleTags;
    }

    /**
     * Get array of compiled script tags
     */
    public function getScriptTags()
    {
        $scriptTags = [];
        $this->js_src_collector = array_unique($this->js_src_collector);
        //collect simple script tags
        foreach ($this->js_src_collector as $scriptUrl) {
            $scriptTags[] = '<script type="application/javascript" src="'
                . $scriptUrl
                . '"></script>';
        }
        //collect asset script tags
        foreach ($this->asset_collector as $asset) {
            $scriptTags = array_merge(
                $scriptTags,
                preg_split(
                    '~(?=<script)~',
                    $this->tagRenderer->renderWebpackScriptTags(...$asset)
                )
            );
        }
        $scriptTags = array_filter($scriptTags, function ($script) {
            return !empty($script);
        });
        return $scriptTags;
    }

    /**
     * Add asset, script or style
     */
    public function addStatic(
        string $entryNameOrSource,
        string $packageName = '',
        string $entrypointName = '_default',
        $attributes = []
    ) {
        if (strpos($entryNameOrSource, '.js')) {
            $this->addScript($entryNameOrSource);
        } else if (strpos($entryNameOrSource, '.css')) {
            $this->addStyle($entryNameOrSource);
        } else {
            $this->addAsset(
                $entryNameOrSource,
                empty($packageName) ? null : $packageName,
                $entrypointName,
                empty($attributes) ? [] : (is_array($attributes) ? $attributes : explode(',', $attributes))
            );
        }
    }

    /**
     * Add script url to collector
     */
    private function addScript(
        string $scriptUrl
    ) {
        $this->js_src_collector[] = $scriptUrl;
    }

    /**
     * Add style url to collector
     */
    private function addStyle(
        string $scriptUrl
    ) {
        $this->js_src_collector[] = $scriptUrl;
    }

    /**
     * Add asset to collector (with unique control)
     */
    private function addAsset(
        string $entryName,
        string $packageName = null,
        string $entrypointName = '_default',
        array $attributes = []
    ) {
        $this->asset_collector[$this->getAssetHash(
            $entryName,
            $packageName,
            $entrypointName,
            $attributes
        )] = [
            $entryName,
            $packageName,
            $entrypointName,
            $attributes
        ];
    }

    private function getAssetHash(
        string $entryName,
        string $packageName = null,
        string $entrypointName = '_default',
        array $attributes = []
    ) {
        return serialize([$entryName, $packageName, $entrypointName, $attributes]);
    }
}
