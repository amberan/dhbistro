<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\Skipper\SkipVoter;

use ECSPrefix20211002\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20211002\Symplify\Skipper\Matcher\FileInfoMatcher;
use ECSPrefix20211002\Symplify\Skipper\SkipCriteriaResolver\SkippedClassAndCodesResolver;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * Matching class and code, e.g. App\Category\ArraySniff.SomeCode
 */
final class ClassAndCodeSkipVoter implements \ECSPrefix20211002\Symplify\Skipper\Contract\SkipVoterInterface
{
    /**
     * @var \Symplify\Skipper\SkipCriteriaResolver\SkippedClassAndCodesResolver
     */
    private $skippedClassAndCodesResolver;
    /**
     * @var \Symplify\Skipper\Matcher\FileInfoMatcher
     */
    private $fileInfoMatcher;
    public function __construct(\ECSPrefix20211002\Symplify\Skipper\SkipCriteriaResolver\SkippedClassAndCodesResolver $skippedClassAndCodesResolver, \ECSPrefix20211002\Symplify\Skipper\Matcher\FileInfoMatcher $fileInfoMatcher)
    {
        $this->skippedClassAndCodesResolver = $skippedClassAndCodesResolver;
        $this->fileInfoMatcher = $fileInfoMatcher;
    }
    /**
     * @param string|object $element
     */
    public function match($element) : bool
    {
        if (!\is_string($element)) {
            return \false;
        }
        return \substr_count($element, '.') === 1;
    }
    /**
     * @param string|object $element
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     */
    public function shouldSkip($element, $smartFileInfo) : bool
    {
        if (\is_object($element)) {
            return \false;
        }
        $skippedClassAndCodes = $this->skippedClassAndCodesResolver->resolve();
        if (!\array_key_exists($element, $skippedClassAndCodes)) {
            return \false;
        }
        // skip regardless the path
        $skippedPaths = $skippedClassAndCodes[$element];
        if ($skippedPaths === null) {
            return \true;
        }
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
