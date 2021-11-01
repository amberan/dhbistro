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
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Vladimir Boliev <voff.web@gmail.com>
 */
final class MethodChainingIndentationFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Method chaining MUST be properly indented. Method chaining with different levels of indentation is not supported.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$user->setEmail('voff.web@gmail.com')\n         ->setPassword('233434');\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before ArrayIndentationFixer, MethodArgumentSpaceFixer.
     * Must run after BracesFixer.
     */
    public function getPriority() : int
    {
        return 34;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound(\PhpCsFixer\Tokenizer\Token::getObjectOperatorKinds());
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        for ($index = 1, $count = \count($tokens); $index < $count; ++$index) {
            if (!$tokens[$index]->isObjectOperator()) {
                continue;
            }
            if ($this->canBeMovedToNextLine($index, $tokens)) {
                $newline = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $lineEnding]);
                if ($tokens[$index - 1]->isWhitespace()) {
                    $tokens[$index - 1] = $newline;
                } else {
                    $tokens->insertAt($index, $newline);
                    ++$index;
                }
            }
            $currentIndent = $this->getIndentAt($tokens, $index - 1);
            if (null === $currentIndent) {
                continue;
            }
            $expectedIndent = $this->getExpectedIndentAt($tokens, $index);
            if ($currentIndent !== $expectedIndent) {
                $tokens[$index - 1] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $lineEnding . $expectedIndent]);
            }
        }
    }
    /**
     * @param int $index index of the first token on the line to indent
     */
    private function getExpectedIndentAt(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : string
    {
        $index = $tokens->getPrevMeaningfulToken($index);
        $indent = $this->whitespacesConfig->getIndent();
        for ($i = $index; $i >= 0; --$i) {
            if ($tokens[$i]->equals(')')) {
                $i = $tokens->findBlockStart(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $i);
            }
            $currentIndent = $this->getIndentAt($tokens, $i);
            if (null === $currentIndent) {
                continue;
            }
            if ($this->currentLineRequiresExtraIndentLevel($tokens, $i, $index)) {
                return $currentIndent . $indent;
            }
            return $currentIndent;
        }
        return $indent;
    }
    /**
     * @param int $index position of the object operator token ("->" or "?->")
     */
    private function canBeMovedToNextLine(int $index, \PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        $prevMeaningful = $tokens->getPrevMeaningfulToken($index);
        $hasCommentBefore = \false;
        for ($i = $index - 1; $i > $prevMeaningful; --$i) {
            if ($tokens[$i]->isComment()) {
                $hasCommentBefore = \true;
                continue;
            }
            if ($tokens[$i]->isWhitespace() && 1 === \PhpCsFixer\Preg::match('/\\R/', $tokens[$i]->getContent())) {
                return $hasCommentBefore;
            }
        }
        return \false;
    }
    /**
     * @param int $index index of the indentation token
     */
    private function getIndentAt(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : ?string
    {
        if (1 === \PhpCsFixer\Preg::match('/\\R{1}(\\h*)$/', $this->getIndentContentAt($tokens, $index), $matches)) {
            return $matches[1];
        }
        return null;
    }
    private function getIndentContentAt(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : string
    {
        if (!$tokens[$index]->isGivenKind([\T_WHITESPACE, \T_INLINE_HTML])) {
            return '';
        }
        $content = $tokens[$index]->getContent();
        if ($tokens[$index]->isWhitespace() && $tokens[$index - 1]->isGivenKind(\T_OPEN_TAG)) {
            $content = $tokens[$index - 1]->getContent() . $content;
        }
        if (\PhpCsFixer\Preg::match('/\\R/', $content)) {
            return $content;
        }
        return '';
    }
    /**
     * @param int $start index of first meaningful token on previous line
     * @param int $end   index of last token on previous line
     */
    private function currentLineRequiresExtraIndentLevel(\PhpCsFixer\Tokenizer\Tokens $tokens, int $start, int $end) : bool
    {
        if ($tokens[$start + 1]->isObjectOperator()) {
            return \false;
        }
        if ($tokens[$end]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_BRACE_CLASS_INSTANTIATION_CLOSE)) {
            return \true;
        }
        return !$tokens[$end]->equals(')') || $tokens->findBlockStart(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $end) >= $start;
    }
}
