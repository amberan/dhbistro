<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpKernel\EventListener;

use ECSPrefix20211002\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20211002\Symfony\Component\HttpKernel\Event\RequestEvent;
use ECSPrefix20211002\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use ECSPrefix20211002\Symfony\Component\HttpKernel\KernelEvents;
use ECSPrefix20211002\Symfony\Component\HttpKernel\UriSigner;
/**
 * Handles content fragments represented by special URIs.
 *
 * All URL paths starting with /_fragment are handled as
 * content fragments by this listener.
 *
 * Throws an AccessDeniedHttpException exception if the request
 * is not signed or if it is not an internal sub-request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class FragmentListener implements \ECSPrefix20211002\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $signer;
    private $fragmentPath;
    /**
     * @param string $fragmentPath The path that triggers this listener
     */
    public function __construct(\ECSPrefix20211002\Symfony\Component\HttpKernel\UriSigner $signer, string $fragmentPath = '/_fragment')
    {
        $this->signer = $signer;
        $this->fragmentPath = $fragmentPath;
    }
    /**
     * Fixes request attributes when the path is '/_fragment'.
     *
     * @throws AccessDeniedHttpException if the request does not come from a trusted IP
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     */
    public function onKernelRequest($event)
    {
        $request = $event->getRequest();
        if ($this->fragmentPath !== \rawurldecode($request->getPathInfo())) {
            return;
        }
        if ($request->attributes->has('_controller')) {
            // Is a sub-request: no need to parse _path but it should still be removed from query parameters as below.
            $request->query->remove('_path');
            return;
        }
        if ($event->isMainRequest()) {
            $this->validateRequest($request);
        }
        \parse_str($request->query->get('_path', ''), $attributes);
        $request->attributes->add($attributes);
        $request->attributes->set('_route_params', \array_replace($request->attributes->get('_route_params', []), $attributes));
        $request->query->remove('_path');
    }
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    protected function validateRequest($request)
    {
        // is the Request safe?
        if (!$request->isMethodSafe()) {
            throw new \ECSPrefix20211002\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        }
        // is the Request signed?
        if ($this->signer->checkRequest($request)) {
            return;
        }
        throw new \ECSPrefix20211002\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }
    public static function getSubscribedEvents() : array
    {
        return [\ECSPrefix20211002\Symfony\Component\HttpKernel\KernelEvents::REQUEST => [['onKernelRequest', 48]]];
    }
}
