<?php

namespace SON;

class Resolver implements \ArrayAccess
{
    use Collection;

    public function handler(string $class, string $method = null)
    {

        $ref = new \ReflectionClass($class);
        $instance = $this->getInstance($ref);

        if(!$method){
            return $instance;
        }

        $refMethod = new \ReflectionMethod($instance, $method);
        $parameters = $this->methodResolver($ref, $refMethod);

        call_user_func_array([$instance, $method], $parameters);

    }

    private function getInstance($ref)
    {

        $constructor = $ref->getConstructor();
        if(!$constructor){
            return $ref->newInstance();
        }

        $parameters = $this->methodResolver($ref, $constructor);

        return $ref->newInstanceArgs($parameters);
    }

    private function methodResolver($ref, $method)
    {
        $parameters = [];

        foreach($method->getParameters() as $param)
        {
            if($param->getType() !== null && $this->offsetExists((string)$param->getType())){
                $parameters[] = $this->offsetGet($param->getType());
                continue;
            }
            if($param->isOptional()){
                $parameters[] = $param->getDefaultValue();
                continue;
            }
           if($param->getClass()){
               $parameters[] = $this->handler($param->getClass()->getName());
               continue;
           }

        }

        return $parameters;
    }
}