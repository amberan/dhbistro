<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * Can be used in unit testing or in a situations where persisted sessions are not desired.
 *
 * @author Drak <drak@zikula.org>
 */
class NullSessionHandler extends \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\AbstractSessionHandler
{
    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function close()
    {
        return \true;
    }
    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function validateId($sessionId)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     * @param string $sessionId
     */
    protected function doRead($sessionId)
    {
        return '';
    }
    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function updateTimestamp($sessionId, $data)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     * @param string $sessionId
     * @param string $data
     */
    protected function doWrite($sessionId, $data)
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     * @param string $sessionId
     */
    protected function doDestroy($sessionId)
    {
        return \true;
    }
    /**
     * @return int|false
     */
    #[\ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
        return 0;
    }
}
