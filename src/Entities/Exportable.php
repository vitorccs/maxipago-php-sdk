<?php

namespace Vitorccs\Maxipago\Entities;

/**
 * Note: properties are exported in their declaration order, which also
 * defines the XML node order. Promoted properties are declared at the
 * constructor position within the class body.
 */
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
        $properties = array_diff_key($properties, array_flip($this->nonExportableFields()));

        // add additional array fields
        return array_merge($properties, $this->addExportableFields());
    }
}
