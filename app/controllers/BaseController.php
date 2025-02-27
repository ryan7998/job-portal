<?php
class BaseController
{
    protected function render($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . "/../views/$view.php";
    }

    protected function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCsrfToken($token)
    {
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
