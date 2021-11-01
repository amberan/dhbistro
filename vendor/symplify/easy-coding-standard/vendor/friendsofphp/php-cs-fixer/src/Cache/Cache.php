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
namespace PhpCsFixer\Cache;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class Cache implements \PhpCsFixer\Cache\CacheInterface
{
    /**
     * @var SignatureInterface
     */
    private $signature;
    /**
     * @var array
     */
    private $hashes = [];
    public function __construct(\PhpCsFixer\Cache\SignatureInterface $signature)
    {
        $this->signature = $signature;
    }
    public function getSignature() : \PhpCsFixer\Cache\SignatureInterface
    {
        return $this->signature;
    }
    /**
     * @param string $file
     */
    public function has($file) : bool
    {
        return \array_key_exists($file, $this->hashes);
    }
    /**
     * @param string $file
     */
    public function get($file) : ?int
    {
        if (!$this->has($file)) {
            return null;
        }
        return $this->hashes[$file];
    }
    /**
     * @param string $file
     * @param int $hash
     */
    public function set($file, $hash) : void
    {
        $this->hashes[$file] = $hash;
    }
    /**
     * @param string $file
     */
    public function clear($file) : void
    {
        unset($this->hashes[$file]);
    }
    public function toJson() : string
    {
        $json = \json_encode(['php' => $this->getSignature()->getPhpVersion(), 'version' => $this->getSignature()->getFixerVersion(), 'indent' => $this->getSignature()->getIndent(), 'lineEnding' => $this->getSignature()->getLineEnding(), 'rules' => $this->getSignature()->getRules(), 'hashes' => $this->hashes]);
        if (\JSON_ERROR_NONE !== \json_last_error()) {
            throw new \UnexpectedValueException(\sprintf('Can not encode cache signature to JSON, error: "%s". If you have non-UTF8 chars in your signature, like in license for `header_comment`, consider enabling `ext-mbstring` or install `symfony/polyfill-mbstring`.', \json_last_error_msg()));
        }
        return $json;
    }
    /**
     * @throws \InvalidArgumentException
     *
     * @return Cache
     * @param string $json
     */
    public static function fromJson($json) : self
    {
        $data = \json_decode($json, \true);
        if (null === $data && \JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf('Value needs to be a valid JSON string, got "%s", error: "%s".', $json, \json_last_error_msg()));
        }
        $requiredKeys = ['php', 'version', 'indent', 'lineEnding', 'rules', 'hashes'];
        $missingKeys = \array_diff_key(\array_flip($requiredKeys), $data);
        if (\count($missingKeys)) {
            throw new \InvalidArgumentException(\sprintf('JSON data is missing keys "%s"', \implode('", "', $missingKeys)));
        }
        $signature = new \PhpCsFixer\Cache\Signature($data['php'], $data['version'], $data['indent'], $data['lineEnding'], $data['rules']);
        $cache = new self($signature);
        $cache->hashes = $data['hashes'];
        return $cache;
    }
}
