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
namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractNoUselessElseFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class NoSuperfluousElseifFixer extends \PhpCsFixer\AbstractNoUselessElseFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_ELSE, \T_ELSEIF]);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Replaces superfluous `elseif` with `if`.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nif (\$a) {\n    return 1;\n} elseif (\$b) {\n    return 2;\n}\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before SimplifiedIfReturnFixer.
     * Must run after NoAlternativeSyntaxFixer.
     */
    public function getPriority() : int
    {
        return parent::getPriority();
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        foreach ($tokens as $index => $token) {
            if ($this->isElseif($tokens, $index) && $this->isSuperfluousElse($tokens, $index)) {
                $this->convertElseifToIf($tokens, $index);
            }
        }
    }
    private function isElseif(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        return $tokens[$index]->isGivenKind(\T_ELSEIF) || $tokens[$index]->isGivenKind(\T_ELSE) && $tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind(\T_IF);
    }
    private function convertElseifToIf(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : void
    {
        if ($tokens[$index]->isGivenKind(\T_ELSE)) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        } else {
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_IF, 'if']);
        }
        $whitespace = '';
        for ($previous = $index - 1; $previous > 0; --$previous) {
            $token = $tokens[$previous];
            if ($token->isWhitespace() && \PhpCsFixer\Preg::match('/(\\R\\N*)$/', $token->getContent(), $matches)) {
                $whitespace = $matches[1];
                break;
            }
        }
        if ('' === $whitespace) {
            return;
        }
        $previousToken = $tokens[$index - 1];
        if (!$previousToken->isWhitespace()) {
            $tokens->insertAt($index, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $whitespace]));
        } elseif (!\PhpCsFixer\Preg::match('/\\R/', $previousToken->getContent())) {
            $tokens[$index - 1] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $whitespace]);
        }
    }
}
