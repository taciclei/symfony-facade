<?php

namespace Phpjit\SymfonysFacade\Services\Symfony;

use ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Phpjit\SymfonyBundles;

class SymfonyKernel extends Kernel
{
    use MicroKernelTrait;
    /**
     * Symfonys app dir.
     *
     * @var string
     */
    public $appDir;

    /**
     * Symfonys log dir.
     *
     * @var string
     */
    public $logDir;

    public function __construct($env, $debug)
    {
        // Set Symfony app dir.
        $appDir = config('app.symfonysfacade_app_dir');
        if (isset($appDir)) {
            $this->appDir = base_path() . '/' . $appDir . '/' . $env;
        } else {
            $this->appDir = base_path() .'/storage/app/symfony';
        }

        parent::__construct($env, $debug);

        // Set Symfony log dir.
        $logDir = config('app.symfonysfacade_log_dir');
        if (isset($logDir)) {
            $this->logDir = base_path() . '/' . $logDir . '/' . $env;
        } else {
            $this->logDir = base_path() . '/storage/app/symfony'. $env;
        }
    }

    /**
     *
     * @return array
     */
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle()
        ];
        return array_merge($this->getBundlesFromConfig(), $bundles);
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import($this->appDir . '/config/{packages}/*.yaml');
        $container->import($this->appDir . '/config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file($this->appDir . '/config/services.yaml')) {
            $container->import($this->appDir . '/config/{services}.yaml');
            $container->import($this->appDir . '/config/{services}_'.$this->environment.'.yaml');
        } elseif (is_file($path = \dirname(__DIR__).'/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->appDir . '/config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import($this->appDir . '/config/{routes}/*.yaml');

        if (is_file($this->appDir . '/config/routes.yaml')) {
            $routes->import($this->appDir . '/config/{routes}.yaml');
        } elseif (is_file($path = $this->appDir . '/config/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }
    }
    public function getRootDir()
    {
        return $this->appDir;
    }

    public function getCacheDir()
    {
        return $this->appDir . '/cache';
    }

    public function getLogDir()
    {
        return $this->appDir . '/logs';
    }

    private function getBundlesFromConfig()
    {
        $bundleProvider = config('SymfonyBundles');
        if (!empty($bundleProvider)) {
            return $bundleProvider;
        }

        return [];
    }
}
