<?php

namespace ECSPrefix20211002\React\Socket;

use ECSPrefix20211002\React\Dns\Config\Config as DnsConfig;
use ECSPrefix20211002\React\Dns\Resolver\Factory as DnsFactory;
use ECSPrefix20211002\React\Dns\Resolver\ResolverInterface;
use ECSPrefix20211002\React\EventLoop\LoopInterface;
/**
 * The `Connector` class is the main class in this package that implements the
 * `ConnectorInterface` and allows you to create streaming connections.
 *
 * You can use this connector to create any kind of streaming connections, such
 * as plaintext TCP/IP, secure TLS or local Unix connection streams.
 *
 * Under the hood, the `Connector` is implemented as a *higher-level facade*
 * for the lower-level connectors implemented in this package. This means it
 * also shares all of their features and implementation details.
 * If you want to typehint in your higher-level protocol implementation, you SHOULD
 * use the generic [`ConnectorInterface`](#connectorinterface) instead.
 *
 * @see ConnectorInterface for the base interface
 */
final class Connector implements \ECSPrefix20211002\React\Socket\ConnectorInterface
{
    private $connectors = array();
    /**
     * Instantiate new `Connector`
     *
     * ```php
     * $connector = new React\Socket\Connector();
     * ```
     *
     * This class takes two optional arguments for more advanced usage:
     *
     * ```php
     * // constructor signature as of v1.9.0
     * $connector = new React\Socket\Connector(array $context = [], ?LoopInterface $loop = null);
     *
     * // legacy constructor signature before v1.9.0
     * $connector = new React\Socket\Connector(?LoopInterface $loop = null, array $context = []);
     * ```
     *
     * This class takes an optional `LoopInterface|null $loop` parameter that can be used to
     * pass the event loop instance to use for this object. You can use a `null` value
     * here in order to use the [default loop](https://github.com/reactphp/event-loop#loop).
     * This value SHOULD NOT be given unless you're sure you want to explicitly use a
     * given event loop instance.
     *
     * @param array|LoopInterface|null $context
     * @param null|LoopInterface|array $loop
     * @throws \InvalidArgumentException for invalid arguments
     */
    public function __construct($context = array(), $loop = null)
    {
        // swap arguments for legacy constructor signature
        if (($context instanceof \ECSPrefix20211002\React\EventLoop\LoopInterface || $context === null) && (\func_num_args() <= 1 || \is_array($loop))) {
            $swap = $loop === null ? array() : $loop;
            $loop = $context;
            $context = $swap;
        }
        if (!\is_array($context) || $loop !== null && !$loop instanceof \ECSPrefix20211002\React\EventLoop\LoopInterface) {
            throw new \InvalidArgumentException('Expected "array $context" and "?LoopInterface $loop" arguments');
        }
        // apply default options if not explicitly given
        $context += array('tcp' => \true, 'tls' => \true, 'unix' => \true, 'dns' => \true, 'timeout' => \true, 'happy_eyeballs' => \true);
        if ($context['timeout'] === \true) {
            $context['timeout'] = (float) \ini_get("default_socket_timeout");
        }
        if ($context['tcp'] instanceof \ECSPrefix20211002\React\Socket\ConnectorInterface) {
            $tcp = $context['tcp'];
        } else {
            $tcp = new \ECSPrefix20211002\React\Socket\TcpConnector($loop, \is_array($context['tcp']) ? $context['tcp'] : array());
        }
        if ($context['dns'] !== \false) {
            if ($context['dns'] instanceof \ECSPrefix20211002\React\Dns\Resolver\ResolverInterface) {
                $resolver = $context['dns'];
            } else {
                if ($context['dns'] !== \true) {
                    $config = $context['dns'];
                } else {
                    // try to load nameservers from system config or default to Google's public DNS
                    $config = \ECSPrefix20211002\React\Dns\Config\Config::loadSystemConfigBlocking();
                    if (!$config->nameservers) {
                        $config->nameservers[] = '8.8.8.8';
                        // @codeCoverageIgnore
                    }
                }
                $factory = new \ECSPrefix20211002\React\Dns\Resolver\Factory();
                $resolver = $factory->createCached($config, $loop);
            }
            if ($context['happy_eyeballs'] === \true) {
                $tcp = new \ECSPrefix20211002\React\Socket\HappyEyeBallsConnector($loop, $tcp, $resolver);
            } else {
                $tcp = new \ECSPrefix20211002\React\Socket\DnsConnector($tcp, $resolver);
            }
        }
        if ($context['tcp'] !== \false) {
            $context['tcp'] = $tcp;
            if ($context['timeout'] !== \false) {
                $context['tcp'] = new \ECSPrefix20211002\React\Socket\TimeoutConnector($context['tcp'], $context['timeout'], $loop);
            }
            $this->connectors['tcp'] = $context['tcp'];
        }
        if ($context['tls'] !== \false) {
            if (!$context['tls'] instanceof \ECSPrefix20211002\React\Socket\ConnectorInterface) {
                $context['tls'] = new \ECSPrefix20211002\React\Socket\SecureConnector($tcp, $loop, \is_array($context['tls']) ? $context['tls'] : array());
            }
            if ($context['timeout'] !== \false) {
                $context['tls'] = new \ECSPrefix20211002\React\Socket\TimeoutConnector($context['tls'], $context['timeout'], $loop);
            }
            $this->connectors['tls'] = $context['tls'];
        }
        if ($context['unix'] !== \false) {
            if (!$context['unix'] instanceof \ECSPrefix20211002\React\Socket\ConnectorInterface) {
                $context['unix'] = new \ECSPrefix20211002\React\Socket\UnixConnector($loop);
            }
            $this->connectors['unix'] = $context['unix'];
        }
    }
    public function connect($uri)
    {
        $scheme = 'tcp';
        if (\strpos($uri, '://') !== \false) {
            $scheme = (string) \substr($uri, 0, \strpos($uri, '://'));
        }
        if (!isset($this->connectors[$scheme])) {
            return \ECSPrefix20211002\React\Promise\reject(new \RuntimeException('No connector available for URI scheme "' . $scheme . '"'));
        }
        return $this->connectors[$scheme]->connect($uri);
    }
}
