<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ReturnTypeDeclarationFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        $versionSpecification = new \PhpCsFixer\FixerDefinition\VersionSpecification(70000);
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('There should be one or no space before colon, and one space after it in return type declarations, according to configuration.', [new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction foo(int \$a):string {};\n", $versionSpecification), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction foo(int \$a):string {};\n", $versionSpecification, ['space_before' => 'none']), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction foo(int \$a):string {};\n", $versionSpecification, ['space_before' => 'one'])], 'Rule is applied only in a PHP 7+ environment.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run after PhpdocToReturnTypeFixer, VoidReturnFixer.
     */
    public function getPriority() : int
    {
        return -17;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return \PHP_VERSION_ID >= 70000 && $tokens->isTokenKindFound(\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $oneSpaceBefore = 'one' === $this->configuration['space_before'];
        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            if (!$tokens[$index]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON)) {
                continue;
            }
            $previousIndex = $index - 1;
            $previousToken = $tokens[$previousIndex];
            if ($previousToken->isWhitespace()) {
                if (!$tokens[$tokens->getPrevNonWhitespace($index - 1)]->isComment()) {
                    if ($oneSpaceBefore) {
                        $tokens[$previousIndex] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
                    } else {
                        $tokens->clearAt($previousIndex);
                    }
                }
            } elseif ($oneSpaceBefore) {
                $tokenWasAdded = $tokens->ensureWhitespaceAtIndex($index, 0, ' ');
                if ($tokenWasAdded) {
                    ++$limit;
                }
                ++$index;
            }
            ++$index;
            $tokenWasAdded = $tokens->ensureWhitespaceAtIndex($index, 0, ' ');
            if ($tokenWasAdded) {
                ++$limit;
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('space_before', 'Spacing to apply before colon.'))->setAllowedValues(['one', 'none'])->setDefault('none')->getOption()]);
    }
}
