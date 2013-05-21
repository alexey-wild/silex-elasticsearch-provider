<?php
namespace Elasticsearch\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ElasticsearchServiceProvider implements ServiceProviderInterface
{
    public $sherlock;
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
        $options = $app['settings']['elasticsearch'];
        $app['elasticsearch'] = $app->share(function ($options) use ($app) {
            $settings['mode'] = 'development'; // development, production
            $settings['log.enabled'] = true; // true, false
            $settings['log.level'] = 'error'; // 'debug', 'info', 'notice', 'warning', 'error', 'alert', 'urgent'
//            $settings['log.file'] = 'vendor/sherlock/sherlock/src/sherlock.log';
            $settings['cluster.autodetect'] = false; // true, false
            $instances = array('host' => 'localhost', 'port' => 9200);

            if (array_key_exists('mode', $options) && in_array($options['mode'], array('development', 'production'))) $settings['mode'] = $options['mode'];
            if (array_key_exists('log', $options)) {
                if (array_key_exists('enabled', $options['log']) && in_array($options['log']['enabled'], array(true, false))) $settings['log.enabled'] = $options['log']['enabled'];
                if (array_key_exists('level', $options['log']) && in_array($options['log']['level'], array('debug', 'info', 'notice', 'warning', 'error', 'alert', 'urgent'))) $settings['log.level'] = $options['log']['level'];
                if (array_key_exists('file', $options['log']) && is_string($options['log']['file'])) $settings['log.file'] = $options['log']['file'];
            }

            if (array_key_exists('cluster', $options)) {
                if (array_key_exists('instance', $options['cluster']) && is_array($options['cluster']['instance'])) $instances = $options['cluster']['instances'];
                if (array_key_exists('autodetect', $options['cluster']) && in_array($options['cluster']['autodetect'], array(true, false))) $settings['cluster.autodetect'] = $options['cluster']['autodetect'];
            }

            $this->sherlock = new \Sherlock\Sherlock($settings);

            if (is_array($instances)) {
                if (array_key_exists('host', $instances))  {
                    $this->sherlock->addNode($instances['host'], $instances['port']);
                } else {
                    foreach ($instances as $instance) {
                        if (array_key_exists('host', $instance) && is_string($instance['host'])
                            && array_key_exists('port', $instance) && is_int($instance['port']))
                        $this->sherlock->addNode($instance['host'], $instance['port']);
                    }
                }
            }

            return $this->sherlock->search();
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

    public function query()
    {
        $return = new \Sherlock\Sherlock;
        return $return::query();
    }
}