<?php

namespace Smalot\Cups\Model\Traits;

use Smalot\Cups\Model\Job;
use Smalot\Cups\Model\Printer;

/**
 * Trait UriAware
 *
 * @package Smalot\Cups\Model\Traits
 */
trait UriAware
{

    /**
     * @var string
     */
    protected string $uri;

    /**
     * @return null|string
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     *
     * @return Job|Printer|UriAware
     */
    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }
}
