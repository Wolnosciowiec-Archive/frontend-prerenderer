<?php declare(strict_types = 1);

use App\Controller\ChromiumRenderController;
use App\Controller\PhantomRenderController;
use App\Controller\RenderController;
use App\Controller\RenderInterface;
use App\Factory\PhantomRequestFactory;
use App\Manager\VisitedUrlsManager;
use App\Repository\ConfigurationRepository;
use JonnyW\PhantomJs\Client;
use Psr\Container\ContainerInterface;

$builder->addDefinitions([
    /*
     * Core and browser agnostic
     */
    ConfigurationRepository::class => function () {
        return new ConfigurationRepository(array_merge(
            require __DIR__ . '/../../config.dist.php',
            is_file(__DIR__ . '/../../config.php') ? require __DIR__ . '/../../config.php' : []
        ));
    },

    VisitedUrlsManager::class => function () { return new VisitedUrlsManager(); },

    RenderInterface::class => function (ContainerInterface $container) {
        return $container->get(RenderController::class);
    },

    RenderController::class => function (ContainerInterface $container) {
        /** @var ConfigurationRepository $config */
        $config = $container->get(ConfigurationRepository::class);

        return new RenderController(
            [
                $container->get(ChromiumRenderController::class),
                $container->get(PhantomRenderController::class)
            ],
            $config->get('renderer')
        );
    },

    /*
     * PhantomJS support
     */
    PhantomRequestFactory::class   => function (ContainerInterface $container) {
        /** @var ConfigurationRepository $config */
        $config = $container->get(ConfigurationRepository::class);

        return new PhantomRequestFactory(
            $container->get(Client::class),
            $config->get('skipped_headers', [])
        );
    },

    PhantomRenderController::class => function (ContainerInterface $container) {
        /** @var ConfigurationRepository $config */
        $config = $container->get(ConfigurationRepository::class);

        return new PhantomRenderController(
            $container->get(PhantomRequestFactory::class),
            $container->get(Client::class),
            $config->get('with_images', false),
            $config->get('delay', 2),
            $config->get('timeout', 0),
            $config->get('debug', false)
        );
    },

    Client::class => function () {
        return Client::getInstance();
    },

    /*
     * Chromium support
     */
    ChromiumRenderController::class => function (ContainerInterface $container) {
        /** @var ConfigurationRepository $config */
        $config = $container->get(ConfigurationRepository::class);

        return new ChromiumRenderController(
            $config->get('chromium_binary', 'chromium'),
            $config->get('with_images', false),
            $config->get('window_size', '1920x1080'),
            $config->get('open_process_limit', 3)
        );
    }
]);
