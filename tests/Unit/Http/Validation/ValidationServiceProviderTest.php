<?php

namespace Handtuchsystem\Test\Unit\Http\Validation;

use Handtuchsystem\Application;
use Handtuchsystem\Http\Validation\ValidationServiceProvider;
use Handtuchsystem\Http\Validation\Validator;
use Handtuchsystem\Test\Unit\Http\Validation\Stub\ValidatesRequestImplementation;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use stdClass;

class ValidationServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Http\Validation\ValidationServiceProvider::register
     */
    public function testRegister()
    {
        $app = new Application();

        $serviceProvider = new ValidationServiceProvider($app);
        $serviceProvider->register();

        $this->assertTrue($app->has(Validator::class));
        $this->assertTrue($app->has('validator'));

        /** @var ValidatesRequestImplementation $validatesRequest */
        $validatesRequest = $app->make(ValidatesRequestImplementation::class);
        $this->assertTrue($validatesRequest->hasValidator());

        // Test afterResolving early return
        $app->make(stdClass::class);
    }
}
