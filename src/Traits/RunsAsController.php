<?php

namespace JohnBand\Traits;

use Illuminate\Http\Request;

trait RunsAsAcontroller
{
    /** @var Request|null */
    protected $request = null;

    public function __invoke(Request $request)
    {
        $this->request = $request;
        $this->attributes = $this->getAttributesFromRequest();
    }

    protected function runsAsController() : bool
    {
        return $this->request instanceof Request;
    }

    protected function getAttributesFromRequest() : array
    {
        return array_merge(
            $this->request->all(),
            $this->getAttributesFromRequest()
        );
    }

    protected function getAttributesFromRoute() : array
    {
        $route = $this->request->route();

        return $route ? $route->parametersWithoutNull() : [];
    }
}