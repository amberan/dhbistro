<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Annotation;

use ECSPrefix20211002\Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token as DoctrineAnnotationToken;
use PhpCsFixer\Doctrine\Annotation\Tokens as DoctrineAnnotationTokens;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationElementAnalyzer;
use Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationNameResolver;
use ECSPrefix20211002\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use ECSPrefix20211002\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use ECSPrefix20211002\Webmozart\Assert\Assert;
final class DoctrineAnnotationNestedBracketsFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20211002\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface, \ECSPrefix20211002\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ANNOTATION_CLASSES = 'annotation_classes';
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Adds nested curly brackets to defined annotations, see https://github.com/doctrine/annotations/issues/418';
    /**
     * @var string[]
     */
    private $annotationClasses = [];
    /**
     * @var \Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationElementAnalyzer
     */
    private $doctrineAnnotationElementAnalyzer;
    /**
     * @var \Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationNameResolver
     */
    private $doctrineAnnotationNameResolver;
    /**
     * @var \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer
     */
    private $namespaceUsesAnalyzer;
    public function __construct(\Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationElementAnalyzer $doctrineAnnotationElementAnalyzer, \Symplify\CodingStandard\TokenAnalyzer\DoctrineAnnotationNameResolver $doctrineAnnotationNameResolver, \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer $namespaceUsesAnalyzer)
    {
        $this->doctrineAnnotationElementAnalyzer = $doctrineAnnotationElementAnalyzer;
        $this->doctrineAnnotationNameResolver = $doctrineAnnotationNameResolver;
        $this->namespaceUsesAnalyzer = $namespaceUsesAnalyzer;
    }
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    public function getRuleDefinition() : \ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample(<<<'CODE_SAMPLE'
/**
* @MainAnnotation(
*     @NestedAnnotation(),
*     @NestedAnnotation(),
* )
*/
CODE_SAMPLE
, <<<'CODE_SAMPLE'
/**
* @MainAnnotation({
*     @NestedAnnotation(),
*     @NestedAnnotation(),
* })
*/
CODE_SAMPLE
, [self::ANNOTATION_CLASSES => ['MainAnnotation']])]);
    }
    /**
     * @param array<string, string[]> $configuration
     */
    public function configure(array $configuration) : void
    {
        $annotationsClasses = $configuration[self::ANNOTATION_CLASSES] ?? [];
        \ECSPrefix20211002\Webmozart\Assert\Assert::isArray($annotationsClasses);
        \ECSPrefix20211002\Webmozart\Assert\Assert::allString($annotationsClasses);
        $this->annotationClasses = $annotationsClasses;
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
        $useDeclarations = $this->namespaceUsesAnalyzer->getDeclarationsFromTokens($tokens);
        // fetch indexes one time, this is safe as we never add or remove a token during fixing
        /** @var Token[] $docCommentTokens */
        $docCommentTokens = $tokens->findGivenKind(\T_DOC_COMMENT);
        foreach ($docCommentTokens as $index => $docCommentToken) {
            $doctrineAnnotationTokens = \PhpCsFixer\Doctrine\Annotation\Tokens::createFromDocComment($docCommentToken, []);
            $this->fixAnnotations($doctrineAnnotationTokens, $useDeclarations);
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $doctrineAnnotationTokens->getCode()]);
        }
    }
    /**
     * @param DoctrineAnnotationTokens<DoctrineAnnotationToken> $doctrineAnnotationTokens
     */
    private function fixAnnotations(\PhpCsFixer\Doctrine\Annotation\Tokens $doctrineAnnotationTokens, array $useDeclarations) : void
    {
        foreach ($doctrineAnnotationTokens as $index => $token) {
            $isAtToken = $doctrineAnnotationTokens[$index]->isType(\ECSPrefix20211002\Doctrine\Common\Annotations\DocLexer::T_AT);
            if (!$isAtToken) {
                continue;
            }
            $annotationName = $this->doctrineAnnotationNameResolver->resolveName($doctrineAnnotationTokens, $index, $useDeclarations);
            if ($annotationName === null) {
                continue;
            }
            if (!\in_array($annotationName, $this->annotationClasses, \true)) {
                continue;
            }
            $closingBraceIndex = $doctrineAnnotationTokens->getAnnotationEnd($index);
            if ($closingBraceIndex === null) {
                continue;
            }
            $braceIndex = $doctrineAnnotationTokens->getNextMeaningfulToken($index + 1);
            if ($braceIndex === null) {
                continue;
            }
            /** @var DoctrineAnnotationToken $braceToken */
            $braceToken = $doctrineAnnotationTokens[$braceIndex];
            if (!$this->doctrineAnnotationElementAnalyzer->isOpeningBracketFollowedByAnnotation($braceToken, $doctrineAnnotationTokens, $braceIndex)) {
                continue;
            }
            // add closing brace
            $doctrineAnnotationTokens->insertAt($closingBraceIndex, new \PhpCsFixer\Doctrine\Annotation\Token(\ECSPrefix20211002\Doctrine\Common\Annotations\DocLexer::T_OPEN_CURLY_BRACES, '}'));
            // add opening brace
            $doctrineAnnotationTokens->insertAt($braceIndex + 1, new \PhpCsFixer\Doctrine\Annotation\Token(\ECSPrefix20211002\Doctrine\Common\Annotations\DocLexer::T_OPEN_CURLY_BRACES, '{'));
        }
    }
}
