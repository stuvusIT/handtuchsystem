<?php

namespace Handtuchsystem\Test\Unit\Controllers\Admin;

use Handtuchsystem\Config\Config;
use Handtuchsystem\Controllers\Admin\NewsController;
use Handtuchsystem\Helpers\Authenticator;
use Handtuchsystem\Http\Exceptions\ValidationException;
use Handtuchsystem\Http\Request;
use Handtuchsystem\Http\Response;
use Handtuchsystem\Http\UrlGenerator;
use Handtuchsystem\Http\UrlGeneratorInterface;
use Handtuchsystem\Http\Validation\Validator;
use Handtuchsystem\Models\News;
use Handtuchsystem\Models\User\User;
use Handtuchsystem\Test\Unit\HasDatabase;
use Handtuchsystem\Test\Unit\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class NewsControllerTest extends TestCase
{
    use HasDatabase;

    /** @var Authenticator|MockObject */
    protected $auth;

    /** @var array */
    protected $data = [
        [
            'title'      => 'Foo',
            'text'       => '<b>foo</b>',
            'is_meeting' => false,
            'user_id'    => 1,
        ]
    ];

    /** @var TestLogger */
    protected $log;

    /** @var Response|MockObject */
    protected $response;

    /** @var Request */
    protected $request;

    /**
     * @covers \Handtuchsystem\Controllers\Admin\NewsController::edit
     */
    public function testEditHtmlWarning()
    {
        $this->request->attributes->set('id', 1);
        $this->response->expects($this->once())
            ->method('withView')
            ->willReturnCallback(function ($view, $data) {
                $this->assertEquals('pages/news/edit.twig', $view);

                /** @var Collection $warnings */
                $warnings = $data['warnings'];
                $this->assertNotEmpty($data['news']);
                $this->assertTrue($warnings->isNotEmpty());
                $this->assertEquals('news.edit.contains-html', $warnings->first());

                return $this->response;
            });
        $this->addUser();

        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);

        $controller->edit($this->request);
    }

    /**
     * @covers \Handtuchsystem\Controllers\Admin\NewsController::__construct
     * @covers \Handtuchsystem\Controllers\Admin\NewsController::edit
     */
    public function testEdit()
    {
        $this->request->attributes->set('id', 1);
        $this->response->expects($this->once())
            ->method('withView')
            ->willReturnCallback(function ($view, $data) {
                $this->assertEquals('pages/news/edit.twig', $view);

                /** @var Collection $warnings */
                $warnings = $data['warnings'];
                $this->assertNotEmpty($data['news']);
                $this->assertTrue($warnings->isEmpty());

                return $this->response;
            });
        $this->auth->expects($this->once())
            ->method('can')
            ->with('admin_news_html')
            ->willReturn(true);

        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);

        $controller->edit($this->request);
    }

    /**
     * @covers \Handtuchsystem\Controllers\Admin\NewsController::edit
     */
    public function testEditIsMeeting()
    {
        $isMeeting = false;
        $this->response->expects($this->exactly(3))
            ->method('withView')
            ->willReturnCallback(
                function ($view, $data) use (&$isMeeting) {
                    $this->assertEquals($isMeeting, $data['is_meeting']);
                    $isMeeting = !$isMeeting;

                    return $this->response;
                }
            );
        $this->auth->expects($this->once())
            ->method('can')
            ->with('admin_news_html')
            ->willReturn(true);

        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);

        // Is no meeting
        $controller->edit($this->request);

        // Is meeting
        $this->request->query->set('meeting', 1);
        $controller->edit($this->request);

        // Should stay no meeting
        $this->request->attributes->set('id', 1);
        $controller->edit($this->request);
    }

    /**
     * @covers \Handtuchsystem\Controllers\Admin\NewsController::save
     */
    public function testSaveCreateInvalid()
    {
        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);
        $controller->setValidator(new Validator());

        $this->expectException(ValidationException::class);
        $controller->save($this->request);
    }

    /**
     * @return array
     */
    public function saveCreateEditProvider(): array
    {
        return [
            ['Some <b>test</b>', true, true, 'Some <b>test</b>'],
            ['Some <b>test</b>', false, false, 'Some test'],
            ['Some <b>test</b>', false, true, 'Some <b>test</b>', 1],
            ['Some <b>test</b>', true, false, 'Some test', 1],
        ];
    }

    /**
     * @covers       \Handtuchsystem\Controllers\Admin\NewsController::save
     * @dataProvider saveCreateEditProvider
     *
     * @param string $text
     * @param bool $isMeeting
     * @param bool $canEditHtml
     * @param string $result
     * @param int|null $id
     */
    public function testSaveCreateEdit(
        string $text,
        bool $isMeeting,
        bool $canEditHtml,
        string $result,
        int $id = null
    ) {
        $this->request->attributes->set('id', $id);
        $id = $id ?: 2;
        $body = [
            'title'      => 'Some Title',
            'text'       => $text,
        ];
        if ($isMeeting) {
            $body['is_meeting'] = '1';
        }

        $this->request = $this->request->withParsedBody($body);
        $this->addUser();
        $this->auth->expects($this->once())
            ->method('can')
            ->with('admin_news_html')
            ->willReturn($canEditHtml);
        $this->response->expects($this->once())
            ->method('redirectTo')
            ->with('http://localhost/news/' . $id)
            ->willReturn($this->response);

        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);
        $controller->setValidator(new Validator());

        $controller->save($this->request);

        $this->assertTrue($this->log->hasInfoThatContains('Updated'));

        /** @var Session $session */
        $session = $this->app->get('session');
        $messages = $session->get('messages');
        $this->assertEquals('news.edit.success', $messages[0]);

        $news = (new News())->find($id);
        $this->assertEquals($result, $news->text);
        $this->assertEquals($isMeeting, (bool)$news->is_meeting);
    }

    /**
     * @covers \Handtuchsystem\Controllers\Admin\NewsController::save
     */
    public function testSaveDelete()
    {
        $this->request->attributes->set('id', 1);
        $this->request = $this->request->withParsedBody([
            'title'  => '.',
            'text'   => '.',
            'delete' => '1',
        ]);
        $this->response->expects($this->once())
            ->method('redirectTo')
            ->with('http://localhost/news')
            ->willReturn($this->response);

        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);
        $controller->setValidator(new Validator());

        $controller->save($this->request);

        $this->assertTrue($this->log->hasInfoThatContains('Deleted'));

        /** @var Session $session */
        $session = $this->app->get('session');
        $messages = $session->get('messages');
        $this->assertEquals('news.delete.success', $messages[0]);
    }

    /**
     * Setup environment
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();

        $this->request = Request::create('http://localhost');
        $this->app->instance('request', $this->request);
        $this->app->instance(Request::class, $this->request);
        $this->app->instance(ServerRequestInterface::class, $this->request);

        $this->response = $this->createMock(Response::class);
        $this->app->instance(Response::class, $this->response);

        $this->log = new TestLogger();
        $this->app->instance(LoggerInterface::class, $this->log);

        $this->app->instance('session', new Session(new MockArraySessionStorage()));

        $this->auth = $this->createMock(Authenticator::class);
        $this->app->instance(Authenticator::class, $this->auth);

        $this->app->bind(UrlGeneratorInterface::class, UrlGenerator::class);

        $this->app->instance('config', new Config());

        (new News([
            'title'      => 'Foo',
            'text'       => '<b>foo</b>',
            'is_meeting' => false,
            'user_id'    => 1,
        ]))->save();
    }

    /**
     * Creates a new user
     */
    protected function addUser()
    {
        $user = new User([
            'name'          => 'foo',
            'password'      => '',
            'email'         => '',
            'api_key'       => '',
            'last_login_at' => null,
        ]);
        $user->forceFill(['id' => 42]);
        $user->save();

        $this->auth->expects($this->any())
            ->method('user')
            ->willReturn($user);
    }
}
