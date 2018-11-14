<?php

namespace Mouf\Mvc\Splash\Controllers\Admin;

use Mouf\Composer\ClassNameMapper;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\Utils\WebLibraryManager\WebLibrary;
use Mouf\MoufManager;
use Mouf\Mvc\Splash\Controllers\Controller;
use TheCodingMachine\Splash\Services\SplashCreateControllerService;
use TheCodingMachine\Splash\Services\SplashCreateControllerServiceException;

/**
 * A controller used to create controllers in Splash.
 */
class SplashCreateControllerController extends Controller
{
    /**
     * The template used by the Splash page.
     *
     * @var TemplateInterface
     */
    public $template;

    /**
     * @var HtmlBlock
     */
    public $content;

    protected $selfedit;
    protected $controllerNamespace;
    protected $autoloadDetected;

    /**
     * Displays the create controller page.
     *
     * @Action
     *
     * @param string $controllernamespace
     * @param string $selfedit
     */
    public function index($controllernamespace = null, $selfedit = 'false')
    {
        $this->selfedit = $selfedit;
        $this->controllerNamespace = $controllernamespace;

        if ($this->controllerNamespace == null) {
            $classNameMapper = ClassNameMapper::createFromComposerFile(__DIR__.'/../../../../../../../../../composer.json');
            $namespaces = $classNameMapper->getManagedNamespaces();
            if ($namespaces) {
                $this->autoloadDetected = true;
                $rootNamespace = $namespaces[0];
                $this->controllerNamespace = $rootNamespace.'Controllers';
            } else {
                $this->autoloadDetected = false;
                $this->controllerNamespace = 'YourApplication\\Controllers';
            }
        } else {
            $this->autoloadDetected = true;
        }

        $this->template->getWebLibraryManager()->addLibrary(new WebLibrary(
                array(
                        '../mvc.splash-ui/src/views/javascript/angular.min.js',
                        '../mvc.splash-ui/src/views/javascript/ui-utils.min.js',
                        '../mvc.splash-ui/src/views/javascript/createController.js',
                )));

        $this->content->addFile(__DIR__.'/../../../../../views/admin/createController.php', $this);
        $this->template->toHtml();
    }

    /**
     * Triggers the controller generation.
     *
     * @Action
     *
     * @param string $controllerName
     * @param string $instanceName
     * @param string $namespace
     * @param string $injectLogger
     * @param string $injectTemplate
     * @param array  $actions
     */
    public function generate($controllerName, $instanceName, $namespace, $injectLogger = false,
            $injectTemplate = false, $actions = array())
    {
        $injectLogger = ($injectLogger == 'false') ? false : $injectLogger;
        $injectTemplate = ($injectTemplate == 'false') ? false : $injectTemplate;

        $moufManager = MoufManager::getMoufManagerHiddenInstance();

        $generatorService = new SplashCreateControllerService();
        try {
            $generatorService->generate($moufManager, $controllerName, $instanceName, $namespace, $injectLogger,
                $injectTemplate, $actions);
        } catch (SplashCreateControllerServiceException $e) {
            $errors = $e->getErrors();
            header('Content-type: application/json');
            $errors['status'] = 'ko';
            echo json_encode($errors);

            return;
        }

        echo json_encode(array('status' => 'ok'));
    }
}
