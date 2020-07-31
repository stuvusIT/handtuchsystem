<?php

namespace Handtuchsystem\Test\Unit\Mail;

use Handtuchsystem\Application;
use Handtuchsystem\Config\Config;
use Handtuchsystem\Mail\HandtuchsystemMailer;
use Handtuchsystem\Mail\Mailer;
use Handtuchsystem\Mail\MailerServiceProvider;
use Handtuchsystem\Mail\Transport\LogTransport;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Swift_Mailer as SwiftMailer;
use Swift_SendmailTransport as SendmailTransport;
use Swift_SmtpTransport as SmtpTransport;
use Swift_Transport as Transport;

class MailerServiceProviderTest extends ServiceProviderTest
{
    /** @var array */
    protected $defaultConfig = [
        'app_name' => 'Handtuchsystem App',
        'email'    => [
            'driver'   => 'mail',
            'from'     => [
                'name'    => 'Handtuchsystem',
                'address' => 'foo@bar.batz',
            ],
            'sendmail' => '/opt/bin/sendmail -bs',
        ],
    ];

    /** @var array */
    protected $smtpConfig = [
        'email' => [
            'driver'     => 'smtp',
            'host'       => 'mail.foo.bar',
            'port'       => 587,
            'encryption' => 'tls',
            'username'   => 'foobar',
            'password'   => 'LoremIpsum123',
        ],
    ];

    /**
     * @covers \Handtuchsystem\Mail\MailerServiceProvider::register
     */
    public function testRegister()
    {
        $app = $this->getApplication();

        $serviceProvider = new MailerServiceProvider($app);
        $serviceProvider->register();

        $this->assertExistsInContainer(['mailer.transport', Transport::class], $app);
        $this->assertExistsInContainer(['mailer.swift', SwiftMailer::class], $app);
        $this->assertExistsInContainer(['mailer', HandtuchsystemMailer::class, Mailer::class], $app);

        /** @var HandtuchsystemMailer $mailer */
        $mailer = $app->get('mailer');
        $this->assertEquals('Handtuchsystem App', $mailer->getSubjectPrefix());
        $this->assertEquals('Handtuchsystem', $mailer->getFromName());
        $this->assertEquals('foo@bar.batz', $mailer->getFromAddress());

        /** @var SendmailTransport $transport */
        $transport = $app->get('mailer.transport');
        $this->assertEquals($this->defaultConfig['email']['sendmail'], $transport->getCommand());
    }

    /**
     * @return array
     */
    public function provideTransports()
    {
        return [
            [LogTransport::class, ['email' => ['driver' => 'log']]],
            [SendmailTransport::class, ['email' => ['driver' => 'mail']]],
            [SendmailTransport::class, ['email' => ['driver' => 'sendmail']]],
            [
                SmtpTransport::class,
                $this->smtpConfig,
            ],
        ];
    }

    /**
     * @covers       \Handtuchsystem\Mail\MailerServiceProvider::getTransport
     * @param string $class
     * @param array  $emailConfig
     * @dataProvider provideTransports
     */
    public function testGetTransport($class, $emailConfig = [])
    {
        $app = $this->getApplication($emailConfig);

        $serviceProvider = new MailerServiceProvider($app);
        $serviceProvider->register();

        $transport = $app->get('mailer.transport');
        $this->assertInstanceOf($class, $transport);
    }

    /**
     * @covers \Handtuchsystem\Mail\MailerServiceProvider::getTransport
     */
    public function testGetTransportNotFound()
    {
        $app = $this->getApplication(['email' => ['driver' => 'foo-bar-batz']]);
        $this->expectException(InvalidArgumentException::class);

        $serviceProvider = new MailerServiceProvider($app);
        $serviceProvider->register();
    }

    /**
     * @covers \Handtuchsystem\Mail\MailerServiceProvider::getSmtpTransport
     */
    public function testGetSmtpTransport()
    {
        $app = $this->getApplication($this->smtpConfig);

        $serviceProvider = new MailerServiceProvider($app);
        $serviceProvider->register();

        /** @var SmtpTransport $transport */
        $transport = $app->get('mailer.transport');

        $this->assertEquals($this->smtpConfig['email']['host'], $transport->getHost());
        $this->assertEquals($this->smtpConfig['email']['port'], $transport->getPort());
        $this->assertEquals($this->smtpConfig['email']['encryption'], $transport->getEncryption());
        $this->assertEquals($this->smtpConfig['email']['username'], $transport->getUsername());
        $this->assertEquals($this->smtpConfig['email']['password'], $transport->getPassword());
    }

    /**
     * @param array $configuration
     * @return Application
     */
    protected function getApplication($configuration = []): Application
    {
        $app = new Application();

        $configuration = new Config(array_replace_recursive($this->defaultConfig, $configuration));
        $app->instance('config', $configuration);

        $logger = $this->getMockForAbstractClass(LoggerInterface::class);
        $app->instance(LoggerInterface::class, $logger);

        return $app;
    }

    /**
     * @param string[]    $abstracts
     * @param Application $container
     */
    protected function assertExistsInContainer($abstracts, $container)
    {
        $first = array_shift($abstracts);
        $this->assertContainerHas($first, $container);

        foreach ($abstracts as $abstract) {
            $this->assertContainerHas($abstract, $container);
            $this->assertEquals($container->get($first), $container->get($abstract));
        }
    }

    /**
     * @param string      $abstract
     * @param Application $container
     */
    protected function assertContainerHas($abstract, $container)
    {
        $this->assertTrue(
            $container->has($abstract) || $container->hasMethodBinding($abstract),
            sprintf('Container does not contain abstract %s', $abstract)
        );
    }
}
