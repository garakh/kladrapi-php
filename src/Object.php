<?php

namespace Kladr;

/*
 * It has been replaced by [[ObjectKladr]] because `object` has become a reserved word which can not be
 * used as class name in PHP 7.2.
 *
 * @deprecated, the class name `Object` is invalid since PHP 7.2, use [[ObjectKladr]] instead.
 * @see https://wiki.php.net/rfc/object-typehint
 * @see ObjectKladr
 */
class Object extends ObjectKladr
{
}