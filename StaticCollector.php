<?php
namespace Skrip42\Bundle\StaticCollectorBundle;

use Symfony\WebpackEncoreBundle\Asset\TagRenderer;

class StaticCollector
{
    private $tagRenderer;
    private $staticCollection = [];

    public function __construct(TagRenderer $tagRenderer)
    {
        $this->tagRenderer = $tagRenderer;
    }

    public function clear($group)
    {
        if (empty($group)) {
            $this->staticCollection = [];
        }
        if (!empty($this->staticCollection[$group])) {
            $this->staticCollection[$group] = [];
        }
    }

    /**
     * Get array of compiled style tags
     */
    public function getStyleTags($group = 'default')
    {
        $entries = [];
        //get entries from group
        if (!empty($group)) {
            $entries = array_filter($this->staticCollection[$group], function ($e) {
                return $e['type'] == 'style' || $e['type'] == 'asset';
            });
        } else {
            foreach ($this->staticCollection as $group) {
                $entries = array_merge($entries, array_filter($group, function ($e) {
                    return $e['type'] == 'style' || $e['type'] == 'asset';
                }));
            }
        }

        //sort entries
        usort($entries, function ($e1, $e2) {
            return $e1['order'] <=> $e2['order'];
        });
        //compile entries to links
        $styleTags = [];
        foreach ($entries as $entry) {
            if ($entry['type'] == 'style') {
                $styleTags[] = '<link rel="stylesheet" href="' . $entry['url'] . '">';
            } else if ($entry['type'] == 'asset') {
                $styleTags = array_merge(
                    $styleTags,
                    preg_split(
                        '~(?=<link)~',
                        $this->tagRenderer->renderWebpackLinkTags(
                            $entry['entry'],
                            $entry['packageName'],
                            $entry['entrypointName'],
                            $entry['attributes']
                        )
                    )
                );
            }
        }
        //drop empty links
        $styleTags = array_filter($styleTags, function ($e) {
            return !empty($e);
        });
        //unique links
        $styleTags = array_unique($styleTags);

        return $styleTags;
    }

    /**
     * Get array of compiled script tags
     */
    public function getScriptTags($group = 'default')
    {
        $entries = [];
        //get entries from group
        if (!empty($group)) {
            $entries = array_filter($this->staticCollection[$group], function ($e) {
                return $e['type'] == 'script' || $e['type'] == 'asset';
            });
        } else {
            foreach ($this->staticCollection as $group) {
                $entries = array_merge($entries, array_filter($group, function ($e) {
                    return $e['type'] == 'script' || $e['type'] == 'asset';
                }));
            }
        }

        //sort entries
        usort($entries, function ($e1, $e2) {
            return $e1['order'] <=> $e2['order'];
        });
        //compile entries to scripts
        $scriptTags = [];
        foreach ($entries as $entry) {
            if ($entry['type'] == 'script') {
                $scriptTags[] = '<script type="application/javascript" src="' . $entry['url'] . '"></script>';
            } else if ($entry['type'] == 'asset') {
                $scriptTags = array_merge(
                    $scriptTags,
                    preg_split(
                        '~(?=<script)~',
                        $this->tagRenderer->renderWebpackScriptTags(
                            $entry['entry'],
                            $entry['packageName'],
                            $entry['entrypointName'],
                            $entry['attributes']
                        )
                    )
                );
            }
        }
        //drop empty scripts
        $scriptTags = array_filter($scriptTags, function ($e) {
            return !empty($e);
        });
        //unique scripts
        $scriptTags = array_unique($scriptTags);
        return $scriptTags;
    }

    /**
     * Add asset, script or style
     */
    public function addStatic(
        string $entryNameOrSource,
        string $group = 'default',
        int $order = 1000,
        string $packageName = '',
        string $entrypointName = '_default',
        $attributes = []
    ) {
        if (empty($this->staticCollection[$group])) {
            $this->staticCollection[$group] = [];
        }

        if (strpos($entryNameOrSource, '.js')) {
            $this->staticCollection[$group][] = [
                'url'   => $entryNameOrSource,
                'order' => $order,
                'type'  => 'script'
            ];
        } else if (strpos($entryNameOrSource, '.css')) {
            $this->staticCollection[$group][] = [
                'url'   => $entryNameOrSource,
                'order' => $order,
                'type'  => 'style'
            ];
        } else {
            $this->staticCollection[$group][] = [
                'entry'          => $entryNameOrSource,
                'packageName'    => empty($packageName) ? null : $packageName,
                'order'          => $order,
                'entrypointName' => $entrypointName,
                'attributes'     => empty($attributes) ? [] : (is_array($attributes) ? $attributes : explode(',', $attributes)),
                'type'           => 'asset'
            ];
        }
    }
}
