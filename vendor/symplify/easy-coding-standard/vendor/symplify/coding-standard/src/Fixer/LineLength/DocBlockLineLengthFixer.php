<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\LineLength;

use ECSPrefix20211002\Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\ValueObjectFactory\DocBlockLinesFactory;
use ECSPrefix20211002\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use ECSPrefix20211002\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use ECSPrefix20211002\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\LineLength\DocBlockLineLengthFixer\DocBlockLineLengthFixerTest
 */
final class DocBlockLineLengthFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20211002\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface, \PhpCsFixer\Fixer\ConfigurableFixerInterface, \ECSPrefix20211002\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @api
     * @var string
     */
    public const LINE_LENGTH = 'line_length';
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Docblock lenght should fit expected width';
    /**
     * @see https://regex101.com/r/DNWfB6/1
     * @var string
     */
    private const INDENTATION_BEFORE_ASTERISK_REGEX = '/^(?<' . self::INDENTATION_PART . '>\\s*) \\*/m';
    /**
     * @var string
     */
    private const INDENTATION_PART = 'indentation_part';
    /**
     * @var int
     */
    private const DEFAULT_LINE_LENGHT = 120;
    /**
     * @var int
     */
    private $lineLength = self::DEFAULT_LINE_LENGHT;
    /**
     * @var \Symplify\CodingStandard\ValueObjectFactory\DocBlockLinesFactory
     */
    private $docBlockLinesFactory;
    public function __construct(\Symplify\CodingStandard\ValueObjectFactory\DocBlockLinesFactory $docBlockLinesFactory)
    {
        $this->docBlockLinesFactory = $docBlockLinesFactory;
    }
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        // function arguments, function call parameters, lambda use()
        for ($position = \count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $docBlock = $token->getContent();
            $docBlockLines = $this->docBlockLinesFactory->createFromDocBlock($docBlock);
            // The available line length is the configured line length, minus the existing indentation, minus ' * '
            $indentationString = $this->resolveIndentationStringFor($docBlock);
            $maximumLineLength = $this->lineLength - \strlen($indentationString) - 3;
            $descriptionLines = $docBlockLines->getDescriptionLines();
            if ($descriptionLines === []) {
                continue;
            }
            if ($docBlockLines->hasListDescriptionLines()) {
                continue;
            }
            $paragraphs = $this->extractParagraphsFromDescriptionLines($descriptionLines);
            $lineWrappedParagraphs = $this->wrapParagraphs($paragraphs, $maximumLineLength);
            $wrappedDescription = \implode(\PHP_EOL . \PHP_EOL, $lineWrappedParagraphs);
            $otherLines = $docBlockLines->getOtherLines();
            if ($otherLines !== []) {
                $wrappedDescription .= "\n";
            }
            $reformattedLines = \array_merge($this->getLines($wrappedDescription), $otherLines);
            $newDocBlockContent = $this->formatLinesAsDocBlockContent($reformattedLines, $indentationString);
            if ($docBlock === $newDocBlockContent) {
                continue;
            }
            $tokens[$position] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $newDocBlockContent]);
        }
    }
    /**
     * @param array<string, int> $configuration
     */
    public function configure(array $configuration) : void
    {
        $this->lineLength = $configuration[self::LINE_LENGTH] ?? self::DEFAULT_LINE_LENGHT;
    }
    public function getRuleDefinition() : \ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample(<<<'CODE_SAMPLE'
/**
 * Super long doc block description
 */
function some()
{
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
/**
 * Super long doc
 * block description
 */
function some()
{
}
CODE_SAMPLE
, [self::LINE_LENGTH => 40])]);
    }
    public function getConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        throw new \ECSPrefix20211002\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
    }
    private function resolveIndentationStringFor(string $docBlock) : string
    {
        $matches = \ECSPrefix20211002\Nette\Utils\Strings::match($docBlock, self::INDENTATION_BEFORE_ASTERISK_REGEX);
        return $matches[self::INDENTATION_PART] ?? '';
    }
    private function formatLinesAsDocBlockContent(array $docBlockLines, string $indentationString) : string
    {
        foreach ($docBlockLines as $index => $docBlockLine) {
            $docBlockLines[$index] = $indentationString . ' *' . ($docBlockLine !== '' ? ' ' : '') . $docBlockLine;
        }
        \array_unshift($docBlockLines, '/**');
        $docBlockLines[] = $indentationString . ' */';
        return \implode(\PHP_EOL, $docBlockLines);
    }
    /**
     * @return array<string>
     */
    private function extractParagraphsFromDescriptionLines(array $descriptionLines) : array
    {
        $paragraphLines = [];
        $paragraphIndex = 0;
        foreach ($descriptionLines as $line) {
            if (!isset($paragraphLines[$paragraphIndex])) {
                $paragraphLines[$paragraphIndex] = [];
            }
            $line = \trim($line);
            if ($line === '') {
                ++$paragraphIndex;
            } else {
                $paragraphLines[$paragraphIndex][] = $line;
            }
        }
        return \array_map(function (array $lines) : string {
            return \implode(' ', $lines);
        }, $paragraphLines);
    }
    /**
     * @return string[]
     */
    private function getLines(string $string) : array
    {
        return \explode(\PHP_EOL, $string);
    }
    /**
     * @param string[] $lines
     * @return string[]
     */
    private function wrapParagraphs(array $lines, int $maximumLineLength) : array
    {
        $wrappedLines = [];
        foreach ($lines as $line) {
            $wrappedLines[] = \wordwrap($line, $maximumLineLength);
        }
        return $wrappedLines;
    }
}
