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

    /**
     * @var int $waitForProcessTime
     */
    private $waitForProcessTime;

    public function __construct(
        string $chromeBinary = 'chromium',
        bool $withImages = false,
        string $windowSize = '1920x1080',
        int $openProcessLimit = 3,
        int $waitForProcessTime = 4)
    {
        $this->chromeBinary       = $chromeBinary;
        $this->withImages         = $withImages;
        $this->windowSize         = $windowSize;
        $this->openProcessLimit   = $openProcessLimit;
        $this->waitForProcessTime = $waitForProcessTime;
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
        $command = $this->chromeBinary .
                   ' --headless --no-sandbox --disable-gpu ' . $blinkSettings .
                   ' ' . $this->buildProxyArgument($request) .
                   ' --dump-dom --window-size=' . $this->windowSize . ' "' . $url . '"';

        if (!$this->canSpawnNewProcess()) {
            return new Response('Error: Too many requests. Open: ' . $this->getOpenedProcessesCount(), Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return new Response($this->executeCommand($command));
    }

    protected function executeCommand(string $command): string
    {
        return shell_exec($command) ?? '';
    }

    /**
     * @inheritdoc
     */
    public function canRender(string $renderName): bool
    {
        return in_array($renderName, ['chromium', 'chrome'], true);
    }

    private function buildProxyArgument(Request $request): string
    {
        $proxyAddress = (string) $request->headers->get('X-Proxy-Address');

        if (filter_var($proxyAddress, FILTER_VALIDATE_URL) === false) {
            return '';
        }

        return '--proxy-server=' . $proxyAddress;
    }

    private function canSpawnNewProcess(): bool
    {
        if ($this->getOpenedProcessesCount() >= $this->openProcessLimit) {
            sleep($this->waitForProcessTime);
        }

        return $this->getOpenedProcessesCount() < $this->openProcessLimit;
    }

    private function getOpenedProcessesCount(): int
    {
        return count(array_filter(explode("\n", $this->executeCommand('ps aux | grep "' . $this->chromeBinary . '" | grep -v grep | grep -v \'type=\'') ?? '')));
    }
}
