<?php
/**
 * @author Rémy M. Böhler
 */

namespace Statamic\addons\Sentry;

use Exception;
use Statamic\Exceptions\Handler;

/**
 * Class ExceptionHandler
 * @package Statamic\addons\Sentry
 */
class ExceptionHandler extends Handler
{
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     */
    public function report(Exception $e)
    {
        if ($this->shouldReport($e)) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }
}