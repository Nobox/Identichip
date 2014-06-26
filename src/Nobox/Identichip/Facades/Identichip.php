<?php namespace Nobox\Identichip\Facades;

use Illuminate\Support\Facades\Facade;

class Identichip extends Facade {

  /**
   * Get the registered name of the component.
   *
   * @return string
   */
    protected static function getFacadeAccessor() { return 'identichip'; }

}