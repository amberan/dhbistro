<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\EasyTesting\DataProvider;

use Iterator;
use ECSPrefix20211002\Nette\Utils\Strings;
use ECSPrefix20211002\Symfony\Component\Finder\Finder;
use ECSPrefix20211002\Symfony\Component\Finder\SplFileInfo;
use ECSPrefix20211002\Symplify\SmartFileSystem\Exception\FileNotFoundException;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20211002\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\EasyTesting\Tests\DataProvider\StaticFixtureFinder\StaticFixtureFinderTest
 */
final class StaticFixtureFinder
{
    /**
     * @return Iterator<array<int, SmartFileInfo>>
     */
    public static function yieldDirectory(string $directory, string $suffix = '*.php.inc') : \Iterator
    {
        $fileInfos = self::findFilesInDirectory($directory, $suffix);
        return self::yieldFileInfos($fileInfos);
    }
    /**
     * @return Iterator<SmartFileInfo>
     */
    public static function yieldDirectoryExclusively(string $directory, string $suffix = '*.php.inc') : \Iterator
    {
        $fileInfos = self::findFilesInDirectoryExclusively($directory, $suffix);
        return self::yieldFileInfos($fileInfos);
    }
    /**
     * @return Iterator<string, array<int, SplFileInfo>>
     */
    public static function yieldDirectoryWithRelativePathname(string $directory, string $suffix = '*.php.inc') : \Iterator
    {
        $fileInfos = self::findFilesInDirectory($directory, $suffix);
        return self::yieldFileInfosWithRelativePathname($fileInfos);
    }
    /**
     * @return Iterator<string, array<int, SplFileInfo>>
     */
    public static function yieldDirectoryExclusivelyWithRelativePathname(string $directory, string $suffix = '*.php.inc') : \Iterator
    {
        $fileInfos = self::findFilesInDirectoryExclusively($directory, $suffix);
        return self::yieldFileInfosWithRelativePathname($fileInfos);
    }
    /**
     * @param SplFileInfo[] $fileInfos
     * @return Iterator<array<int, SmartFileInfo>>
     */
    private static function yieldFileInfos(array $fileInfos) : \Iterator
    {
        foreach ($fileInfos as $fileInfo) {
            try {
                $smartFileInfo = new \ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo($fileInfo->getRealPath());
                (yield [$smartFileInfo]);
            } catch (\ECSPrefix20211002\Symplify\SmartFileSystem\Exception\FileNotFoundException $exception) {
            }
        }
    }
    /**
     * @param SplFileInfo[] $fileInfos
     * @return Iterator<string, array<int, SplFileInfo>>
     */
    private static function yieldFileInfosWithRelativePathname(array $fileInfos) : \Iterator
    {
        foreach ($fileInfos as $fileInfo) {
            try {
                $smartFileInfo = new \ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo($fileInfo->getRealPath());
                (yield $fileInfo->getRelativePathname() => [$smartFileInfo]);
            } catch (\ECSPrefix20211002\Symplify\SmartFileSystem\Exception\FileNotFoundException $exception) {
            }
        }
    }
    /**
     * @return SplFileInfo[]
     */
    private static function findFilesInDirectory(string $directory, string $suffix) : array
    {
        $finder = \ECSPrefix20211002\Symfony\Component\Finder\Finder::create()->in($directory)->files()->name($suffix);
        $fileInfos = \iterator_to_array($finder);
        return \array_values($fileInfos);
    }
    /**
     * @return SplFileInfo[]
     */
    private static function findFilesInDirectoryExclusively(string $directory, string $suffix) : array
    {
        self::ensureNoOtherFileName($directory, $suffix);
        $finder = \ECSPrefix20211002\Symfony\Component\Finder\Finder::create()->in($directory)->files()->name($suffix);
        $fileInfos = \iterator_to_array($finder->getIterator());
        return \array_values($fileInfos);
    }
    private static function ensureNoOtherFileName(string $directory, string $suffix) : void
    {
        $iterator = \ECSPrefix20211002\Symfony\Component\Finder\Finder::create()->in($directory)->files()->notName($suffix)->getIterator();
        $relativeFilePaths = [];
        foreach ($iterator as $fileInfo) {
            $relativeFilePaths[] = \ECSPrefix20211002\Nette\Utils\Strings::substring($fileInfo->getRealPath(), \strlen(\getcwd()) + 1);
        }
        if ($relativeFilePaths === []) {
            return;
        }
        throw new \ECSPrefix20211002\Symplify\SymplifyKernel\Exception\ShouldNotHappenException(\sprintf('Files "%s" have invalid suffix, use "%s" suffix instead', \implode('", ', $relativeFilePaths), $suffix));
    }
}
