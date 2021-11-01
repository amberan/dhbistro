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
namespace PhpCsFixer\Linter;

/**
 * Handle PHP code linting process.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class Linter implements \PhpCsFixer\Linter\LinterInterface
{
    /**
     * @var LinterInterface
     */
    private $sublinter;
    /**
     * @param null|string $executable PHP executable, null for autodetection
     */
    public function __construct(?string $executable = null)
    {
        try {
            $this->sublinter = new \PhpCsFixer\Linter\TokenizerLinter();
        } catch (\PhpCsFixer\Linter\UnavailableLinterException $e) {
            $this->sublinter = new \PhpCsFixer\Linter\ProcessLinter($executable);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function isAsync() : bool
    {
        return $this->sublinter->isAsync();
    }
    /**
     * {@inheritdoc}
     * @param string $path
     */
    public function lintFile($path) : \PhpCsFixer\Linter\LintingResultInterface
    {
        return $this->sublinter->lintFile($path);
    }
    /**
     * {@inheritdoc}
     * @param string $source
     */
    public function lintSource($source) : \PhpCsFixer\Linter\LintingResultInterface
    {
        return $this->sublinter->lintSource($source);
    }
}
