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
namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20211002\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use ECSPrefix20211002\Symfony\Component\OptionsResolver\Options;
/**
 * @author SpacePossum
 */
final class PhpdocTagTypeFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    private const TAG_REGEX = '/^(?:
        (?<tag>
            (?:@(?<tag_name>.+?)(?:\\s.+)?)
        )
        |
        {(?<inlined_tag>
            (?:@(?<inlined_tag_name>.+?)(?:\\s.+)?)
        )}
    )$/x';
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Forces PHPDoc tags to be either regular annotations or inline.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * {@api}\n */\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @inheritdoc\n */\n", ['tags' => ['inheritdoc' => 'inline']])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority() : int
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        if (!$this->configuration['tags']) {
            return;
        }
        $regularExpression = \sprintf('/({?@(?:%s).*?(?:(?=\\s\\*\\/)|(?=\\n)}?))/i', \implode('|', \array_map(function (string $tag) {
            return \preg_quote($tag, '/');
        }, \array_keys($this->configuration['tags']))));
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $parts = \PhpCsFixer\Preg::split($regularExpression, $token->getContent(), -1, \PREG_SPLIT_DELIM_CAPTURE);
            for ($i = 1, $max = \count($parts) - 1; $i < $max; $i += 2) {
                if (!\PhpCsFixer\Preg::match(self::TAG_REGEX, $parts[$i], $matches)) {
                    continue;
                }
                if ('' !== $matches['tag']) {
                    $tag = $matches['tag'];
                    $tagName = $matches['tag_name'];
                } else {
                    $tag = $matches['inlined_tag'];
                    $tagName = $matches['inlined_tag_name'];
                }
                $tagName = \strtolower($tagName);
                if (!isset($this->configuration['tags'][$tagName])) {
                    continue;
                }
                if ('inline' === $this->configuration['tags'][$tagName]) {
                    $parts[$i] = '{' . $tag . '}';
                    continue;
                }
                if (!$this->tagIsSurroundedByText($parts, $i)) {
                    $parts[$i] = $tag;
                }
            }
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, \implode('', $parts)]);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('tags', 'The list of tags to fix'))->setAllowedTypes(['array'])->setAllowedValues([function ($value) {
            foreach ($value as $type) {
                if (!\in_array($type, ['annotation', 'inline'], \true)) {
                    throw new \ECSPrefix20211002\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException("Unknown tag type \"{$type}\".");
                }
            }
            return \true;
        }])->setDefault(['api' => 'annotation', 'author' => 'annotation', 'copyright' => 'annotation', 'deprecated' => 'annotation', 'example' => 'annotation', 'global' => 'annotation', 'inheritDoc' => 'annotation', 'internal' => 'annotation', 'license' => 'annotation', 'method' => 'annotation', 'package' => 'annotation', 'param' => 'annotation', 'property' => 'annotation', 'return' => 'annotation', 'see' => 'annotation', 'since' => 'annotation', 'throws' => 'annotation', 'todo' => 'annotation', 'uses' => 'annotation', 'var' => 'annotation', 'version' => 'annotation'])->setNormalizer(function (\ECSPrefix20211002\Symfony\Component\OptionsResolver\Options $options, $value) {
            $normalized = [];
            foreach ($value as $tag => $type) {
                $normalized[\strtolower($tag)] = $type;
            }
            return $normalized;
        })->getOption()]);
    }
    private function tagIsSurroundedByText(array $parts, int $index) : bool
    {
        return \PhpCsFixer\Preg::match('/(^|\\R)\\h*[^@\\s]\\N*/', $this->cleanComment($parts[$index - 1])) || \PhpCsFixer\Preg::match('/^.*?\\R\\s*[^@\\s]/', $this->cleanComment($parts[$index + 1]));
    }
    private function cleanComment(string $comment) : string
    {
        $comment = \PhpCsFixer\Preg::replace('/^\\/\\*\\*|\\*\\/$/', '', $comment);
        return \PhpCsFixer\Preg::replace('/(\\R)(\\h*\\*)?\\h*/', '$1', $comment);
    }
}
