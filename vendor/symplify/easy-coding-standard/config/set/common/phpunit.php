<?php

declare (strict_types=1);
namespace ECSPrefix20211002;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(\PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer::class);
    $services->set(\PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer::class);
    $services->set(\PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer::class);
};
