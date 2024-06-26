<?php

namespace Vitorccs\Maxipago\Test\Shared;

use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Vitorccs\Maxipago\Http\Resource;

trait ResourceStubTrait
{
    public function setStubResponse(MockBuilder $mockBuilder,
                                    string      $methodName,
                                    array       $args,
                                    object      $responseBody): MockObject&Resource
    {
        /* @var MockObject&Resource $stub */

        $stub = $mockBuilder
            ->disableOriginalConstructor()
            ->onlyMethods([$methodName])
            ->getMock();

        $stub->expects($this->once())
            ->method($methodName)
            ->with(...$args)
            ->willReturn($responseBody);

        return $stub;
    }

    public function setStubException(MockBuilder $mockBuilder,
                                     string      $methodName,
                                     \Exception  $e): MockObject&Resource
    {
        /* @var MockObject&Resource $stub */

        $stub = $mockBuilder
            ->disableOriginalConstructor()
            ->onlyMethods([$methodName])
            ->getMock();

        $stub->expects($this->once())
            ->method($methodName)
            ->willThrowException($e);

        return $stub;
    }
}
