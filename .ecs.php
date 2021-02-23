<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer;
use PhpCsFixer\Fixer\StringNotation\SimpleToComplexStringVariableFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use SlevomatCodingStandard\Sniffs\Commenting\DisallowCommentAfterCodeSniff;
use SlevomatCodingStandard\Sniffs\Whitespaces\DuplicateSpacesSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveCommentedCodeFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short',
        ]])
    ;

    $parameters = $containerConfigurator->parameters();
//    $parameters->set('cache_directory', '.ecs_cache');
    $parameters->set(Option::PATHS, [__DIR__ . '.']);

    $parameters->set(Option::SKIP,
    [__DIR__ . '/vendor/*',
        ArrayOpenerAndCloserNewlineFixer::class,
        ConcatSpaceFixer::class,
        DeclareStrictTypesFixer::class,
        DisallowCommentAfterCodeSniff::class,
        DuplicateSpacesSniff::class,
        ExplicitStringVariableFixer::class,
        IncrementStyleFixer::class,
        LineLengthFixer::class,
        MethodArgumentSpaceFixer::class,
        NotOperatorWithSuccessorSpaceFixer::class,
        RemoveCommentedCodeFixer::class,
        SimpleToComplexStringVariableFixer::class,
        SingleQuoteFixer::class,
        YodaStyleFixer::class,
        StrictComparisonFixer::class,

        //'Cognitive complexity for method "addAction" is 13 but has to be less than or equal to 8.',
    ]);

    $parameters->set(Option::SETS, [
        SetList::PSR_1,
        SetList::PSR_12,
        SetList::PHP_70,
        SetList::PHP_73_MIGRATION,
        SetList::PHP_CS_FIXER,
        SetList::PHP_71,
        SetList::CLEAN_CODE,
        SetList::SYMPLIFY,
        SetList::ARRAY,
        SetList::COMMON,
        //         SetList::COMMENTS,
        //         SetList::SPACES,
        SetList::DEAD_CODE,
        SetList::SYMFONY,
    ]);

    $parameters->set(Option::INDENTATION, 'spaces');
    //$parameters->set(Option::LINE_ENDING, "\r\n");
};
