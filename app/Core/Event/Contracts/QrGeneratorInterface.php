<?php

namespace App\Core\Event\Contracts;

interface QrGeneratorInterface
{
    /**
     * Generate a QR code and save it to storage.
     *
     * @param string $content
     * @param string $filename
     * @return string The path to the generated file.
     */
    public function generate(string $content, string $filename): string;
}
