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
        print_r(session_save_path());die();

        session_write_close();
        session_id($id);
        session_start();

        $this->loadSession();

        setcookie($this->getName(), $id, 0, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
    }
}
