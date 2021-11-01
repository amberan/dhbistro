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

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
/**
 * Case sensitive tag replace fixer (does not process inline tags like {@inheritdoc}).
 *
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class PhpdocNoAliasTagFixer extends \PhpCsFixer\AbstractProxyFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('No alias PHPDoc tags should be used.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @property string $foo
 * @property-read string $bar
 *
 * @link baz
 */
final class Example
{
}
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @property string $foo
 * @property-read string $bar
 *
 * @link baz
 */
final class Example
{
}
', ['replacements' => ['link' => 'website']])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAddMissingParamAnnotationFixer, PhpdocAlignFixer, PhpdocSingleLineVarSpacingFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority() : int
    {
        return parent::getPriority();
    }
    public function configure(array $configuration) : void
    {
        parent::configure($configuration);
        /** @var GeneralPhpdocTagRenameFixer $generalPhpdocTagRenameFixer */
        $generalPhpdocTagRenameFixer = $this->proxyFixers['general_phpdoc_tag_rename'];
        try {
            $generalPhpdocTagRenameFixer->configure(['fix_annotation' => \true, 'fix_inline' => \false, 'replacements' => $this->configuration['replacements'], 'case_sensitive' => \true]);
        } catch (\PhpCsFixer\ConfigurationException\InvalidConfigurationException $exception) {
            throw new \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException($this->getName(), \PhpCsFixer\Preg::replace('/^\\[.+?\\] /', '', $exception->getMessage()), $exception);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('replacements', 'Mapping between replaced annotations with new ones.'))->setAllowedTypes(['array'])->setDefault(['property-read' => 'property', 'property-write' => 'property', 'type' => 'var', 'link' => 'see'])->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers() : array
    {
        return [new \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocTagRenameFixer()];
    }
}
