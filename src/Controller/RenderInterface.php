<?php declare(strict_types = 1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\{Request, Response};

interface RenderInterface
{
    /**
     * Renders the page in the browser and returns an HTTP response
     *
     * @param Request $request
     *
     * @return Response
     */
    public function renderAction(Request $request): Response;

    /**
     * Tells if this is the correct render controller implementation
     * for specified render name
     *
     * @param string $renderName
     *
     * @return bool
     */
    public function canRender(string $renderName): bool;
}
