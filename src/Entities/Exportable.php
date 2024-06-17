<?php

namespace Vitorccs\Maxipago\Entities;

trait Exportable
{
    public function nonExportableFields(): array
    {
        return [];
    }

    public function addExportableFields(): array
    {
        return [];
    }

    public function export(): array
    {
        return json_decode(json_encode($this), true);
    }

    public function jsonSerialize(): array
    {
        // get object properties in array format
        $properties = get_object_vars($this);

        // remove non-exportable fields
        $properties = array_filter(
            $properties,
            fn(mixed $value, string $key) => !in_array($key, $this->nonExportableFields()),
            ARRAY_FILTER_USE_BOTH
        );

        // add additional array fields
        return array_merge($properties, $this->addExportableFields());
    }
}
