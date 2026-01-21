<?php

namespace Smalot\Cups\Model;

/**
 * Interface JobInterface
 *
 * @package Smalot\Cups\Model
 */
interface JobInterface
{

    /**
     * @return null|int
     */
    public function getId(): ?int;

    /**
     * @param int $id
     *
     * @return JobInterface|PrinterInterface
     */
    public function setId(int $id): JobInterface|PrinterInterface;

    /**
     * @return null|string
     */
    public function getUri(): ?string;

    /**
     * @param string $uri
     *
     * @return JobInterface|PrinterInterface
     */
    public function setUri(string $uri): JobInterface|PrinterInterface;

    /**
     * @return string
     */
    public function getPrinterUri(): string;

    /**
     * @param string $printer_uri
     *
     * @return JobInterface|PrinterInterface
     */
    public function setPrinterUri(string $printer_uri): JobInterface|PrinterInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return JobInterface|PrinterInterface
     */
    public function setName(string $name): JobInterface|PrinterInterface;

    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @param string $username
     *
     * @return JobInterface|PrinterInterface
     */
    public function setUsername(string $username): JobInterface|PrinterInterface;

    /**
     * @return string
     */
    public function getPageRanges(): string;

    /**
     * @param string $page_ranges
     *
     * @return JobInterface|PrinterInterface
     */
    public function setPageRanges(string $page_ranges = 'all'): JobInterface|PrinterInterface;

    /**
     * @return int
     */
    public function getCopies(): int;

    /**
     * @param int $copies
     *
     * @return JobInterface
     */
    public function setCopies(int $copies): JobInterface;

    /**
     * @return string
     */
    public function getSides(): string;

    /**
     * @param string $sides
     *
     * @return JobInterface|PrinterInterface
     */
    public function setSides(string $sides): JobInterface|PrinterInterface;

    /**
     * @return int
     */
    public function getFidelity(): int;

    /**
     * @param int $fidelity
     *
     * @return JobInterface|PrinterInterface
     */
    public function setFidelity(int $fidelity): JobInterface|PrinterInterface;

    /**
     * @return array
     */
    public function getContent(): array;

    /**
     * @param string $filename
     * @param string $name
     * @param string $mime_type
     *
     * @return JobInterface|PrinterInterface
     */
    public function addFile(string $filename, string $name = '', string $mime_type = 'application/octet-stream'): JobInterface|PrinterInterface;

    /**
     * @param string $text
     * @param string $name
     *
     * @return JobInterface|PrinterInterface
     */
    public function addText(string $text, string $name = ''): JobInterface|PrinterInterface;

    /**
     * @return array
     */
    public function getAttributes(): array;

    /**
     * @param array $attributes
     *
     * @return JobInterface|PrinterInterface
     */
    public function setAttributes(array $attributes): JobInterface|PrinterInterface;

    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @param string $state
     */
    public function setState(string $state);

    /**
     * @return string
     */
    public function getStateReason(): string;

    /**
     * @param string $state_reason
     */
    public function setStateReason(string $state_reason);
}
