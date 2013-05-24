<?php
namespace Elasticsearchphp\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ElasticsearchphpServiceProvider implements ServiceProviderInterface
{
    public $elasticsearchphp;
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
        $options = $app['settings']['elasticsearchphp'];
        $app['elasticsearchphp'] = $app->share(function ($options) use ($app) {
            $settings['cluster.autodetect'] = false; // true, false
            $instances = array('host' => 'localhost', 'port' => 9200);

            if (array_key_exists('cluster', $options)) {
                if (array_key_exists('instance', $options['cluster']) && is_array($options['cluster']['instance'])) $instances = $options['cluster']['instances'];
                if (array_key_exists('autodetect', $options['cluster']) && in_array($options['cluster']['autodetect'], array(true, false))) $settings['cluster.autodetect'] = $options['cluster']['autodetect'];
            }

            $this->elasticsearchphp = new \Elasticsearchphp\Elasticsearchphp($settings);

            if (is_array($instances)) {
                if (array_key_exists('host', $instances))  {
                    $this->elasticsearchphp->addNode($instances['host'], $instances['port']);
                } else {
                    foreach ($instances as $instance) {
                        if (array_key_exists('host', $instance) && is_string($instance['host'])
                            && array_key_exists('port', $instance) && is_int($instance['port']))
                        $this->elasticsearchphp->addNode($instance['host'], $instance['port']);
                    }
                }
            }

            return $this->elasticsearchphp;
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