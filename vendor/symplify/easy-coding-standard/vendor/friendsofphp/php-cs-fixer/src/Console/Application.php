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
namespace PhpCsFixer\Console;

use PhpCsFixer\Console\Command\DescribeCommand;
use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\Console\Command\ListFilesCommand;
use PhpCsFixer\Console\Command\ListSetsCommand;
use PhpCsFixer\Console\Command\SelfUpdateCommand;
use PhpCsFixer\Console\SelfUpdate\GithubClient;
use PhpCsFixer\Console\SelfUpdate\NewVersionChecker;
use PhpCsFixer\PharChecker;
use PhpCsFixer\ToolInfo;
use PhpCsFixer\Utils;
use ECSPrefix20211002\Symfony\Component\Console\Application as BaseApplication;
use ECSPrefix20211002\Symfony\Component\Console\Command\ListCommand;
use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20211002\Symfony\Component\Console\Output\ConsoleOutputInterface;
use ECSPrefix20211002\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class Application extends \ECSPrefix20211002\Symfony\Component\Console\Application
{
    public const VERSION = '3.1.0';
    public const VERSION_CODENAME = 'River';
    /**
     * @var ToolInfo
     */
    private $toolInfo;
    public function __construct()
    {
        parent::__construct('PHP CS Fixer', self::VERSION);
        $this->toolInfo = new \PhpCsFixer\ToolInfo();
        // in alphabetical order
        $this->add(new \PhpCsFixer\Console\Command\DescribeCommand());
        $this->add(new \PhpCsFixer\Console\Command\FixCommand($this->toolInfo));
        $this->add(new \PhpCsFixer\Console\Command\ListFilesCommand($this->toolInfo));
        $this->add(new \PhpCsFixer\Console\Command\ListSetsCommand());
        $this->add(new \PhpCsFixer\Console\Command\SelfUpdateCommand(new \PhpCsFixer\Console\SelfUpdate\NewVersionChecker(new \PhpCsFixer\Console\SelfUpdate\GithubClient()), $this->toolInfo, new \PhpCsFixer\PharChecker()));
    }
    public static function getMajorVersion() : int
    {
        return (int) \explode('.', self::VERSION)[0];
    }
    /**
     * {@inheritdoc}
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function doRun($input, $output) : int
    {
        $stdErr = $output instanceof \ECSPrefix20211002\Symfony\Component\Console\Output\ConsoleOutputInterface ? $output->getErrorOutput() : ($input->hasParameterOption('--format', \true) && 'txt' !== $input->getParameterOption('--format', null, \true) ? null : $output);
        if (null !== $stdErr) {
            $warningsDetector = new \PhpCsFixer\Console\WarningsDetector($this->toolInfo);
            $warningsDetector->detectOldVendor();
            $warningsDetector->detectOldMajor();
            $warnings = $warningsDetector->getWarnings();
            if ($warnings) {
                foreach ($warnings as $warning) {
                    $stdErr->writeln(\sprintf($stdErr->isDecorated() ? '<bg=yellow;fg=black;>%s</>' : '%s', $warning));
                }
                $stdErr->writeln('');
            }
        }
        $result = parent::doRun($input, $output);
        if (null !== $stdErr && $output->getVerbosity() >= \ECSPrefix20211002\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERBOSE) {
            $triggeredDeprecations = \PhpCsFixer\Utils::getTriggeredDeprecations();
            if ($triggeredDeprecations) {
                $stdErr->writeln('');
                $stdErr->writeln($stdErr->isDecorated() ? '<bg=yellow;fg=black;>Detected deprecations in use:</>' : 'Detected deprecations in use:');
                foreach ($triggeredDeprecations as $deprecation) {
                    $stdErr->writeln(\sprintf('- %s', $deprecation));
                }
            }
        }
        return $result;
    }
    /**
     * {@inheritdoc}
     */
    public function getLongVersion() : string
    {
        $version = \implode('', [
            parent::getLongVersion(),
            self::VERSION_CODENAME ? \sprintf(' <info>%s</info>', self::VERSION_CODENAME) : '',
            // @phpstan-ignore-line to avoid `Ternary operator condition is always true|false.`
            ' by <comment>Fabien Potencier</comment> and <comment>Dariusz Ruminski</comment>',
        ]);
        $commit = '@git-commit@';
        if ('@' . 'git-commit@' !== $commit) {
            // @phpstan-ignore-line as `$commit` is replaced during phar building
            $version .= ' (' . \substr($commit, 0, 7) . ')';
        }
        return $version;
    }
    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands() : array
    {
        return [new \PhpCsFixer\Console\Command\HelpCommand(), new \ECSPrefix20211002\Symfony\Component\Console\Command\ListCommand()];
    }
}
