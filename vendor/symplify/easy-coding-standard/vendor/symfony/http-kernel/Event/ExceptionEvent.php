<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpKernel\Event;

use ECSPrefix20211002\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20211002\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows to create a response for a thrown exception.
 *
 * Call setResponse() to set the response that will be returned for the
 * current request. The propagation of this event is stopped as soon as a
 * response is set.
 *
 * You can also call setThrowable() to replace the thrown exception. This
 * exception will be thrown if no response is set during processing of this
 * event.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class ExceptionEvent extends \ECSPrefix20211002\Symfony\Component\HttpKernel\Event\RequestEvent
{
    private $throwable;
    /**
     * @var bool
     */
    private $allowCustomResponseCode = \false;
    public function __construct(\ECSPrefix20211002\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, \ECSPrefix20211002\Symfony\Component\HttpFoundation\Request $request, int $requestType, \Throwable $e)
    {
        parent::__construct($kernel, $request, $requestType);
        $this->setThrowable($e);
    }
    public function getThrowable() : \Throwable
    {
        return $this->throwable;
    }
    /**
     * Replaces the thrown exception.
     *
     * This exception will be thrown if no response is set in the event.
     * @param \Throwable $exception
     */
    public function setThrowable($exception) : void
    {
        $this->throwable = $exception;
    }
    /**
     * Mark the event as allowing a custom response code.
     */
    public function allowCustomResponseCode() : void
    {
        $this->allowCustomResponseCode = \true;
    }
    /**
     * Returns true if the event allows a custom response code.
     */
    public function isAllowingCustomResponseCode() : bool
    {
        return $this->allowCustomResponseCode;
    }
}
