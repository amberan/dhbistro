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
namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoTrailingWhitespaceInCommentFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('There MUST be no trailing spaces inside comment or PHPDoc.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
// This is ' . '
// a comment. ' . '
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after PhpdocNoUselessInheritdocFixer.
     */
    public function getPriority() : int
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_COMMENT, \T_DOC_COMMENT]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(\T_DOC_COMMENT)) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, \PhpCsFixer\Preg::replace('/(*ANY)[\\h]+$/m', '', $token->getContent())]);
                continue;
            }
            if ($token->isGivenKind(\T_COMMENT)) {
                if ('/*' === \substr($token->getContent(), 0, 2)) {
                    $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_COMMENT, \PhpCsFixer\Preg::replace('/(*ANY)[\\h]+$/m', '', $token->getContent())]);
                } elseif (isset($tokens[$index + 1]) && $tokens[$index + 1]->isWhitespace()) {
                    $trimmedContent = \ltrim($tokens[$index + 1]->getContent(), " \t");
                    if ('' !== $trimmedContent) {
                        $tokens[$index + 1] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $trimmedContent]);
                    } else {
                        $tokens->clearAt($index + 1);
                    }
                }
            }
        }
    }
}
