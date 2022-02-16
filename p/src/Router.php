<?php

namespace SON;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

class Router implements \ArrayAccess
{
  use Collection;
    public function handler()
    {
        $path = $_SERVER['PATH_INFO'] ?? '/';
        if (strlen($path) > 1) {
            $path = rtrim($path, '/');
        }

        if ($this->offsetExists($path)) {
            return $this->offsetGet($path);
        }

        http_response_code(404);
        echo 'Página inexistente';
        exit;
    }


}