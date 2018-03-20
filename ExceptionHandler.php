<?php
/**
 * @author Rémy M. Böhler
 */

namespace Statamic\Addons\Sentry;

use Exception;
use Statamic\API\User;
use Statamic\Exceptions\Handler;

/**
 * Class ExceptionHandler.
 * @package Statamic\Addons\Sentry
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
            $sentry = app('sentry');
            $sentry->user_context(SentryContext::getUserContext());
            $sentry->tags_context(SentryContext::getTagsContext());

            // capture the exception
            $sentry->captureException($e);
        }

        parent::report($e);
    }
}
