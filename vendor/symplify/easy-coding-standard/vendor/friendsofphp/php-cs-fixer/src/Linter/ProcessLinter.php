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

use PhpCsFixer\FileReader;
use PhpCsFixer\FileRemoval;
use ECSPrefix20211002\Symfony\Component\Filesystem\Exception\IOException;
use ECSPrefix20211002\Symfony\Component\Process\PhpExecutableFinder;
use ECSPrefix20211002\Symfony\Component\Process\Process;
/**
 * Handle PHP code linting using separated process of `php -l _file_`.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ProcessLinter implements \PhpCsFixer\Linter\LinterInterface
{
    /**
     * @var FileRemoval
     */
    private $fileRemoval;
    /**
     * @var ProcessLinterProcessBuilder
     */
    private $processBuilder;
    /**
     * Temporary file for code linting.
     *
     * @var null|string
     */
    private $temporaryFile;
    /**
     * @param null|string $executable PHP executable, null for autodetection
     */
    public function __construct(?string $executable = null)
    {
        if (null === $executable) {
            $executableFinder = new \ECSPrefix20211002\Symfony\Component\Process\PhpExecutableFinder();
            $executable = $executableFinder->find(\false);
            if (\false === $executable) {
                throw new \PhpCsFixer\Linter\UnavailableLinterException('Cannot find PHP executable.');
            }
            if ('phpdbg' === \PHP_SAPI) {
                if (\false === \strpos($executable, 'phpdbg')) {
                    throw new \PhpCsFixer\Linter\UnavailableLinterException('Automatically found PHP executable is non-standard phpdbg. Could not find proper PHP executable.');
                }
                // automatically found executable is `phpdbg`, let us try to fallback to regular `php`
                $executable = \str_replace('phpdbg', 'php', $executable);
                if (!\is_executable($executable)) {
                    throw new \PhpCsFixer\Linter\UnavailableLinterException('Automatically found PHP executable is phpdbg. Could not find proper PHP executable.');
                }
            }
        }
        $this->processBuilder = new \PhpCsFixer\Linter\ProcessLinterProcessBuilder($executable);
        $this->fileRemoval = new \PhpCsFixer\FileRemoval();
    }
    public function __destruct()
    {
        if (null !== $this->temporaryFile) {
            $this->fileRemoval->delete($this->temporaryFile);
        }
    }
    /**
     * This class is not intended to be serialized,
     * and cannot be deserialized (see __wakeup method).
     */
    public function __sleep() : array
    {
        throw new \BadMethodCallException('Cannot serialize ' . __CLASS__);
    }
    /**
     * Disable the deserialization of the class to prevent attacker executing
     * code by leveraging the __destruct method.
     *
     * @see https://owasp.org/www-community/vulnerabilities/PHP_Object_Injection
     */
    public function __wakeup() : void
    {
        throw new \BadMethodCallException('Cannot unserialize ' . __CLASS__);
    }
    /**
     * {@inheritdoc}
     */
    public function isAsync() : bool
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     * @param string $path
     */
    public function lintFile($path) : \PhpCsFixer\Linter\LintingResultInterface
    {
        return new \PhpCsFixer\Linter\ProcessLintingResult($this->createProcessForFile($path), $path);
    }
    /**
     * {@inheritdoc}
     * @param string $source
     */
    public function lintSource($source) : \PhpCsFixer\Linter\LintingResultInterface
    {
        return new \PhpCsFixer\Linter\ProcessLintingResult($this->createProcessForSource($source), $this->temporaryFile);
    }
    /**
     * @param string $path path to file
     */
    private function createProcessForFile(string $path) : \ECSPrefix20211002\Symfony\Component\Process\Process
    {
        // in case php://stdin
        if (!\is_file($path)) {
            return $this->createProcessForSource(\PhpCsFixer\FileReader::createSingleton()->read($path));
        }
        $process = $this->processBuilder->build($path);
        $process->setTimeout(10);
        $process->start();
        return $process;
    }
    /**
     * Create process that lint PHP code.
     *
     * @param string $source code
     */
    private function createProcessForSource(string $source) : \ECSPrefix20211002\Symfony\Component\Process\Process
    {
        if (null === $this->temporaryFile) {
            $this->temporaryFile = \tempnam(\sys_get_temp_dir(), 'cs_fixer_tmp_');
            $this->fileRemoval->observe($this->temporaryFile);
        }
        if (\false === @\file_put_contents($this->temporaryFile, $source)) {
            throw new \ECSPrefix20211002\Symfony\Component\Filesystem\Exception\IOException(\sprintf('Failed to write file "%s".', $this->temporaryFile), 0, null, $this->temporaryFile);
        }
        return $this->createProcessForFile($this->temporaryFile);
    }
}
