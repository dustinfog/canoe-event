<?php
/**
 * Created by PhpStorm.
 * User: panzd
 * Date: 8/22/16
 * Time: 11:57 AM
 */

namespace Canoe\Util;


use InvalidArgumentException;

class TypeUtils
{
    /**
     * @param string $method
     * @param int $index start with 1
     * @param mixed $value
     * @param string $type
     */
    public static function validateArgumentType($method, $index, $value, $type)
    {
        $actualType = self::getType($value);
        if (self::getType($value) != $type) {
            throw new InvalidArgumentException(
                "Argument $index passed to {$method}() must be of the type $type, $actualType given"
            );
        }
    }

    public static function getType($value)
    {
        $type = gettype($value);
        if ($type == "object") {
            $type = get_class($value);
        }

        return $type;
    }

    /**
     * @param callable $callable
     * @return \ReflectionFunctionAbstract
     */
    public static function reflectCallable(callable $callable)
    {
        $reflector = null;
        if (is_array($callable)) {
            $reflector = new \ReflectionMethod($callable[0], $callable[1]);
        } elseif (is_string($callable)) {
            $reflector = new \ReflectionFunction($callable);
        } elseif (is_a($callable, 'Closure') || is_callable($callable, '__invoke')) {
            $objReflector = new \ReflectionObject($callable);
            $reflector = $objReflector->getMethod('__invoke');
        }

        return $reflector;
    }

    public static function validateCallable(callable $callable, ... $argClassNames)
    {
        $reflector = self::reflectCallable($callable);
        $parameters = $reflector->getParameters();
        $actualCount = count($parameters);
        $requireCount = count($argClassNames);

        if ($requireCount != $actualCount) {
            throw new InvalidArgumentException("callback needs $requireCount arguments, $actualCount given");
        }

        for ($i = 0; $i < $actualCount; $i++) {
            $parameter = $parameters[$i];
            $class = $parameter->getClass();
            $className = empty($class) ? null : $class->getName();
            $argClassName = $argClassNames[$i];
            if ($className != null
                && $argClassName != null
                && $argClassName != $className
                && is_subclass_of($argClassName, $class)) {
                throw new InvalidArgumentException(
                    "Argument $i for callback must be the type $argClassName or its super class, $className given"
                );
            }
        }
    }
}