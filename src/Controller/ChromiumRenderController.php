<?php declare(strict_types = 1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChromiumRenderController implements RenderInterface
{
    /**
     * @var string $chromeBinary
     */
    private $chromeBinary;

    /**
     * @var string $windowSize
     */
    private $windowSize;

    /**
     * @var bool $withImages
     */
    private $withImages;

    /**
     * @var int $openProcessLimit
     */
    private $openProcessLimit;

    public function __construct(
        string $chromeBinary = 'chromium',
        bool $withImages = false,
        string $windowSize = '1920x1080',
        int $openProcessLimit = 3)
    {
        $this->chromeBinary = $chromeBinary;
        $this->withImages   = $withImages;
        $this->windowSize   = $windowSize;
        $this->openProcessLimit = $openProcessLimit;
    }

    /**
     * @inheritdoc
     */
    public function renderAction(Request $request): Response
    {
        $url = $request->getUri();
        $url = str_replace(['"', "'", ' ', "\0"], '', $url);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return new Response('Malformed URL', Response::HTTP_BAD_REQUEST);
        }

        $blinkSettings = $this->withImages ? '' : '--blink-settings=imagesEnabled=false ';
        $command = $this->chromeBinary . ' --headless --no-sandbox --disable-gpu ' . $blinkSettings . ' --dump-dom --window-size=' . $this->windowSize . ' "' . $url . '"';

        if (!$this->canSpawnNewProcess()) {
            return new Response('Error: Too many requests', 429);
        }

        return new Response(shell_exec($command));
    }

    private function canSpawnNewProcess(): bool
    {
        if ($this->getOpenedProcessesCount() >= $this->openProcessLimit) {
            sleep(4);
        }

        if ($this->getOpenedProcessesCount() >= $this->openProcessLimit) {
            return false;
        }

        return true;
    }

    private function getOpenedProcessesCount(): int
    {
        return count(array_filter(explode("\n", shell_exec('ps aux | grep "' . $this->chromeBinary . '" | grep -v grep | grep -v \'type=\'') ?? '')));
    }

    /**
     * @inheritdoc
     */
    public function canRender(string $renderName): bool
    {
        return in_array($renderName, ['chromium', 'chrome'], true);
    }
}
