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
namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoWhitespaceInBlankLineFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Remove trailing whitespace at the end of blank lines.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n   \n\$a = 1;\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after CombineConsecutiveIssetsFixer, CombineConsecutiveUnsetsFixer, FunctionToConstantFixer, NoEmptyCommentFixer, NoEmptyPhpdocFixer, NoEmptyStatementFixer, NoUselessElseFixer, NoUselessReturnFixer.
     */
    public function getPriority() : int
    {
        return -19;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        // skip first as it cannot be a white space token
        for ($i = 1, $count = \count($tokens); $i < $count; ++$i) {
            if ($tokens[$i]->isWhitespace()) {
                $this->fixWhitespaceToken($tokens, $i);
            }
        }
    }
    private function fixWhitespaceToken(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : void
    {
        $content = $tokens[$index]->getContent();
        $lines = \PhpCsFixer\Preg::split("/(\r\n|\n)/", $content);
        $lineCount = \count($lines);
        if ($lineCount > 2 || $lineCount > 0 && (!isset($tokens[$index + 1]) || $tokens[$index - 1]->isGivenKind(\T_OPEN_TAG))) {
            $lMax = isset($tokens[$index + 1]) ? $lineCount - 1 : $lineCount;
            $lStart = 1;
            if ($tokens[$index - 1]->isGivenKind(\T_OPEN_TAG) && "\n" === \substr($tokens[$index - 1]->getContent(), -1)) {
                $lStart = 0;
            }
            for ($l = $lStart; $l < $lMax; ++$l) {
                $lines[$l] = \PhpCsFixer\Preg::replace('/^\\h+$/', '', $lines[$l]);
            }
            $content = \implode($this->whitespacesConfig->getLineEnding(), $lines);
            if ('' !== $content) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $content]);
            } else {
                $tokens->clearAt($index);
            }
        }
    }
}
