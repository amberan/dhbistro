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
namespace PhpCsFixer\Indicator;

use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @internal
 */
final class PhpUnitTestCaseIndicator
{
    public function isPhpUnitClass(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        if (!$tokens[$index]->isGivenKind(\T_CLASS)) {
            throw new \LogicException(\sprintf('No "T_CLASS" at given index %d, got "%s".', $index, $tokens[$index]->getName()));
        }
        $index = $tokens->getNextMeaningfulToken($index);
        if (0 !== \PhpCsFixer\Preg::match('/(?:Test|TestCase)$/', $tokens[$index]->getContent())) {
            return \true;
        }
        while (null !== ($index = $tokens->getNextMeaningfulToken($index))) {
            if ($tokens[$index]->equals('{')) {
                break;
                // end of class signature
            }
            if (!$tokens[$index]->isGivenKind(\T_STRING)) {
                continue;
                // not part of extends nor part of implements; so continue
            }
            if (0 !== \PhpCsFixer\Preg::match('/(?:Test|TestCase)(?:Interface)?$/', $tokens[$index]->getContent())) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @return \Generator array of [int start, int end] indexes from sooner to later classes
     */
    public function findPhpUnitClasses(\PhpCsFixer\Tokenizer\Tokens $tokens) : \Generator
    {
        for ($index = $tokens->count() - 1; $tokens->offsetExists($index); --$index) {
            if (!$tokens[$index]->isGivenKind(\T_CLASS) || !$this->isPhpUnitClass($tokens, $index)) {
                continue;
            }
            $startIndex = $tokens->getNextTokenOfKind($index, ['{'], \false);
            if (null === $startIndex) {
                return;
            }
            $endIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $startIndex);
            (yield [$startIndex, $endIndex]);
        }
    }
}
