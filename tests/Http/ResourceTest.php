<?php

namespace Vitorccs\Maxipago\Test\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Entities\Parameters;
use Vitorccs\Maxipago\Http\Api;
use Vitorccs\Maxipago\Http\Resource;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;
use Vitorccs\Maxipago\Test\Shared\ParameterHelper;

class ResourceTest extends TestCase
{
    #[DataProvider('argumentsProvider')]
    public function test_post_xml(array  $data,
                                  string $command = null,
                                  string $version = null)
    {
        $this->invokeProtectedMethod('postXml', '/UniversalAPI/postXML', $data, $command, $version);
    }

    #[DataProvider('argumentsProvider')]
    public function test_post_api(array  $data,
                                  string $command = null,
                                  string $version = null)
    {
        $this->invokeProtectedMethod('postApi', '/UniversalAPI/postAPI', $data, $command, $version);
    }

    #[DataProvider('argumentsProvider')]
    public function test_reports_api(array  $data,
                                     string $command = null,
                                     string $version = null)
    {
        $this->invokeProtectedMethod('reportsApi', '/ReportsAPI/servlet/ReportsAPI', $data, $command, $version);
    }

    public function getResource(Parameters $parameters, ?string $apiVersion): Resource
    {
        return new class($parameters, $apiVersion) extends Resource {

            protected $apiVersion;

            public function __construct(?Parameters $parameters,
                                        ?string     $apiVersion)
            {
                parent::__construct($parameters);
                $this->apiVersion = $apiVersion;
            }

            protected function root(): string
            {
                return '';
            }

            protected function apiVersion(): ?string
            {
                return $this->apiVersion;
            }
        };
    }

    public static function argumentsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'required arguments' => [
                ['data' => $faker->numberBetween()],
                null,
                null,
            ],
            'optional arguments' => [
                ['data' => $faker->numberBetween()],
                $faker->word(),
                $faker->word(),
            ]
        ];
    }

    private function invokeProtectedMethod(string  $method,
                                           string  $endpoint,
                                           array   $data,
                                           ?string $command,
                                           ?string $apiVersion): void
    {
        // create a mock from Api class
        $stub = $this->createMock(Api::class);

        // set expected behaviour from mock
        $stub->expects($this->once())
            ->method('postRequest')
            ->with($endpoint, $data, $command, $apiVersion);

        // make a concrete class from an abstract class
        $resource = $this->getResource(ParameterHelper::create(), $apiVersion);

        // prepare property and method manipulation
        $reflection = new \ReflectionClass($resource);

        // make "api" property public
        $prop = $reflection->getProperty('api');
        $prop->setAccessible(true);
        $prop->setValue($resource, $stub);

        // make method public
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        // invoke method that became public
        $method->invoke($resource, $data, $command);
    }
}
