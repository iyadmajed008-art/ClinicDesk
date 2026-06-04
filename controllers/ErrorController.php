<?php

declare(strict_types=1);

final class ErrorController
{
    public function forbidden(): void
    {
        http_response_code(403);
        render('errors/403', ['pageTitle' => 'Forbidden']);
    }

    public function not_found(): void
    {
        http_response_code(404);
        render('errors/404', ['pageTitle' => 'Not Found']);
    }
}
