<?php

namespace Handtuchsystem\Test\Unit\Mail;

use Handtuchsystem\Mail\Mailer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swift_Mailer as SwiftMailer;
use Swift_Message as SwiftMessage;

class MailerTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Mail\Mailer::__construct
     * @covers \Handtuchsystem\Mail\Mailer::getFromAddress
     * @covers \Handtuchsystem\Mail\Mailer::getFromName
     * @covers \Handtuchsystem\Mail\Mailer::setFromAddress
     * @covers \Handtuchsystem\Mail\Mailer::setFromName
     */
    public function testInitAndSettersAndGetters()
    {
        /** @var SwiftMailer|MockObject $swiftMailer */
        $swiftMailer = $this->createMock(SwiftMailer::class);

        $mailer = new Mailer($swiftMailer);

        $mailer->setFromName('From Name');
        $this->assertEquals('From Name', $mailer->getFromName());

        $mailer->setFromAddress('from@foo.bar');
        $this->assertEquals('from@foo.bar', $mailer->getFromAddress());
    }

    /**
     * @covers \Handtuchsystem\Mail\Mailer::send
     */
    public function testSend()
    {
        /** @var SwiftMessage|MockObject $message */
        $message = $this->createMock(SwiftMessage::class);
        /** @var SwiftMailer|MockObject $swiftMailer */
        $swiftMailer = $this->createMock(SwiftMailer::class);
        $swiftMailer->expects($this->once())
            ->method('createMessage')
            ->willReturn($message);
        $swiftMailer->expects($this->once())
            ->method('send')
            ->willReturn(1);

        $message->expects($this->once())
            ->method('setTo')
            ->with(['to@xam.pel'])
            ->willReturn($message);

        $message->expects($this->once())
            ->method('setFrom')
            ->with('foo@bar.baz', 'Lorem Ipsum')
            ->willReturn($message);

        $message->expects($this->once())
            ->method('setSubject')
            ->with('Foo Bar')
            ->willReturn($message);

        $message->expects($this->once())
            ->method('setBody')
            ->with('Lorem Ipsum!')
            ->willReturn($message);

        $mailer = new Mailer($swiftMailer);
        $mailer->setFromAddress('foo@bar.baz');
        $mailer->setFromName('Lorem Ipsum');

        $return = $mailer->send('to@xam.pel', 'Foo Bar', 'Lorem Ipsum!');
        $this->assertEquals(1, $return);
    }
}
