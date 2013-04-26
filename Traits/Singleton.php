<?php

/**
 * makes de class a singleton like class (getInstance Method)
 *
 * needs to put in the docblock the @method line: ˝@method static ActualClassName getInstance()˝ so that IDEs know the actual class
 *
 */
trait EtuDev_Util_Traits_Singleton
{
    static private $_singleton_instance;

    static public function getInstance()
    {
        if (!(self::$_singleton_instance instanceof self)) {
            self::$_singleton_instance = new self;
        }
        return self::$_singleton_instance;
    }
}
