<?php

declare (strict_types=1);
namespace ECSPrefix20211002;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20211002\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use ECSPrefix20211002\Symplify\Skipper\ValueObject\Option;
use ECSPrefix20211002\Symplify\SmartFileSystem\Normalizer\PathNormalizer;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(\ECSPrefix20211002\Symplify\Skipper\ValueObject\Option::SKIP, []);
    $parameters->set(\ECSPrefix20211002\Symplify\Skipper\ValueObject\Option::ONLY, []);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('ECSPrefix20211002\Symplify\Skipper\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Bundle', __DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject']);
    $services->set(\ECSPrefix20211002\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker::class);
    $services->set(\ECSPrefix20211002\Symplify\SmartFileSystem\Normalizer\PathNormalizer::class);
};
