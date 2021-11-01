<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Provider;

use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
final class CurrentParentFileInfoProvider
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    private $smartFileInfo;
    public function setParentFileInfo(\ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : void
    {
        $this->smartFileInfo = $smartFileInfo;
    }
    public function provide() : ?\ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo
    {
        return $this->smartFileInfo;
    }
}
