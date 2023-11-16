<?php


declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;


use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
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
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveCommentedCodeFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

// use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
// use Symplify\EasyCodingStandard\ValueObject\Option;
// use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/source/API',
        __DIR__ . '/source/custom',
        __DIR__ . '/doc',
        __DIR__ . '/source/inc',
        __DIR__ . '/source/lib',
        __DIR__ . '/source/pages',
        __DIR__ . '/source/sql',
    ]);

    // this way you add a single rule
    $ecsConfig->rules([
        NoUnusedImportsFixer::class,
    ]);

    // this way you can add sets - group of rules
    $ecsConfig->sets([
        // run and fix, one by one
        SetList::ARRAY,
        SetList::CLEAN_CODE,
        SetList::DOCBLOCK,
        SetList::PHPUNIT,
        SetList::PSR_12,
        SetList::SPACES,
        SetList::SYMPLIFY,
    ]);

    $ecsConfig->skip([
        __DIR__ . '/source/cache/',
        __DIR__ . '/source/css/',
        __DIR__ . '/source/files/',
        __DIR__ . '/source/images/',
        __DIR__ . '/sourge/js/',
        __DIR__ . '/source/log/',
        __DIR__ . '/source/vendor/',

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
        AssignmentInConditionSniff::class,

    ]);

    $ecsConfig->indentation('spaces');
};
