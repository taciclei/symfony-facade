<?php namespace Phpjit\SymfonysFacade\Services\Symfony;

use Illuminate\Support\Facades\App;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SymfonyContainer
{

    /** @var ContainerInterface  */
    private $container;
    public $kernel;

    /**
     * Load Symfonys kernel, from configuration.
     */
    public function __construct()
    {
        $this->kernel = new SymfonyKernel(App::environment(), true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();

    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getSymfonyService($id)
    {
        return $this->container->get($id);
    }

    public function handle(Request $request, int $type = HttpKernelInterface::MASTER_REQUEST, bool $catch = true)
    {
        return $this->kernel->handle($request, $type, $catch);
    }
}
