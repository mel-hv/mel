<?php

namespace Mel\HttpClient;

use Http\Client\HttpClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\HttpClientDiscovery;

class Builder
{
    /**
     * The object that sends HTTP messages.
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * A HTTP client with all our plugins.
     *
     * @var PluginClient
     */
    protected $pluginClient;

    /**
     * @var Plugin[] Plugins used to build HttpPlug instance
     */
    protected $plugins = [];

    /**
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
    }

    /**
     * Get http client instance
     *
     * @return PluginClient
     */
    public function getHttpClient()
    {
        if (!$this->pluginClient) {
            $this->pluginClient = new PluginClient($this->httpClient, $this->plugins);
        }
        return $this->pluginClient;
    }

    /**
     * Add a new plugin to the end of the plugin chain.
     *
     * @param Plugin $plugin
     */
    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[] = $plugin;
        $this->pluginClient = null;
    }

    /**
     * Remove a plugin by its fully qualified class name
     *
     * @param string $className
     */
    public function removePlugin($className)
    {
        foreach ($this->plugins as $key => $plugin) {
            if ($plugin instanceof $className) {
                unset($this->plugins[$key]);
                $this->pluginClient = null;
            }
        }
    }
}