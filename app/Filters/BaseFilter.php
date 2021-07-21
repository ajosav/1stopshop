<?php

namespace App\Filters;

use Closure;
use Illuminate\Support\Str;

abstract class BaseFilter {

    public function handle($request, Closure $next) {
        $process = $next($request);
        if(!request()->has($this->filterName())
            || request($this->filterName()) == "") {
            return $process;
        }

        return $this->applyFilter($process);
    }

    protected abstract function applyFilter($builder);

    protected function filterName() {
        return Str::snake(class_basename($this));
    }


}