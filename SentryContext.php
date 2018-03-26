<?php

namespace Statamic\Addons\Sentry;

use Statamic\API\User;

class SentryContext
{
    /**
     * Get the user context to be attached to Sentry reports.
     *
     * @return array
     */
    public static function getUserContext()
    {
        if (User::loggedIn()) {
            $user = User::getCurrent();

            return [
                'id' => $user->id(),
                'username' => $user->username(),
                'email' => $user->email(),
                'path' => $user->path(),
                'status' => $user->status(),
            ];
        }

        return ['id' => null];
    }

    /**
     * Get the miscellaneous context to be attached to Sentry reports.
     *
     * @return array
     */
    public static function getTagsContext()
    {
        return ['statamic_version' => STATAMIC_VERSION];
    }
}
