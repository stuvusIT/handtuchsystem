<?php

namespace Handtuchsystem\Mail\Transport;

use Psr\Log\LoggerInterface;
use Swift_Mime_SimpleMessage as SimpleMessage;

class LogTransport extends Transport
{
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients
     *
     * @param SimpleMessage $message
     * @param string[]      $failedRecipients An array of failures by-reference
     *
     * @return int
     */
    public function send(
        SimpleMessage $message,
        &$failedRecipients = null
    ): int {
        $this->logger->debug(
            'Mail: Send mail "{title}" to "{recipients}":' . PHP_EOL . PHP_EOL . '{content}',
            [
                'title'      => $message->getSubject(),
                'recipients' => $this->getTo($message),
                'content'    => $message->toString(),
            ]
        );

        return count($this->allRecipients($message));
    }
}
