<?php
/**
 * @author Rémy M. Böhler
 */

namespace Statamic\Addons\Sentry;

use Sentry\SentryLaravel\SentryFacade;
use Sentry\SentryLaravel\SentryLaravelServiceProvider;
use Statamic\Extend\Extensible;

/**
 * Class SentryServiceProvider
 * @package Statamic\Addons\Sentry
 */
class SentryServiceProvider extends SentryLaravelServiceProvider
{
    use Extensible;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->enabled()) {
            return;
        }

        parent::boot();
    }

    public function register()
    {
        if (!$this->enabled()) {
            return;
        }

        // set config
        config([
            'sentry.dsn' => $this->getConfig('dsn', env('SENTRY_DSN')),
            'breadcrumbs.sql_bindings' => false,
            'user_context' => false, // does not work
        ]);

        /**
         * TODO add some more context
         *  - user
         *  - statamic version
         */

        // register classes
        $this->app->alias('Sentry', SentryFacade::class);

        // override exception handler
        $this->app->singleton(
            'Illuminate\Contracts\Debug\ExceptionHandler',
            'Statamic\Addons\Sentry\ExceptionHandler'
        );

        parent::register();
    }

    /**
     * Check if sentry should be enabled
     *
     * @return bool
     */
    private function enabled()
    {
        return
            $this->getConfigBool('enable', false) &&
            in_array(app()->environment(), $this->getConfig('environments', []));
    }
}
