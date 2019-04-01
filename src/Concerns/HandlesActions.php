<?php

namespace Livewire\Concerns;

trait HandlesActions
{
    public function syncInput($name, $value)
    {
        $this->callBeforeAndAferSyncHooks($name, $value, function ($name, $value) {
            $this->removeFromDirtyInputsList($name);

            $this->setPropertyValue($name, $value);
        });
    }

    public function lazySyncInput($name, $value)
    {
        $this->callBeforeAndAferSyncHooks($name, $value, function ($name, $value) {
            $this->setPropertyValue($name, $value);

            $this->rehashProperty($name);
        });
    }

    protected function callBeforeAndAferSyncHooks($name, $value, $callback)
    {
        $beforeMethod = 'updating' . studly_case($name);
        $afterMethod = 'updated' . studly_case($name);

        if (method_exists($this, $beforeMethod)) {
            $this->{$beforeMethod}($value);
        }

        $callback($name, $value);

        if (method_exists($this, $afterMethod)) {
            $this->{$afterMethod}($value);
        }
    }

    public function callMethod($method, $params = [])
    {
        switch ($method) {
            case '$set':
                $prop = array_shift($params);
                $this->syncInput($prop, head($params));
                return;
                break;

            case '$toggle':
                $prop = array_shift($params);
                $this->syncInput($prop, ! $this->{$prop});
                return;
                break;

            case '$refresh':
                return;
                break;

            default:
                $this->{$method}(...$params);
                break;
        }
    }
}