<?php

namespace Handtuchsystem\Test\Unit\Mail\Transport;

use Handtuchsystem\Test\Unit\Mail\Transport\Stub\TransportImplementation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swift_Events_EventListener;
use Swift_Mime_SimpleMessage as SimpleMessage;

class TransportTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Mail\Transport\Transport::isStarted
     * @covers \Handtuchsystem\Mail\Transport\Transport::ping
     * @covers \Handtuchsystem\Mail\Transport\Transport::registerPlugin
     * @covers \Handtuchsystem\Mail\Transport\Transport::start
     * @covers \Handtuchsystem\Mail\Transport\Transport::stop
     */
    public function testMethods()
    {
        /** @var Swift_Events_EventListener|MockObject $plugin */
        $plugin = $this->getMockForAbstractClass(Swift_Events_EventListener::class);

        $transport = new TransportImplementation();

        $transport->start();
        $transport->registerPlugin($plugin);

        $this->assertTrue($transport->isStarted());
        $this->assertTrue($transport->ping());

        $transport->stop();
    }

    /**
     * @covers \Handtuchsystem\Mail\Transport\Transport::allRecipients
     */
    public function testAllRecipients()
    {
        /** @var SimpleMessage|MockObject $message */
        $message = $this->createMock(SimpleMessage::class);
        $transport = new TransportImplementation();
        $message->expects($this->once())
            ->method('getTo')
            ->willReturn([
                'foo@bar.batz'      => 'Foo Bar',
                'lorem@ipsum.dolor' => null,
            ]);
        $message->expects($this->once())
            ->method('getCc')
            ->willReturn([
                'to@bar.batz' => null,
            ]);
        $message->expects($this->once())
            ->method('getBcc')
            ->willReturn([
                'secret@bar.batz' => 'I\'m secret!',
            ]);

        $this->assertEquals(
            [
                'foo@bar.batz'      => 'Foo Bar',
                'lorem@ipsum.dolor' => null,
                'to@bar.batz'       => null,
                'secret@bar.batz'   => 'I\'m secret!',
            ],
            $transport->getAllRecipients($message)
        );
    }

    /**
     * @covers \Handtuchsystem\Mail\Transport\Transport::formatTo
     * @covers \Handtuchsystem\Mail\Transport\Transport::getTo
     */
    public function testGetTo()
    {
        /** @var SimpleMessage|MockObject $message */
        $message = $this->createMock(SimpleMessage::class);
        /** @var TransportImplementation|MockObject $transport */
        $transport = $this->getMockBuilder(TransportImplementation::class)
            ->onlyMethods(['allRecipients'])
            ->getMock();
        $transport->expects($this->once())
            ->method('allRecipients')
            ->with($message)
            ->willReturn([
                'foo@bar.batz'      => null,
                'lorem@ipsum.dolor' => 'Developer',
            ]);

        $return = $transport->getGetTo($message);
        $this->assertEquals('foo@bar.batz,Developer <lorem@ipsum.dolor>', $return);
    }
}
