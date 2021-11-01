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
namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @internal
 */
final class ClassyAnalyzer
{
    public function isClassyInvocation(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        $token = $tokens[$index];
        if (!$token->isGivenKind(\T_STRING)) {
            throw new \LogicException(\sprintf('No T_STRING at given index %d, got "%s".', $index, $tokens[$index]->getName()));
        }
        if (\in_array(\strtolower($token->getContent()), ['bool', 'float', 'int', 'iterable', 'object', 'parent', 'self', 'string', 'void', 'null', 'false'], \true)) {
            return \false;
        }
        $next = $tokens->getNextMeaningfulToken($index);
        $nextToken = $tokens[$next];
        if ($nextToken->isGivenKind(\T_NS_SEPARATOR)) {
            return \false;
        }
        if ($nextToken->isGivenKind([\T_DOUBLE_COLON, \T_ELLIPSIS, \PhpCsFixer\Tokenizer\CT::T_TYPE_ALTERNATION, \T_VARIABLE])) {
            return \true;
        }
        $prev = $tokens->getPrevMeaningfulToken($index);
        while ($tokens[$prev]->isGivenKind([\PhpCsFixer\Tokenizer\CT::T_NAMESPACE_OPERATOR, \T_NS_SEPARATOR, \T_STRING])) {
            $prev = $tokens->getPrevMeaningfulToken($prev);
        }
        $prevToken = $tokens[$prev];
        if ($prevToken->isGivenKind([\T_EXTENDS, \T_INSTANCEOF, \T_INSTEADOF, \T_IMPLEMENTS, \T_NEW, \PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE, \PhpCsFixer\Tokenizer\CT::T_TYPE_ALTERNATION, \PhpCsFixer\Tokenizer\CT::T_TYPE_COLON, \PhpCsFixer\Tokenizer\CT::T_USE_TRAIT])) {
            return \true;
        }
        if (\PhpCsFixer\Tokenizer\Analyzer\AttributeAnalyzer::isAttribute($tokens, $index)) {
            return \true;
        }
        // `Foo & $bar` could be:
        //   - function reference parameter: function baz(Foo & $bar) {}
        //   - bit operator: $x = Foo & $bar;
        if ($nextToken->equals('&') && $tokens[$tokens->getNextMeaningfulToken($next)]->isGivenKind(\T_VARIABLE)) {
            $checkIndex = $tokens->getPrevTokenOfKind($prev + 1, [';', '{', '}', [\T_FUNCTION], [\T_OPEN_TAG], [\T_OPEN_TAG_WITH_ECHO]]);
            return $tokens[$checkIndex]->isGivenKind(\T_FUNCTION);
        }
        if (!$prevToken->equals(',')) {
            return \false;
        }
        do {
            $prev = $tokens->getPrevMeaningfulToken($prev);
        } while ($tokens[$prev]->equalsAny([',', [\T_NS_SEPARATOR], [\T_STRING], [\PhpCsFixer\Tokenizer\CT::T_NAMESPACE_OPERATOR]]));
        return $tokens[$prev]->isGivenKind([\T_IMPLEMENTS, \PhpCsFixer\Tokenizer\CT::T_USE_TRAIT]);
    }
}
