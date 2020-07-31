<?php

// Application config

return [
    // Service providers
    'providers'  => [

        // Application bootstrap
        \Handtuchsystem\Logger\LoggerServiceProvider::class,
        \Handtuchsystem\Exceptions\ExceptionsServiceProvider::class,
        \Handtuchsystem\Config\ConfigServiceProvider::class,
        \Handtuchsystem\Helpers\ConfigureEnvironmentServiceProvider::class,

        // Request handling
        \Handtuchsystem\Http\UrlGeneratorServiceProvider::class,
        \Handtuchsystem\Renderer\RendererServiceProvider::class,
        \Handtuchsystem\Database\DatabaseServiceProvider::class,
        \Handtuchsystem\Http\RequestServiceProvider::class,
        \Handtuchsystem\Http\SessionServiceProvider::class,
        \Handtuchsystem\Helpers\Translation\TranslationServiceProvider::class,
        \Handtuchsystem\Http\ResponseServiceProvider::class,
        \Handtuchsystem\Http\Psr7ServiceProvider::class,
        \Handtuchsystem\Helpers\AuthenticatorServiceProvider::class,
        \Handtuchsystem\Renderer\TwigServiceProvider::class,
        \Handtuchsystem\Middleware\RouteDispatcherServiceProvider::class,
        \Handtuchsystem\Middleware\RequestHandlerServiceProvider::class,
        \Handtuchsystem\Middleware\SessionHandlerServiceProvider::class,
        \Handtuchsystem\Http\Validation\ValidationServiceProvider::class,
        \Handtuchsystem\Http\RedirectServiceProvider::class,

        // Additional services
        \Handtuchsystem\Helpers\VersionServiceProvider::class,
        \Handtuchsystem\Mail\MailerServiceProvider::class,
        \Handtuchsystem\Http\HttpClientServiceProvider::class,
    ],

    // Application middleware
    'middleware' => [
        // Basic initialization
        \Handtuchsystem\Middleware\SendResponseHandler::class,
        \Handtuchsystem\Middleware\ExceptionHandler::class,

        // Changes of request/response parameters
        \Handtuchsystem\Middleware\SetLocale::class,
        \Handtuchsystem\Middleware\AddHeaders::class,

        // The application code
        \Handtuchsystem\Middleware\ErrorHandler::class,
        \Handtuchsystem\Middleware\VerifyCsrfToken::class,
        \Handtuchsystem\Middleware\RouteDispatcher::class,
        \Handtuchsystem\Middleware\SessionHandler::class,

        // Handle request
        \Handtuchsystem\Middleware\RequestHandler::class,
    ],
];
