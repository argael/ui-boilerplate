<?php
/**
 *
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class App extends \Slim\App
{
    const DEFAULT_CSS = '/assets/css/main.css';

    /**
     * App constructor.
     *
     * @param array $container
     */
    public function __construct($container = [])
    {
        $settings = [
            'settings' => ['displayErrorDetails' => true],
            'basePath' => dirname(__DIR__),
            'view' => function ($container) {
                $view = new \Slim\Views\Twig(
                    [$container->basePath . '/src/views', $container->basePath . '/app/views'],
                    ['cache' => false, 'debug'=>true]
                );

                $view->addExtension(new \Twig_Extension_Debug());
                $view->addExtension(
                    new Slim\Views\TwigExtension(
                        $container->get('router'),
                        \Slim\Http\Uri::createFromEnvironment(
                            new \Slim\Http\Environment($_SERVER)))
                );

                return $view;
            }
        ];
        parent::__construct(array_merge($settings, $container));
    }

    /**
     * Prepare route to display all available pages
     *
     * @return $this
     */
    public function allPages()
    {
        $this->get('/', function(Request $request, Response $response) {
            $templates = [];
            foreach(new DirectoryIterator($this->basePath . '/src/views/') as $fileInfo) {
                if ($fileInfo->isFile() && strtolower($fileInfo->getExtension()) === 'twig') {
                    $baseName = $fileInfo->getBasename('.twig');

                    $templates[$baseName] = [
                        'name' => $baseName,
                        'has_data' => file_exists($this->basePath . '/src/data/' . $baseName . '.php'),
                    ];
                }
            }
            ksort($templates);

            return $this->view->render($response, 'app_pages.twig', ['templates' => $templates, 'css_url' => $cssUrl]);
        })->setName('templates.list');

        return $this;
    }

    /**
     * Prepare route to display a specific page.
     *
     * @return $this
     */
    public function currentPage()
    {
        $this->get('/view/{template}', function(Request $request, Response $response, array $args) {
            $template = $args['template'] . '.twig';
            $data = $this->basePath . '/src/data/' . $args['template'] . '.php';
            $data = (file_exists($data)) ? require $data : [];

            return $this->view->render($response, $template, $data);
        })->setName('templates.view');

        $this->get('/data/{template}', function(Request $request, Response $response, array $args) {
            $data = $this->basePath . '/src/data/' . $args['template'] . '.php';
            $data = (file_exists($data)) ? require $data : [];

            return $response->withJson($data);
        })->setName('templates.data');

        return $this;
    }
}