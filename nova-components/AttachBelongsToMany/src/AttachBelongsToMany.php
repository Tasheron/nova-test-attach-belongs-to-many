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
            ->onlyOnForms();
    }

    public function apiResourceNames(string $resource, string $attachResource)
    {
        return $this->withMeta([
            'mainApiResourceName' => $resource,
            'attachApiResourceName' => $attachResource,
        ]);
    }

    public function attachResourceName(string $resource)
    {
        return $this->withMeta([
            'attachResourceName' => $resource,
        ]);
    }
}
