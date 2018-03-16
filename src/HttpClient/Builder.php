<?php

namespace Mel\HttpClient;

use Mel\Mel;
use Mel\Http\UriGenerator;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Mel\HttpClient\Plugins\Authentication;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;

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
     * Create default Builder instance used in Mel
     * This Builder contains default plugins list
     *
     * @param Mel             $mel
     * @param HttpClient|null $httpClient
     *
     * @return Builder
     */
    public static function create(Mel $mel, HttpClient $httpClient = null)
    {
        $builder = new self($httpClient);

        // Base uri Plugin
        $uriGenerator = new UriGenerator($mel);
        $baseUri = new Plugin\BaseUriPlugin($uriGenerator->createUri(UriGenerator::API_URI), [
            'replace' => true,
        ]);
        $builder->addPlugin($baseUri);


        // Authentication Plugin
        $authentication = new Authentication($mel);
        $builder->addPlugin(new Plugin\AuthenticationPlugin($authentication));


        // Headers plugin
        $builder->addPlugin(new HeaderDefaultsPlugin([
            'User-Agent'   => sprintf('%1$s - %2$s', 'MEL', Mel::VERSION),
            'Content-Type' => 'application/json',
        ]));

        return $builder;
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