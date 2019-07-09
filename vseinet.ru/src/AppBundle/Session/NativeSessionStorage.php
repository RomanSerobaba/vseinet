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

        $data = session_encode();
        var_dump(session_write_close());
        var_dump(session_id($id));
        var_dump(session_start());
        session_decode($data);
var_dump($id, session_id());die();
        $this->loadSession();

        setcookie($this->getName(), $id, 0, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
    }
}
