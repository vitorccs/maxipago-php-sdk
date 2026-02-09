<?php

namespace Vitorccs\Maxipago\Converters;

use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Vitorccs\Maxipago\Helpers\ArrayHelper;
use Vitorccs\Maxipago\Interfaces\XmlConverter;

/**
 * Source:
 * https://symfony.com/doc/current/components/serializer.html#the-xmlencoder
 */
class SymfonyXmlConverter implements XmlConverter
{
    protected XmlEncoder $encoder;

    public function __construct()
    {
        $this->encoder = new XmlEncoder();
    }

    public function decodeArray(string $content): ?array
    {
        try {
            $decoded = $this->encoder->decode($content, XmlEncoder::FORMAT);
        } catch (NotEncodableValueException $e) {
            // prevent XML errors from stoping the execution
            $decoded = [];
        }

        $failed = !is_array($decoded) || empty($decoded);

        return $failed ? null : $decoded;
    }

    public function decodeObject(string $content): ?object
    {
        $decoded = $this->decodeArray($content);

        if (is_null($decoded)) return null;

        return json_decode(json_encode($decoded), false);
    }

    /**
     * Notes:
     * Maxipago API fails to process empty tags (e.g: <tag></tag> or <tag/>)
     * so they all need to be removed.
     *
     * Maxipago is also unable to decode HTML entities (e.g. "&#xE7;" to "ç")
     * so we have to force them to be wrapped in a CDATA section
     */
    public function encode(array $data, ?string $root = null): string
    {
        $data = ArrayHelper::removeEmpty($data);

        $xmlContainsRoot = empty($root);
        if ($xmlContainsRoot) {
            $root = array_key_first($data);
            $data = $data[$root];
        }

        return $this->encoder->encode($data, XmlEncoder::FORMAT, [
            XmlEncoder::ROOT_NODE_NAME => $root,
            XmlEncoder::CDATA_WRAPPING_PATTERN => '/[^a-zA-Z0-9_,\-\. ]/',
        ]);
    }
}
