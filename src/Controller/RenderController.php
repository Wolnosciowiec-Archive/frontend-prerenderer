<?php declare(strict_types = 1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renders the page and dumps into a Response object
 * by one of browsers
 */
class RenderController implements RenderInterface
{
    /**
     * @var RenderInterface[] $aggregatedControllers
     */
    private $aggregatedControllers;

    /**
     * @var string $renderName
     */
    private $renderName;

    public function __construct(array $aggregatedControllers, string $renderName)
    {
        $this->aggregatedControllers = $aggregatedControllers;
        $this->renderName            = $renderName;
    }

    /**
     * @inheritdoc
     */
    public function renderAction(Request $request): Response
    {
        foreach ($this->aggregatedControllers as $controller) {
            if ($controller->canRender($this->renderName)) {
                return $controller->renderAction($request);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function canRender(string $renderName): bool
    {
        foreach ($this->aggregatedControllers as $controller) {
            if ($controller->canRender($renderName)) {
                return true;
            }
        }

        return false;
    }
}
