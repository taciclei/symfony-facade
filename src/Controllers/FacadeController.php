<?php namespace Phpjit\SymfonysFacade\Controllers;

use ApiPlatform\Core\Action\PlaceholderAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Phpjit\SymfonysFacade\Facades\Routes\SymfonyRoutesManager;
use Phpjit\SymfonysFacade\Services\Symfony\SymfonyContainer;
use Symfony\Component\ErrorHandler\Debug;

class FacadeController extends Controller
{
    public function __construct(
        SymfonyRoutesManager $symfonyRoutesManager,
        SymfonyContainer $symfonyContainer,
        Request $request
    ) {
        $this->symfonyRoutesManager = $symfonyRoutesManager;
        $this->symfonyContainer = $symfonyContainer;
        $this->request = $request;
    }

    public function __invoke($target, $arguments = [])
    {
        $request = SymfonyRequest::createFromGlobals();
        //Debug::enable();
        $kernel = $this->symfonyContainer->kernel;

        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    }

    private function convertRequest()
    {
        return new SymfonyRequest($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER, '');
    }
}
