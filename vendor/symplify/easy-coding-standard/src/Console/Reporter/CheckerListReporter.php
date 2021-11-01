<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Reporter;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use ECSPrefix20211002\Symfony\Component\Console\Style\SymfonyStyle;
final class CheckerListReporter
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(\ECSPrefix20211002\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param FixerInterface[]|Sniff[] $checkers
     */
    public function report(array $checkers, string $type) : void
    {
        if ($checkers === []) {
            return;
        }
        $checkerNames = \array_map(function ($checker) : string {
            return \get_class($checker);
        }, $checkers);
        $sectionMessage = \sprintf('%d checker%s from %s:', \count($checkers), \count($checkers) === 1 ? '' : 's', $type);
        $this->symfonyStyle->section($sectionMessage);
        \sort($checkerNames);
        $this->symfonyStyle->listing($checkerNames);
    }
}
