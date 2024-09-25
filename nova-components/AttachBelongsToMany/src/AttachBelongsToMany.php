<?php

namespace Tasheron\AttachBelongsToMany;

use Laravel\Nova\Fields\Field;

class AttachBelongsToMany extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'attach-belongs-to-many';

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->fillUsing(function () {})
            ->onlyOnForms()
            ->setSorting('index')
            ->setNameField('name');
    }

    public function apiResourceNames(string $resource, string $attachResource)
    {
        return $this->withMeta([
            'mainApiResourceName' => $resource,
            'attachApiResourceName' => $attachResource,
        ]);
    }

    public function setPivotFields(array $fields)
    {
        return $this->withMeta([
            'pivotFields' => $fields,
        ]);
    }

    public function setSorting(string $field)
    {
        return $this->withMeta([
            'sortingField' => $field,
        ]);
    }

    public function setNameField(string $field)
    {
        return $this->withMeta([
            'nameField' => $field,
        ]);
    }
}
