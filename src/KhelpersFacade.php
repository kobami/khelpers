<?php

namespace Kobami\Khelpers;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kobami\Khelpers\Skeleton\SkeletonClass
 */
class KhelpersFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'khelpers';
    }
}
