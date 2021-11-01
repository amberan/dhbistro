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
namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareParenthesesFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * Fixer for rules defined in PSR2 ¶4.1, ¶4.4, ¶5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class BracesFixer extends \PhpCsFixer\AbstractProxyFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface, \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * @internal
     */
    public const LINE_NEXT = 'next';
    /**
     * @internal
     */
    public const LINE_SAME = 'same';
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('The body of each structure MUST be enclosed by braces. Braces should be properly placed. Body of braces should be properly indented.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php

class Foo {
    public function bar($baz) {
        if ($baz = 900) echo "Hello!";

        if ($baz = 9000)
            echo "Wait!";

        if ($baz == true)
        {
            echo "Why?";
        }
        else
        {
            echo "Ha?";
        }

        if (is_array($baz))
            foreach ($baz as $b)
            {
                echo $b;
            }
    }
}
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
$positive = function ($item) { return $item >= 0; };
$negative = function ($item) {
                return $item < 0; };
', ['allow_single_line_closure' => \true]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

class Foo
{
    public function bar($baz)
    {
        if ($baz = 900) echo "Hello!";

        if ($baz = 9000)
            echo "Wait!";

        if ($baz == true)
        {
            echo "Why?";
        }
        else
        {
            echo "Ha?";
        }

        if (is_array($baz))
            foreach ($baz as $b)
            {
                echo $b;
            }
    }
}
', ['position_after_functions_and_oop_constructs' => self::LINE_SAME])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before ArrayIndentationFixer, MethodArgumentSpaceFixer, MethodChainingIndentationFixer.
     * Must run after ClassAttributesSeparationFixer, ClassDefinitionFixer, ElseifFixer, EmptyLoopBodyFixer, LineEndingFixer, NoAlternativeSyntaxFixer, NoEmptyStatementFixer, NoUselessElseFixer, SingleLineThrowFixer, SingleSpaceAfterConstructFixer, SingleTraitInsertPerStatementFixer.
     */
    public function getPriority() : int
    {
        return 35;
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
        $this->fixCommentBeforeBrace($tokens);
        $this->fixMissingControlBraces($tokens);
        $this->fixIndents($tokens);
        $this->fixControlContinuationBraces($tokens);
        $this->fixSpaceAroundToken($tokens);
        $this->fixDoWhile($tokens);
        parent::applyFix($file, $tokens);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('allow_single_line_anonymous_class_with_empty_body', 'Whether single line anonymous class with empty body notation should be allowed.'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('allow_single_line_closure', 'Whether single line lambda notation should be allowed.'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('position_after_functions_and_oop_constructs', 'whether the opening brace should be placed on "next" or "same" line after classy constructs (non-anonymous classes, interfaces, traits, methods and non-lambda functions).'))->setAllowedValues([self::LINE_NEXT, self::LINE_SAME])->setDefault(self::LINE_NEXT)->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('position_after_control_structures', 'whether the opening brace should be placed on "next" or "same" line after control structures.'))->setAllowedValues([self::LINE_NEXT, self::LINE_SAME])->setDefault(self::LINE_SAME)->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('position_after_anonymous_constructs', 'whether the opening brace should be placed on "next" or "same" line after anonymous constructs (anonymous classes and lambda functions).'))->setAllowedValues([self::LINE_NEXT, self::LINE_SAME])->setDefault(self::LINE_SAME)->getOption()]);
    }
    protected function createProxyFixers() : array
    {
        return [new \PhpCsFixer\Fixer\LanguageConstruct\DeclareParenthesesFixer()];
    }
    private function fixCommentBeforeBrace(\PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        $controlTokens = $this->getControlTokens();
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];
            if ($token->isGivenKind($controlTokens)) {
                $prevIndex = $this->findParenthesisEnd($tokens, $index);
            } elseif ($token->isGivenKind(\T_FUNCTION) && $tokensAnalyzer->isLambda($index) || $token->isGivenKind(\T_CLASS) && $tokensAnalyzer->isAnonymousClass($index)) {
                $prevIndex = $tokens->getNextTokenOfKind($index, ['{']);
                $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
            } else {
                continue;
            }
            $commentIndex = $tokens->getNextNonWhitespace($prevIndex);
            $commentToken = $tokens[$commentIndex];
            if (!$commentToken->isGivenKind(\T_COMMENT) || 0 === \strpos($commentToken->getContent(), '/*')) {
                continue;
            }
            $braceIndex = $tokens->getNextMeaningfulToken($commentIndex);
            $braceToken = $tokens[$braceIndex];
            if (!$braceToken->equals('{')) {
                continue;
            }
            /** @var Token $tokenTmp */
            $tokenTmp = $tokens[$braceIndex];
            $newBraceIndex = $prevIndex + 1;
            for ($i = $braceIndex; $i > $newBraceIndex; --$i) {
                // we might be moving one white space next to another, these have to be merged
                /** @var Token $previousToken */
                $previousToken = $tokens[$i - 1];
                $tokens[$i] = $previousToken;
                if ($tokens[$i]->isWhitespace() && $tokens[$i + 1]->isWhitespace()) {
                    $tokens[$i] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $tokens[$i]->getContent() . $tokens[$i + 1]->getContent()]);
                    $tokens->clearAt($i + 1);
                }
            }
            $tokens[$newBraceIndex] = $tokenTmp;
            $c = $tokens[$braceIndex]->getContent();
            if (\substr_count($c, "\n") > 1) {
                // left trim till last line break
                $tokens[$braceIndex] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, \substr($c, \strrpos($c, "\n"))]);
            }
        }
    }
    private function fixControlContinuationBraces(\PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $controlContinuationTokens = $this->getControlContinuationTokens();
        for ($index = \count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind($controlContinuationTokens)) {
                continue;
            }
            $prevIndex = $tokens->getPrevNonWhitespace($index);
            $prevToken = $tokens[$prevIndex];
            if (!$prevToken->equals('}')) {
                continue;
            }
            $tokens->ensureWhitespaceAtIndex($index - 1, 1, self::LINE_NEXT === $this->configuration['position_after_control_structures'] ? $this->whitespacesConfig->getLineEnding() . \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $index) : ' ');
        }
    }
    private function fixDoWhile(\PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = \count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_DO)) {
                continue;
            }
            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
            $startBraceIndex = $tokens->getNextNonWhitespace($parenthesisEndIndex);
            $endBraceIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $startBraceIndex);
            $nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($endBraceIndex);
            $nextNonWhitespaceToken = $tokens[$nextNonWhitespaceIndex];
            if (!$nextNonWhitespaceToken->isGivenKind(\T_WHILE)) {
                continue;
            }
            $tokens->ensureWhitespaceAtIndex($nextNonWhitespaceIndex - 1, 1, ' ');
        }
    }
    private function fixIndents(\PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $classyTokens = \PhpCsFixer\Tokenizer\Token::getClassyTokenKinds();
        $classyAndFunctionTokens = \array_merge([\T_FUNCTION], $classyTokens);
        $controlTokens = $this->getControlTokens();
        $indentTokens = \array_filter(\array_merge($classyAndFunctionTokens, $controlTokens), static function (int $item) {
            return \T_SWITCH !== $item;
        });
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($index = 0, $limit = \count($tokens); $index < $limit; ++$index) {
            $token = $tokens[$index];
            // if token is not a structure element - continue
            if (!$token->isGivenKind($indentTokens)) {
                continue;
            }
            // do not change indent for `while` in `do ... while ...`
            if ($token->isGivenKind(\T_WHILE) && $tokensAnalyzer->isWhilePartOfDoWhile($index)) {
                continue;
            }
            if ($this->configuration['allow_single_line_anonymous_class_with_empty_body'] && $token->isGivenKind(\T_CLASS)) {
                $prevIndex = $tokens->getPrevMeaningfulToken($index);
                if ($tokens[$prevIndex]->isGivenKind(\T_NEW)) {
                    $braceStartIndex = $tokens->getNextTokenOfKind($index, ['{']);
                    $braceEndIndex = $tokens->getNextMeaningfulToken($braceStartIndex);
                    if ('}' === $tokens[$braceEndIndex]->getContent() && !$this->isMultilined($tokens, $index, $braceEndIndex)) {
                        $index = $braceEndIndex;
                        continue;
                    }
                }
            }
            if ($this->configuration['allow_single_line_closure'] && $token->isGivenKind(\T_FUNCTION) && $tokensAnalyzer->isLambda($index)) {
                $braceEndIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $tokens->getNextTokenOfKind($index, ['{']));
                if (!$this->isMultilined($tokens, $index, $braceEndIndex)) {
                    $index = $braceEndIndex;
                    continue;
                }
            }
            if ($token->isGivenKind($classyAndFunctionTokens)) {
                $startBraceIndex = $tokens->getNextTokenOfKind($index, [';', '{']);
                $startBraceToken = $tokens[$startBraceIndex];
            } else {
                $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
                $startBraceIndex = $tokens->getNextNonWhitespace($parenthesisEndIndex);
                $startBraceToken = $tokens[$startBraceIndex];
            }
            // structure without braces block - nothing to do, e.g. do { } while (true);
            if (!$startBraceToken->equals('{')) {
                continue;
            }
            $nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($startBraceIndex, " \t");
            $nextNonWhitespace = $tokens[$nextNonWhitespaceIndex];
            /* if CLOSE_TAG is after { on the same line, do not indent. e.g. <?php if ($condition) { ?> */
            if ($nextNonWhitespace->isGivenKind(\T_CLOSE_TAG)) {
                continue;
            }
            /* if CLOSE_TAG is after { on the next line and a comment on this line, do not indent. e.g. <?php if ($condition) { // \n?> */
            if ($nextNonWhitespace->isComment() && $tokens[$tokens->getNextMeaningfulToken($nextNonWhitespaceIndex)]->isGivenKind(\T_CLOSE_TAG)) {
                continue;
            }
            $endBraceIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $startBraceIndex);
            $indent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $index);
            // fix indent near closing brace
            $tokens->ensureWhitespaceAtIndex($endBraceIndex - 1, 1, $this->whitespacesConfig->getLineEnding() . $indent);
            // fix indent between braces
            $lastCommaIndex = $tokens->getPrevTokenOfKind($endBraceIndex - 1, [';', '}']);
            $nestLevel = 1;
            for ($nestIndex = $lastCommaIndex; $nestIndex >= $startBraceIndex; --$nestIndex) {
                $nestToken = $tokens[$nestIndex];
                if ($nestToken->equalsAny([')', [\PhpCsFixer\Tokenizer\CT::T_BRACE_CLASS_INSTANTIATION_CLOSE]])) {
                    $nestIndex = $tokens->findBlockStart($nestToken->equals(')') ? \PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE : \PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_BRACE_CLASS_INSTANTIATION, $nestIndex);
                    continue;
                }
                if (1 === $nestLevel) {
                    // Next token is the beginning of a line that can be indented when
                    // the current token is a `;`, a `}` or the opening `{` of current
                    // scope. Current token may also be a comment that follows `;` or
                    // `}`, in which case indentation will only be fixed if this
                    // comment is followed by a newline.
                    $nextLineCanBeIndented = \false;
                    if ($nestToken->equalsAny([';', '}'])) {
                        $nextLineCanBeIndented = \true;
                    } elseif ($this->isCommentWithFixableIndentation($tokens, $nestIndex)) {
                        for ($i = $nestIndex; $i > $startBraceIndex; --$i) {
                            if ($tokens[$i]->equalsAny([';', '}'])) {
                                $nextLineCanBeIndented = \true;
                                break;
                            }
                            if (!$tokens[$i]->isWhitespace() && !$tokens[$i]->isComment()) {
                                break;
                            }
                        }
                        if ($nextLineCanBeIndented || $i === $startBraceIndex) {
                            $nextToken = $tokens[$nestIndex + 1];
                            $nextLineCanBeIndented = $nextToken->isWhitespace() && 1 === \PhpCsFixer\Preg::match('/\\R/', $nextToken->getContent());
                        }
                    }
                    if (!$nextLineCanBeIndented) {
                        continue;
                    }
                    $nextNonWhitespaceNestIndex = $tokens->getNextNonWhitespace($nestIndex);
                    $nextNonWhitespaceNestToken = $tokens[$nextNonWhitespaceNestIndex];
                    if (!($nextNonWhitespaceNestToken->isComment() && (!$tokens[$nextNonWhitespaceNestIndex - 1]->isWhitespace() || !\PhpCsFixer\Preg::match('/\\R/', $tokens[$nextNonWhitespaceNestIndex - 1]->getContent()))) && !($nestToken->equals('}') && $nextNonWhitespaceNestToken->equalsAny([';', ',', ']', [\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE]])) && !($nestToken->equals('}') && $nextNonWhitespaceNestToken->equals('(')) && !($nestToken->equals('}') && $tokens[$nestIndex - 1]->equalsAny(['"', "'", [\T_CONSTANT_ENCAPSED_STRING], [\T_VARIABLE]])) && !($tokens[$nestIndex - 1]->isGivenKind(\T_END_HEREDOC) && $nextNonWhitespaceNestToken->isGivenKind(\T_CLOSE_TAG))) {
                        if (self::LINE_NEXT !== $this->configuration['position_after_control_structures'] && $nextNonWhitespaceNestToken->isGivenKind($this->getControlContinuationTokens()) && !$tokens[$tokens->getPrevNonWhitespace($nextNonWhitespaceNestIndex)]->isComment() || $nextNonWhitespaceNestToken->isGivenKind(\T_CLOSE_TAG) || self::LINE_NEXT !== $this->configuration['position_after_control_structures'] && $nextNonWhitespaceNestToken->isGivenKind(\T_WHILE) && $tokensAnalyzer->isWhilePartOfDoWhile($nextNonWhitespaceNestIndex)) {
                            $whitespace = ' ';
                        } else {
                            $nextToken = $tokens[$nestIndex + 1];
                            $nextWhitespace = '';
                            if ($nextToken->isWhitespace()) {
                                $nextWhitespace = \rtrim($nextToken->getContent(), " \t");
                                if ('' !== $nextWhitespace) {
                                    $nextWhitespace = \PhpCsFixer\Preg::replace(\sprintf('/%s$/', $this->whitespacesConfig->getLineEnding()), '', $nextWhitespace, 1);
                                }
                            }
                            $whitespace = $nextWhitespace . $this->whitespacesConfig->getLineEnding() . $indent;
                            if (!$nextNonWhitespaceNestToken->equals('}')) {
                                $determineIsIndentableBlockContent = static function (int $contentIndex) use($tokens) : bool {
                                    if (!$tokens[$contentIndex]->isComment()) {
                                        return \true;
                                    }
                                    if (!$tokens[$tokens->getPrevMeaningfulToken($contentIndex)]->equals(';')) {
                                        return \true;
                                    }
                                    $nextIndex = $tokens->getNextMeaningfulToken($contentIndex);
                                    if (!$tokens[$nextIndex]->equals('}')) {
                                        return \true;
                                    }
                                    $nextNextIndex = $tokens->getNextMeaningfulToken($nextIndex);
                                    if (null === $nextNextIndex) {
                                        return \true;
                                    }
                                    if ($tokens[$nextNextIndex]->equalsAny([[\T_ELSE], [\T_ELSEIF], ','])) {
                                        return \false;
                                    }
                                    return \true;
                                };
                                // add extra indent only if current content is not a comment for content outside of current block
                                if ($determineIsIndentableBlockContent($nestIndex + 2)) {
                                    $whitespace .= $this->whitespacesConfig->getIndent();
                                }
                            }
                        }
                        $this->ensureWhitespaceAtIndexAndIndentMultilineComment($tokens, $nestIndex + 1, $whitespace);
                    }
                }
                if ($nestToken->equals('}')) {
                    ++$nestLevel;
                    continue;
                }
                if ($nestToken->equals('{')) {
                    --$nestLevel;
                    continue;
                }
            }
            // fix indent near opening brace
            if (isset($tokens[$startBraceIndex + 2]) && $tokens[$startBraceIndex + 2]->equals('}')) {
                $tokens->ensureWhitespaceAtIndex($startBraceIndex + 1, 0, $this->whitespacesConfig->getLineEnding() . $indent);
            } else {
                $nextToken = $tokens[$startBraceIndex + 1];
                $nextNonWhitespaceToken = $tokens[$tokens->getNextNonWhitespace($startBraceIndex)];
                // set indent only if it is not a case, when comment is following { on same line
                if (!$nextNonWhitespaceToken->isComment() || $nextToken->isWhitespace() && 1 === \substr_count($nextToken->getContent(), "\n")) {
                    $this->ensureWhitespaceAtIndexAndIndentMultilineComment($tokens, $startBraceIndex + 1, $this->whitespacesConfig->getLineEnding() . $indent . $this->whitespacesConfig->getIndent());
                }
            }
            if ($token->isGivenKind($classyTokens) && !$tokensAnalyzer->isAnonymousClass($index)) {
                if (self::LINE_SAME === $this->configuration['position_after_functions_and_oop_constructs'] && !$tokens[$tokens->getPrevNonWhitespace($startBraceIndex)]->isComment()) {
                    $ensuredWhitespace = ' ';
                } else {
                    $ensuredWhitespace = $this->whitespacesConfig->getLineEnding() . $indent;
                }
                $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, $ensuredWhitespace);
            } elseif ($token->isGivenKind(\T_FUNCTION) && !$tokensAnalyzer->isLambda($index) || (self::LINE_NEXT === $this->configuration['position_after_control_structures'] && $token->isGivenKind($controlTokens) || self::LINE_NEXT === $this->configuration['position_after_anonymous_constructs'] && ($token->isGivenKind(\T_FUNCTION) && $tokensAnalyzer->isLambda($index) || $token->isGivenKind(\T_CLASS) && $tokensAnalyzer->isAnonymousClass($index)))) {
                $isAnonymousClass = $token->isGivenKind($classyTokens) && $tokensAnalyzer->isAnonymousClass($index);
                $closingParenthesisIndex = $tokens->getPrevTokenOfKind($startBraceIndex, [')']);
                if (null === $closingParenthesisIndex && !$isAnonymousClass) {
                    continue;
                }
                if (!$isAnonymousClass && $tokens[$closingParenthesisIndex - 1]->isWhitespace() && \false !== \strpos($tokens[$closingParenthesisIndex - 1]->getContent(), "\n")) {
                    if (!$tokens[$startBraceIndex - 2]->isComment()) {
                        $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
                    }
                } else {
                    if (self::LINE_SAME === $this->configuration['position_after_functions_and_oop_constructs'] && ($token->isGivenKind(\T_FUNCTION) && !$tokensAnalyzer->isLambda($index) || $token->isGivenKind($classyTokens) && !$tokensAnalyzer->isAnonymousClass($index)) && !$tokens[$tokens->getPrevNonWhitespace($startBraceIndex)]->isComment()) {
                        $ensuredWhitespace = ' ';
                    } else {
                        $ensuredWhitespace = $this->whitespacesConfig->getLineEnding() . $indent;
                    }
                    $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, $ensuredWhitespace);
                }
            } else {
                $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
            }
            // reset loop limit due to collection change
            $limit = \count($tokens);
        }
    }
    private function fixMissingControlBraces(\PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $controlTokens = $this->getControlTokens();
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind($controlTokens)) {
                continue;
            }
            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
            $nextAfterParenthesisEndIndex = $tokens->getNextMeaningfulToken($parenthesisEndIndex);
            $tokenAfterParenthesis = $tokens[$nextAfterParenthesisEndIndex];
            // if Token after parenthesis is { then we do not need to insert brace, but to fix whitespace before it
            if ($tokenAfterParenthesis->equals('{') && self::LINE_SAME === $this->configuration['position_after_control_structures']) {
                $tokens->ensureWhitespaceAtIndex($parenthesisEndIndex + 1, 0, ' ');
                continue;
            }
            // do not add braces for cases:
            // - structure without block, e.g. while ($iter->next());
            // - structure with block, e.g. while ($i) {...}, while ($i) : {...} endwhile;
            if ($tokenAfterParenthesis->equalsAny([';', '{', ':'])) {
                continue;
            }
            // do not add for 'short if' followed by alternative loop, for example: if ($a) while ($b): ? > X < ?php endwhile; ? >
            // or 'short if' after an alternative loop, for example:  foreach ($arr as $index => $item) if ($item):
            if ($tokenAfterParenthesis->isGivenKind([\T_FOR, \T_FOREACH, \T_SWITCH, \T_WHILE, \T_IF])) {
                $tokenAfterParenthesisBlockEnd = $tokens->findBlockEnd(
                    // go to ')'
                    \PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
                    $tokens->getNextMeaningfulToken($nextAfterParenthesisEndIndex)
                );
                if ($tokens[$tokens->getNextMeaningfulToken($tokenAfterParenthesisBlockEnd)]->equals(':')) {
                    continue;
                }
            }
            $statementEndIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);
            // insert closing brace
            $tokens->insertAt($statementEndIndex + 1, [new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']), new \PhpCsFixer\Tokenizer\Token('}')]);
            // insert missing `;` if needed
            if (!$tokens[$statementEndIndex]->equalsAny([';', '}'])) {
                $tokens->insertAt($statementEndIndex + 1, new \PhpCsFixer\Tokenizer\Token(';'));
            }
            // insert opening brace
            $tokens->insertAt($parenthesisEndIndex + 1, new \PhpCsFixer\Tokenizer\Token('{'));
            $tokens->ensureWhitespaceAtIndex($parenthesisEndIndex + 1, 0, ' ');
        }
    }
    private function fixSpaceAroundToken(\PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $controlTokens = $this->getControlTokens();
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];
            // Declare tokens don't follow the same rules are other control statements
            if ($token->isGivenKind(\T_DECLARE)) {
                continue;
                // delegated to DeclareParenthesesFixer
            }
            if ($token->isGivenKind($controlTokens) || $token->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_USE_LAMBDA)) {
                $nextNonWhitespaceIndex = $tokens->getNextNonWhitespace($index);
                if (!$tokens[$nextNonWhitespaceIndex]->equals(':')) {
                    $tokens->ensureWhitespaceAtIndex($index + 1, 0, self::LINE_NEXT === $this->configuration['position_after_control_structures'] && !$tokens[$nextNonWhitespaceIndex]->equals('(') ? $this->whitespacesConfig->getLineEnding() . \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $index) : ' ');
                }
                $prevToken = $tokens[$index - 1];
                if (!$prevToken->isWhitespace() && !$prevToken->isComment() && !$prevToken->isGivenKind(\T_OPEN_TAG)) {
                    $tokens->ensureWhitespaceAtIndex($index - 1, 1, ' ');
                }
            }
        }
    }
    private function findParenthesisEnd(\PhpCsFixer\Tokenizer\Tokens $tokens, int $structureTokenIndex) : int
    {
        $nextIndex = $tokens->getNextMeaningfulToken($structureTokenIndex);
        $nextToken = $tokens[$nextIndex];
        // return if next token is not opening parenthesis
        if (!$nextToken->equals('(')) {
            return $structureTokenIndex;
        }
        return $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);
    }
    private function findStatementEnd(\PhpCsFixer\Tokenizer\Tokens $tokens, int $parenthesisEndIndex) : int
    {
        $nextIndex = $tokens->getNextMeaningfulToken($parenthesisEndIndex);
        $nextToken = $tokens[$nextIndex];
        if (!$nextToken) {
            return $parenthesisEndIndex;
        }
        if ($nextToken->equals('{')) {
            return $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $nextIndex);
        }
        if ($nextToken->isGivenKind($this->getControlTokens())) {
            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $nextIndex);
            $endIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);
            if ($nextToken->isGivenKind([\T_IF, \T_TRY, \T_DO])) {
                $openingTokenKind = $nextToken->getId();
                while (\true) {
                    $nextIndex = $tokens->getNextMeaningfulToken($endIndex);
                    $nextToken = isset($nextIndex) ? $tokens[$nextIndex] : null;
                    if ($nextToken && $nextToken->isGivenKind($this->getControlContinuationTokensForOpeningToken($openingTokenKind))) {
                        $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $nextIndex);
                        $endIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);
                        if ($nextToken->isGivenKind($this->getFinalControlContinuationTokensForOpeningToken($openingTokenKind))) {
                            return $endIndex;
                        }
                    } else {
                        break;
                    }
                }
            }
            return $endIndex;
        }
        $index = $parenthesisEndIndex;
        while (\true) {
            $token = $tokens[++$index];
            // if there is some block in statement (eg lambda function) we need to skip it
            if ($token->equals('{')) {
                $index = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                continue;
            }
            if ($token->equals(';')) {
                return $index;
            }
            if ($token->isGivenKind(\T_CLOSE_TAG)) {
                return $tokens->getPrevNonWhitespace($index);
            }
        }
    }
    private function getControlTokens() : array
    {
        static $tokens = [\T_DECLARE, \T_DO, \T_ELSE, \T_ELSEIF, \T_FINALLY, \T_FOR, \T_FOREACH, \T_IF, \T_WHILE, \T_TRY, \T_CATCH, \T_SWITCH];
        // @TODO: drop condition when PHP 8.0+ is required
        if (\defined('T_MATCH')) {
            $tokens['match'] = \T_MATCH;
        }
        return $tokens;
    }
    private function getControlContinuationTokens() : array
    {
        static $tokens = [\T_CATCH, \T_ELSE, \T_ELSEIF, \T_FINALLY];
        return $tokens;
    }
    private function getControlContinuationTokensForOpeningToken(int $openingTokenKind) : array
    {
        if (\T_IF === $openingTokenKind) {
            return [\T_ELSE, \T_ELSEIF];
        }
        if (\T_DO === $openingTokenKind) {
            return [\T_WHILE];
        }
        if (\T_TRY === $openingTokenKind) {
            return [\T_CATCH, \T_FINALLY];
        }
        return [];
    }
    private function getFinalControlContinuationTokensForOpeningToken(int $openingTokenKind) : array
    {
        if (\T_IF === $openingTokenKind) {
            return [\T_ELSE];
        }
        if (\T_TRY === $openingTokenKind) {
            return [\T_FINALLY];
        }
        return [];
    }
    private function ensureWhitespaceAtIndexAndIndentMultilineComment(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index, string $whitespace) : void
    {
        if ($tokens[$index]->isWhitespace()) {
            $nextTokenIndex = $tokens->getNextNonWhitespace($index);
        } else {
            $nextTokenIndex = $index;
        }
        $nextToken = $tokens[$nextTokenIndex];
        if ($nextToken->isComment()) {
            $previousToken = $tokens[$nextTokenIndex - 1];
            $nextTokenContent = $nextToken->getContent();
            // do not indent inline comments used to comment out unused code
            if ($previousToken->isWhitespace() && 1 === \PhpCsFixer\Preg::match('/\\R$/', $previousToken->getContent()) && (0 === \strpos($nextTokenContent, '//' . $this->whitespacesConfig->getIndent()) || '//' === $nextTokenContent || (0 === \strpos($nextTokenContent, '#' . $this->whitespacesConfig->getIndent()) || '#' === $nextTokenContent))) {
                return;
            }
            $tokens[$nextTokenIndex] = new \PhpCsFixer\Tokenizer\Token([$nextToken->getId(), \PhpCsFixer\Preg::replace('/(\\R)' . \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $nextTokenIndex) . '(\\h*\\S+.*)/', '$1' . \PhpCsFixer\Preg::replace('/^.*\\R(\\h*)$/s', '$1', $whitespace) . '$2', $nextToken->getContent())]);
        }
        $tokens->ensureWhitespaceAtIndex($index, 0, $whitespace);
    }
    private function isMultilined(\PhpCsFixer\Tokenizer\Tokens $tokens, int $startParenthesisIndex, int $endParenthesisIndex) : bool
    {
        for ($i = $startParenthesisIndex; $i < $endParenthesisIndex; ++$i) {
            if (\false !== \strpos($tokens[$i]->getContent(), "\n")) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Returns whether the token at given index is a comment whose indentation
     * can be fixed.
     *
     * Indentation of a comment is not changed when the comment is part of a
     * multi-line message whose lines are all single-line comments and at least
     * one line has meaningful content.
     */
    private function isCommentWithFixableIndentation(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        if (!$tokens[$index]->isComment()) {
            return \false;
        }
        if (0 === \strpos($tokens[$index]->getContent(), '/*')) {
            return \true;
        }
        $firstCommentIndex = $index;
        while (\true) {
            $i = $this->getSiblingContinuousSingleLineComment($tokens, $firstCommentIndex, \false);
            if (null === $i) {
                break;
            }
            $firstCommentIndex = $i;
        }
        $lastCommentIndex = $index;
        while (\true) {
            $i = $this->getSiblingContinuousSingleLineComment($tokens, $lastCommentIndex, \true);
            if (null === $i) {
                break;
            }
            $lastCommentIndex = $i;
        }
        if ($firstCommentIndex === $lastCommentIndex) {
            return \true;
        }
        for ($i = $firstCommentIndex + 1; $i < $lastCommentIndex; ++$i) {
            if (!$tokens[$i]->isWhitespace() && !$tokens[$i]->isComment()) {
                return \false;
            }
        }
        return \true;
    }
    private function getSiblingContinuousSingleLineComment(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index, bool $after) : ?int
    {
        $siblingIndex = $index;
        do {
            $siblingIndex = $tokens->getTokenOfKindSibling($siblingIndex, $after ? 1 : -1, [[\T_COMMENT]]);
            if (null === $siblingIndex) {
                return null;
            }
        } while (0 === \strpos($tokens[$siblingIndex]->getContent(), '/*'));
        $newLines = 0;
        for ($i = \min($siblingIndex, $index) + 1, $max = \max($siblingIndex, $index); $i < $max; ++$i) {
            if ($tokens[$i]->isWhitespace() && \PhpCsFixer\Preg::match('/\\R/', $tokens[$i]->getContent())) {
                if (1 === $newLines || \PhpCsFixer\Preg::match('/\\R.*\\R/', $tokens[$i]->getContent())) {
                    return null;
                }
                ++$newLines;
            }
        }
        return $siblingIndex;
    }
}
