<?php

namespace Handtuchsystem\Test\Unit\Mail;

use Handtuchsystem\Helpers\Translation\Translator;
use Handtuchsystem\Mail\HandtuchsystemMailer;
use Handtuchsystem\Models\User\Contact;
use Handtuchsystem\Models\User\Settings;
use Handtuchsystem\Models\User\User;
use Handtuchsystem\Renderer\Renderer;
use Handtuchsystem\Test\Unit\HasDatabase;
use Handtuchsystem\Test\Unit\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Swift_Mailer as SwiftMailer;
use Swift_Message as SwiftMessage;

class HandtuchsystemMailerTest extends TestCase
{
    use HasDatabase;

    /**
     * @covers \Handtuchsystem\Mail\HandtuchsystemMailer::__construct
     * @covers \Handtuchsystem\Mail\HandtuchsystemMailer::sendView
     */
    public function testSendView()
    {
        /** @var Renderer|MockObject $view */
        $view = $this->createMock(Renderer::class);
        /** @var SwiftMailer|MockObject $swiftMailer */
        $swiftMailer = $this->createMock(SwiftMailer::class);
        /** @var HandtuchsystemMailer|MockObject $mailer */
        $mailer = $this->getMockBuilder(HandtuchsystemMailer::class)
            ->setConstructorArgs(['mailer' => $swiftMailer, 'view' => $view])
            ->onlyMethods(['send'])
            ->getMock();
        $this->setExpects($mailer, 'send', ['foo@bar.baz', 'Lorem dolor', 'Rendered Stuff!'], 1);
        $this->setExpects($view, 'render', ['test/template.tpl', ['dev' => true]], 'Rendered Stuff!');

        $return = $mailer->sendView('foo@bar.baz', 'Lorem dolor', 'test/template.tpl', ['dev' => true]);
        $this->assertEquals(1, $return);
    }

    /**
     * @covers \Handtuchsystem\Mail\HandtuchsystemMailer::sendViewTranslated
     */
    public function testSendViewTranslated()
    {
        $this->initDatabase();

        $settings = new Settings([
            'language' => 'de_DE',
            'theme'    => '',
        ]);
        $contact = new Contact(['email' => null]);
        $user = new User([
            'id'       => 42,
            'name'     => 'username',
            'email'    => 'foo@bar.baz',
            'password' => '',
            'api_key'  => '',
        ]);
        $user->save();
        $settings->user()->associate($user)->save();
        $contact->user()->associate($user)->save();

        /** @var Renderer|MockObject $view */
        $view = $this->createMock(Renderer::class);
        /** @var SwiftMailer|MockObject $swiftMailer */
        $swiftMailer = $this->createMock(SwiftMailer::class);
        /** @var Translator|MockObject $translator */
        $translator = $this->createMock(Translator::class);

        /** @var HandtuchsystemMailer|MockObject $mailer */
        $mailer = $this->getMockBuilder(HandtuchsystemMailer::class)
            ->setConstructorArgs(['mailer' => $swiftMailer, 'view' => $view, 'translation' => $translator])
            ->onlyMethods(['sendView'])
            ->getMock();

        $this->setExpects($mailer, 'sendView', ['foo@bar.baz', 'Lorem dolor', 'test/template.tpl', ['dev' => true]], 1);
        $this->setExpects($translator, 'getLocales', null, ['de_DE' => 'de_DE', 'en_US' => 'en_US']);
        $this->setExpects($translator, 'getLocale', null, 'en_US');
        $this->setExpects($translator, 'translate', ['translatable.text'], 'Lorem dolor');
        $translator->expects($this->exactly(2))
            ->method('setLocale')
            ->withConsecutive(['de_DE'], ['en_US']);

        $return = $mailer->sendViewTranslated(
            $user,
            'translatable.text',
            'test/template.tpl',
            ['dev' => true],
            'de_DE'
        );
        $this->assertEquals(1, $return);
    }

    /**
     * @covers \Handtuchsystem\Mail\HandtuchsystemMailer::getSubjectPrefix
     * @covers \Handtuchsystem\Mail\HandtuchsystemMailer::send
     * @covers \Handtuchsystem\Mail\HandtuchsystemMailer::setSubjectPrefix
     */
    public function testSend()
    {
        /** @var SwiftMessage|MockObject $message */
        $message = $this->createMock(SwiftMessage::class);
        /** @var SwiftMailer|MockObject $swiftMailer */
        $swiftMailer = $this->createMock(SwiftMailer::class);
        $this->setExpects($swiftMailer, 'createMessage', null, $message);
        $this->setExpects($swiftMailer, 'send', null, 1);
        $this->setExpects($message, 'setTo', [['to@xam.pel']], $message);
        $this->setExpects($message, 'setFrom', ['foo@bar.baz', 'Lorem Ipsum'], $message);
        $this->setExpects($message, 'setSubject', ['[Mail test] Foo Bar'], $message);
        $this->setExpects($message, 'setBody', ['Lorem Ipsum!'], $message);

        $mailer = new HandtuchsystemMailer($swiftMailer);
        $mailer->setFromAddress('foo@bar.baz');
        $mailer->setFromName('Lorem Ipsum');
        $mailer->setSubjectPrefix('Mail test');

        $this->assertEquals('Mail test', $mailer->getSubjectPrefix());

        $return = $mailer->send('to@xam.pel', 'Foo Bar', 'Lorem Ipsum!');
        $this->assertEquals(1, $return);
    }
}
