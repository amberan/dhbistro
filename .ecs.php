<?php

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
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveCommentedCodeFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(SetList::SPACES);
    $containerConfigurator->import(SetList::ARRAY);
    $containerConfigurator->import(SetList::DOCBLOCK);
    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(SetList::SYMFONY);
    $containerConfigurator->import(SetList::ARRAY);
    $containerConfigurator->import(SetList::PHP_CS_FIXER);
    $containerConfigurator->import(SetList::SYMPLIFY);
    $containerConfigurator->import(SetList::CLEAN_CODE);
    $containerConfigurator->import(SetList::COMMON);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        __DIR__ . '/vendor',

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

    $parameters->set(Option::INDENTATION, 'spaces');
    $parameters->set(Option::PARALLEL, true);
};
