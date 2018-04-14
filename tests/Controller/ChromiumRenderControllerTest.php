<?php declare(strict_types = 1);

namespace Tests\Controller;

use App\Controller\ChromiumRenderController;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestCase;

/**
 * @see ChromiumRenderController
 */
class ChromiumRenderControllerTest extends TestCase
{
    /**
     * @see ChromiumRenderController::renderAction()
     */
    public function test_invokes_with_proper_url()
    {
        $service = $this->createServiceMock();

        // asserts
        $service->expects($this->at(1))->method('executeCommand')->with("ps aux | grep \"chromium\" | grep -v grep | grep -v 'type='")->willReturn('...');
        $service->expects($this->at(2))->method('executeCommand')->with('chromium --headless --no-sandbox --disable-gpu --blink-settings=imagesEnabled=false   --dump-dom --window-size=1920x1080 "http://iwa-ait.org/"');

        // the action
        $service->renderAction(Request::create('http://iwa-ait.org'));
    }

    /**
     * @see ChromiumRenderController::renderAction()
     */
    public function test_proxy_valid_data_works()
    {
        $service = $this->createServiceMock([
            /* chromeBinary */ 'chrome',
            /* withImages */   true,
            /* windowSize */   '1280x720',
            /* openProcessLimit */ 3
        ]);

        $service->expects($this->at(2))->method('executeCommand')->with('chrome --headless --no-sandbox --disable-gpu  --proxy-server=http://localhost:8080 --dump-dom --window-size=1280x720 "http://zsp.net.pl/"');
        $service->renderAction(Request::create('http://zsp.net.pl', 'GET', [], [], [], ['HTTP_X_PROXY_ADDRESS' => 'http://localhost:8080']));
    }

    /**
     * @inheritdoc
     */
    public function test_cannot_pass_non_url_as_the_proxy()
    {
        $service = $this->createServiceMock();
        $service->expects($this->at(2))->method('executeCommand')->with('chromium --headless --no-sandbox --disable-gpu --blink-settings=imagesEnabled=false   --dump-dom --window-size=1920x1080 "https://wolnosciowiec.org/"');
        $service->renderAction(Request::create('https://wolnosciowiec.org', 'GET', [], [], [], ['HTTP_X_PROXY_ADDRESS' => '"; SELECT 1;']));
    }
    
    /**
     * @param array|null $constructorArgs
     *
     * @return MockObject|ChromiumRenderController
     */
    private function createServiceMock(array $constructorArgs = null)
    {
        if ($constructorArgs === null) {
            $constructorArgs = [
                /* chromeBinary */ 'chromium',
                /* withImages */   false,
                /* windowSize */   '1920x1080',
                /* openProcessLimit */ 3
            ];
        }

        $builder = $this->getMockBuilder(ChromiumRenderController::class);
        $builder->setConstructorArgs($constructorArgs);
        $builder->setMethods(['executeCommand']);
        
        return $builder->getMock();
    }
}