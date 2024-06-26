<?php

namespace Vitorccs\Maxipago\Interfaces;

interface XmlConverter
{
    public function decodeArray(string $content): ?array;

    public function decodeObject(string $content): ?object;

    public function encode(array $data, ?string $root = null): string;
}
