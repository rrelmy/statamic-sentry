<?php
/**
 * @author Rémy M. Böhler
 */

namespace Statamic\Addons\Sentry;

use Sentry\SentryLaravel\SentryFacade;
use Sentry\SentryLaravel\SentryLaravelServiceProvider;
use Statamic\Extend\Extensible;

/**
 * Class SentryServiceProvider.
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
         *  - statamic version.
         */

        // register classes
        $this->app->alias('Sentry', SentryFacade::class);

        // override exception handler
        $this->app->singleton(
            'Illuminate\Contracts\Debug\ExceptionHandler',
            'Statamic\Addons\Sentry\ExceptionHandler'
        );

        parent::register();

        if (!$this->logsEnabled()) {
            $monolog = \Log::getMonolog();

            // register a monolog handler to report log entries to Sentry
            $handler = new \Monolog\Handler\RavenHandler(app('sentry'));
            $handler->setFormatter(new \Monolog\Formatter\LineFormatter("%message% %context% %extra%\n"));
            $monolog->pushHandler($handler);

            $monolog->pushProcessor(function ($record) {
                // add contex to the log record
                $record['context']['user'] = SentryContext::getUserContext();
                $record['context']['tags'] = SentryContext::getTagsContext();

                return $record;
            });
        }
    }

    /**
     * Check if sentry should be enabled.
     *
     * @return bool
     */
    private function enabled()
    {
        return
            $this->getConfigBool('enable', false) &&
            in_array(app()->environment(), $this->getConfig('environments', []));
    }

    /**
     * Check if logs should be reported.
     *
     * @return bool
     */
    private function logsEnabled()
    {
        return
            $this->getConfigBool('enable_logs', false) &&
            in_array(app()->environment(), $this->getConfig('environments', []));
    }
}
