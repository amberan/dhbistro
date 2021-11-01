<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\Skipper\Skipper;

use ECSPrefix20211002\Symplify\Skipper\Matcher\FileInfoMatcher;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\Skipper\Tests\Skipper\Only\OnlySkipperTest
 */
final class OnlySkipper
{
    /**
     * @var \Symplify\Skipper\Matcher\FileInfoMatcher
     */
    private $fileInfoMatcher;
    public function __construct(\ECSPrefix20211002\Symplify\Skipper\Matcher\FileInfoMatcher $fileInfoMatcher)
    {
        $this->fileInfoMatcher = $fileInfoMatcher;
    }
    /**
     * @param mixed[] $only
     * @param object|string $checker
     */
    public function doesMatchOnly($checker, \ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, array $only) : ?bool
    {
        foreach ($only as $onlyClass => $onlyFiles) {
            if (\is_int($onlyClass)) {
                // solely class
                $onlyClass = $onlyFiles;
                $onlyFiles = null;
            }
            if (!\is_a($checker, $onlyClass, \true)) {
                continue;
            }
            if ($onlyFiles === null) {
                return \true;
            }
            return !$this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $onlyFiles);
        }
        return null;
    }
}
