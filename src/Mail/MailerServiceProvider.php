<?php

namespace Handtuchsystem\Mail;

use Handtuchsystem\Config\Config;
use Handtuchsystem\Container\ServiceProvider;
use Handtuchsystem\Mail\Transport\LogTransport;
use InvalidArgumentException;
use Swift_Mailer as SwiftMailer;
use Swift_SendmailTransport as SendmailTransport;
use Swift_SmtpTransport as SmtpTransport;
use Swift_Transport as Transport;

class MailerServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** @var Config $config */
        $config = $this->app->get('config');
        $mailConfig = $config->get('email');

        $transport = $this->getTransport($mailConfig['driver'], $mailConfig);
        $this->app->instance(Transport::class, $transport);
        $this->app->instance('mailer.transport', $transport);

        /** @var SwiftMailer $swiftMailer */
        $swiftMailer = $this->app->make(SwiftMailer::class);
        $this->app->instance(SwiftMailer::class, $swiftMailer);
        $this->app->instance('mailer.swift', $swiftMailer);

        /** @var HandtuchsystemMailer $mailer */
        $mailer = $this->app->make(HandtuchsystemMailer::class);
        $mailer->setFromAddress($mailConfig['from']['address']);
        $mailer->setSubjectPrefix($config->get('app_name'));
        if (!empty($mailConfig['from']['name'])) {
            $mailer->setFromName($mailConfig['from']['name']);
        }

        $this->app->instance(HandtuchsystemMailer::class, $mailer);
        $this->app->instance(Mailer::class, $mailer);
        $this->app->instance('mailer', $mailer);
    }

    /**
     * @param string $transport
     * @param array  $config
     * @return Transport
     */
    protected function getTransport($transport, $config)
    {
        switch ($transport) {
            case 'log':
                return $this->app->make(LogTransport::class);
            case 'mail':
            case 'sendmail':
                return $this->app->make(SendmailTransport::class, ['command' => $config['sendmail']]);
            case 'smtp':
                return $this->getSmtpTransport($config);
        }

        throw new InvalidArgumentException(sprintf('Mail driver "%s" not found', $transport));
    }

    /**
     * @param array $config
     * @return SmtpTransport
     */
    protected function getSmtpTransport(array $config)
    {
        /** @var SmtpTransport $transport */
        $transport = $this->app->make(SmtpTransport::class, [
            'host'       => $config['host'],
            'port'       => $config['port'],
            'encryption' => $config['encryption'],
        ]);

        if ($config['username']) {
            $transport->setUsername($config['username']);
        }

        if ($config['password']) {
            $transport->setPassword($config['password']);
        }

        return $transport;
    }
}
