<?php

namespace AppBundle\Session;

use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage as BaseNativeSessionStorage;

class NativeSessionStorage extends BaseNativeSessionStorage
{
    public function regenerateWithId($id)
    {
        // Cannot regenerate the session ID for non-active sessions.
        if (\PHP_SESSION_ACTIVE !== session_status()) {
            return false;
        }

        if (headers_sent()) {
            return false;
        }

        session_id($id);
        $this->loadSession();

        setcookie($this->getName(), $id, 0, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
    }
}
