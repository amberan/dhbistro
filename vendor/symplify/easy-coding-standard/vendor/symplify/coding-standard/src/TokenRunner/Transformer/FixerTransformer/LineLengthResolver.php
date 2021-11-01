<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use ECSPrefix20211002\Symplify\PackageBuilder\Configuration\StaticEolConfiguration;
final class LineLengthResolver
{
    /**
     * @param Tokens<Token> $tokens
     */
    public function getLengthFromStartEnd(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo) : int
    {
        $lineLength = 0;
        // compute from function to start of line
        $start = $blockInfo->getStart();
        while (!$this->isNewLineOrOpenTag($tokens, $start)) {
            $lineLength += \strlen($tokens[$start]->getContent());
            --$start;
            if (!isset($tokens[$start])) {
                break;
            }
        }
        // get spaces to first line
        $lineLength += \strlen($tokens[$start]->getContent());
        // get length from start of function till end of arguments - with spaces as one
        $lineLength += $this->getLengthFromFunctionStartToEndOfArguments($blockInfo, $tokens);
        // get length from end or arguments to first line break
        $lineLength += $this->getLengthFromEndOfArgumentToLineBreak($blockInfo, $tokens);
        return $lineLength;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function isNewLineOrOpenTag(\PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : bool
    {
        /** @var Token $currentToken */
        $currentToken = $tokens[$position];
        if (\strncmp($currentToken->getContent(), \ECSPrefix20211002\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar(), \strlen(\ECSPrefix20211002\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar())) === 0) {
            return \true;
        }
        return $currentToken->isGivenKind(\T_OPEN_TAG);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function getLengthFromFunctionStartToEndOfArguments(\Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo, \PhpCsFixer\Tokenizer\Tokens $tokens) : int
    {
        $length = 0;
        $start = $blockInfo->getStart();
        while ($start < $blockInfo->getEnd()) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$start];
            if ($currentToken->isGivenKind(\T_WHITESPACE)) {
                ++$length;
                ++$start;
                continue;
            }
            $length += \strlen($currentToken->getContent());
            ++$start;
            if (!isset($tokens[$start])) {
                break;
            }
        }
        return $length;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function getLengthFromEndOfArgumentToLineBreak(\Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo, \PhpCsFixer\Tokenizer\Tokens $tokens) : int
    {
        $length = 0;
        $end = $blockInfo->getEnd();
        /** @var Token $currentToken */
        $currentToken = $tokens[$end];
        while (\strncmp($currentToken->getContent(), \ECSPrefix20211002\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar(), \strlen(\ECSPrefix20211002\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar())) !== 0) {
            $length += \strlen($currentToken->getContent());
            ++$end;
            if (!isset($tokens[$end])) {
                break;
            }
            /** @var Token $currentToken */
            $currentToken = $tokens[$end];
        }
        return $length;
    }
}
