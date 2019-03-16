<?php

namespace MyUCP\Foundation;

use ArrayAccess;
use BadMethodCallException;
use Closure;
use MyUCP\Support\Arr;
use MyUCP\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class Container implements ArrayAccess
{
    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var array
     */
    protected $aliases = [];

    /**
     * @var array
     */
    protected $bindings = [];

    /**
     * @var array
     */
    protected $singletons = [];

    /**
     * @return Container
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function has($abstract)
    {
        return isset($this->instances[$abstract]) ||
                $this->isBind($abstract) ||
                $this->isSingleton($abstract) ||
                $this->isAlias($abstract);
    }

    /**
     * Determine if a given string is an alias.
     *
     * @param  string  $name
     * @return bool
     */
    public function isAlias($name)
    {
        return isset($this->aliases[$name]);
    }

    /**
     * @param $class
     * @param mixed|null $instance
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function make($class, $instance = null)
    {
        if(is_null($instance)) {
            return $this->build($class);
        }

        if(is_array($instance)) {
            return $this->build($class, $instance);
        }

        return $this->build($class, [], $instance);
    }

    /**
     * @param $class
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function makeWith($class, $parameters = [])
    {
        return $this->make($class, $parameters);
    }

    /**
     * @param $class
     * @param array $parameters
     * @param mixed|Closure|null $instance
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    protected function build($class, $parameters = [], $instance = null)
    {
        // разделить название класса (интерфейса и тд) на метод

        [$name, $method] = $this->parseClassCallback($this->bindMethod($class));

        // проверить интерфейс ли это

        if($this->isBind($name)) {
            $name = $this->bindings[$name];
        }

        // проверить указан ли к интфрейсу алиас

        if($this->isAlias($name)) {
            // если алиас взять название класса

            $name = $this->aliases[$name];
        }

        if(! is_null($instance)) {
            $instance =  $instance instanceof Closure ? $instance : function () use ($instance) {
                return $instance;
            };
        }

        $object = $this->resolveInstance($name, $parameters, $instance);

        if(! is_null($method)) {
            return $this->callMethod($object, $method, $parameters);
        }

        return $object;
    }

    /**
     * @param string|array $method
     *
     * @return string
     */
    protected function bindMethod($method)
    {
        if(is_array($method)) {
            return $method[0] . "@" . $method[1];
        }

        return $method;
    }

    /**
     * @param $instance
     * @param $method
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function callMethod($instance, $method, $parameters = [])
    {
        if(is_string($instance)) {
            $instance = $this->make($instance);
        }

        $class = get_class($instance);

        if(! method_exists($instance, $method)) {
            throw new BadMethodCallException("Cant call [{$method}] method in [{$class}] instance.");
        }

        $method = new ReflectionMethod($instance, $method);

        $dependencies = $method->getParameters();

        $instances = $this->resolveDependencies($dependencies, $parameters);

        return call_user_func_array([$instance, $method->getName()], $instances);
    }

    /**
     * @param $instance
     * @param $method
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function call($instance, $method, $parameters = [])
    {
        return $this->callMethod($instance, $method, $parameters);
    }

    /**
     * @param $name
     * @param array $parameters
     *
     * @param null|Closure $instance
     * @return mixed
     *
     * @throws \ReflectionException
     */
    protected function resolveInstance($name, $parameters = [], $instance = null)
    {
        if($this->isSingleton($name)) {
            return $this->resolveSingleton($name, $parameters, $instance);
        }

        return $this->instances[] = is_null($instance)
                                        ? $this->resolve($name, $parameters)
                                        : $instance();
    }

    /**
     * @param $name
     * @param array $parameters
     *
     * @param null|Closure $instance
     * @return mixed
     *
     * @throws \ReflectionException
     */
    protected function resolveSingleton($name, $parameters = [], $instance = null)
    {
        if(! Arr::has($this->instances, $name)) {
            return $this->instances[$name] = is_null($instance)
                                                ? $this->resolve($name, $parameters)
                                                : $instance();
        }

        return $this->instances[$name];
    }

    /**
     * @param $name
     * @param array $parameters
     *
     * @return mixed
     * @throws \ReflectionException
     */
    protected function resolve($name, $parameters = [])
    {
        $reflector = new ReflectionClass($name);

        $constructor = $reflector->getConstructor();

        if(is_null($constructor)) {
            return new $name;
        }

        $dependencies = $constructor->getParameters();

        $instances = $this->resolveDependencies($dependencies, $parameters);

        $instance = $reflector->newInstanceArgs($instances);

        return $instance;
    }

    /**
     * @param ReflectionParameter $parameter
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    protected function resolveClass(ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
    }

    /**
     * @param array $parameters
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws \ReflectionException
     */
    protected function resolvePrimitiveParameter(array &$parameters, ReflectionParameter $parameter)
    {
        if(empty($parameters) && $parameter->isOptional()) {
            return $parameter->getDefaultValue();
        }

        return array_shift($parameters);
    }

    /**
     * @param array $dependencies
     * @param array $parameters
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    protected function resolveDependencies(array $dependencies, array $parameters = [])
    {
        if(count($dependencies) == count($parameters)) {
            return $parameters;
        }

        $results = [];

        foreach($dependencies as $index => $dependency) {

            if(! is_null($dependency->getClass())) {
                if(($instance = $this->instanceInParameters($parameters, $dependency->getClass())) !== false) {
                    $results[] = $instance;

                    continue;
                }
            }

            $results[] = is_null($dependency->getClass())
                        ? $this->resolvePrimitiveParameter($parameters, $dependency)
                        : $this->resolveClass($dependency);
        }

        return $results;
    }

    /**
     * @param array $parameters
     * @param ReflectionClass $instance
     *
     * @return bool|mixed
     */
    protected function instanceInParameters(array &$parameters, ReflectionClass $instance)
    {
        foreach ($parameters as $index => $parameter) {
            if(is_a($parameter, $instance->getName())) {
                unset($parameters[$index]);

                return $parameter;
            }
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    protected function isBind($name)
    {
        return Arr::has($this->bindings, $name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    protected function isSingleton($name)
    {
        return Arr::has($this->singletons, $name);
    }

    /**
     * @param string $callback
     *
     * @return array
     */
    protected function parseClassCallback($callback)
    {
        return Str::parseCallback($callback);
    }

    /**
     * Make alias for instance or only name
     *
     * @param $alias
     * @param null $name
     * @param null $instance
     *
     * @return bool|mixed|null
     *
     * @throws \ReflectionException
     */
    public function alias($alias, $name = null, $instance = null)
    {
        if(is_null($name)) {
            return $this->make($alias, $instance);
        }

        $this->aliases[$alias] = $name;

        if(! is_null($instance)) {
            $this->make($name, $instance);
        }

        return $this;
    }

    /**
     * @param $interface
     * @param $class
     *
     * @return Container
     */
    public function bind($interface, $class)
    {
        $this->bindings[$interface] = $class;

        return $this;
    }

    /**
     * @param $name
     * @param array $parameters
     *
     * @return $this
     */
    public function singleton($name, $parameters = [])
    {
        if($this->isAlias($name)) {
            $name = $this->aliases[$name];
        }

        $this->singletons[$name] = $parameters;

        return $this;
    }

    /**
     * @param $name
     */
    public function remove($name)
    {
        if(is_string($name)) {
            if($this->isAlias($name)) {
                unset($this->aliases[$name]);
            }

            if($this->isSingleton($name)) {
                unset($this->singletons[$name]);
            }

            if($this->isBind($name)) {
                unset($this->bindings[$name]);
            }

            if(Arr::has($this->instances, $name)) {
                unset($this->instances[$name]);
            }
        } else {
            if(Arr::in($this->instances, $name)) {
                unset($this->instances[array_search($name, $this->instances)]);
            }
        }
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string $key
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function offsetGet($key)
    {
        return $this->make($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->bind($key, $value instanceof Closure ? $value : function () use ($value) {
            return $value;
        });
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    /**
     * Dynamically access container services.
     *
     * @param  string $key
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function __get($key)
    {
        return $this->make($key);
    }

    /**
     * Dynamically set container services.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function __set($key, $value)
    {
        $this->make($key, $value);
    }
}