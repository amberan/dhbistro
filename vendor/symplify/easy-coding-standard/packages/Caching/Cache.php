<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage;
final class Cache
{
    /**
     * @var \Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage
     */
    private $fileCacheStorage;
    public function __construct(\Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage $fileCacheStorage)
    {
        $this->fileCacheStorage = $fileCacheStorage;
    }
    /**
     * @return mixed|null
     */
    public function load(string $key, string $variableKey)
    {
        return $this->fileCacheStorage->load($key, $variableKey);
    }
    /**
     * @param mixed $data
     */
    public function save(string $key, string $variableKey, $data) : void
    {
        $this->fileCacheStorage->save($key, $variableKey, $data);
    }
    public function clear() : void
    {
        $this->fileCacheStorage->clear();
    }
    public function clean(string $cacheKey) : void
    {
        $this->fileCacheStorage->clean($cacheKey);
    }
}
