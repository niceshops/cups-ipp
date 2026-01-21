<?php

namespace Smalot\Cups\Model\Traits;

use Smalot\Cups\Model\Job;
use Smalot\Cups\Model\Printer;

/**
 * Trait AttributeAware
 *
 * @package Smalot\Cups\Model\Traits
 */
trait AttributeAware
{

    /**
     * @var array
     */
    protected array $attributes = [];

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @param mixed  $values
     */
    public function setAttribute(string $name, mixed $values): void
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        $this->attributes[$name] = $values;
    }

    /**
     * @param array $attributes
     *
     * @return Job|Printer|AttributeAware
     */
    public function setAttributes(array $attributes): self
    {
        foreach ($attributes as $name => $values) {
            $this->setAttribute($name, $values);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addAttribute(string $name, mixed $value): void
    {
        $this->attributes[$name][] = $value;
    }

    /**
     * @param string $name
     */
    public function removeAttribute(string $name): void
    {
        unset($this->attributes[$name]);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
}
