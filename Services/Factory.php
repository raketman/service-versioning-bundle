<?php

namespace Raketman\Bundle\ServiceVersioningBundle\Services;


use Raketman\Bundle\ServiceVersioningBundle\Exception\NotFoundVersionException;
use Raketman\Bundle\ServiceVersioningBundle\Resolver\VersionResolverInterface;


final class Factory
{
    /** @var  [] */
    private $versions;

    /** @var  VersionResolverInterface */
    private $resolver;

    public function getClass()
    {
        return $this->get($this->resolver->getVersion());
    }
    /**
     * @param VersionResolverInterface $resolver
     */
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
    }


    public function addVersion($version, $service)
    {
        $this->versions[$version] = $service;
    }


    public function get($key)
    {
        if (false === isset($this->versions[$key])) {
            throw new NotFoundVersionException();
        }

        return $this->versions[$key];
    }



}
