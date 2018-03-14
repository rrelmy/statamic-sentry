<?php
/**
 * @author Rémy M. Böhler
 */

namespace Statamic\addons\Sentry;

use Exception;
use Statamic\API\User;
use Statamic\Exceptions\Handler;

/**
 * Class ExceptionHandler.
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
            $sentry = app('sentry');

            // add user context
            if (User::loggedIn()) {
                $user = User::getCurrent();

                $sentry->user_context([
                    'id' => $user->id(),
                    'username' => $user->username(),
                    'email' => $user->email(),
                    'path' => $user->path(),
                    'status' => $user->status(),
                ]);
            } else {
                $sentry->user_context(['id' => null]);
            }

            // add runtime context
            $sentry->tags_context(['statamic_version' => STATAMIC_VERSION]);

            // capture the exception
            $sentry->captureException($e);
        }

        parent::report($e);
    }
}
