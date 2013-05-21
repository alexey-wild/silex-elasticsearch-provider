<?php
namespace Elasticsearch\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ElasticsearchServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['elasticsearch'] = $app->share(function () use ($app) {
            $options = $app['settings']['elasticsearch'];
            $servers = array('host' => 'localhost', 'port' => 9200);

            if (array_key_exists('servers', $options)) $servers = $options['servers'];

            return new \Elastica\Client($servers);
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registers
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {}
}
