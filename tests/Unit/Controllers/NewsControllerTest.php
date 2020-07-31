<?php

namespace Handtuchsystem\Test\Unit\Controllers;

use Handtuchsystem\Config\Config;
use Handtuchsystem\Controllers\NewsController;
use Handtuchsystem\Helpers\Authenticator;
use Handtuchsystem\Http\Exceptions\ValidationException;
use Handtuchsystem\Http\Request;
use Handtuchsystem\Http\Response;
use Handtuchsystem\Http\UrlGenerator;
use Handtuchsystem\Http\UrlGeneratorInterface;
use Handtuchsystem\Http\Validation\Validator;
use Handtuchsystem\Models\News;
use Handtuchsystem\Models\NewsComment;
use Handtuchsystem\Models\User\User;
use Handtuchsystem\Test\Unit\HasDatabase;
use Handtuchsystem\Test\Unit\TestCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            'text'       => 'foo',
            'is_meeting' => false,
            'user_id'    => 1,
        ],
        [
            'title'      => 'Bar',
            'text'       => 'bar',
            'is_meeting' => false,
            'user_id'    => 1,
        ],
        [
            'title'      => 'baz',
            'text'       => 'baz',
            'is_meeting' => true,
            'user_id'    => 1,
        ],
        [
            'title'      => 'Lorem',
            'text'       => 'lorem',
            'is_meeting' => false,
            'user_id'    => 1,
        ],
        [
            'title'      => 'Ipsum',
            'text'       => 'ipsum',
            'is_meeting' => true,
            'user_id'    => 1,
        ],
        [
            'title'      => 'Dolor',
            'text'       => 'test',
            'is_meeting' => true,
            'user_id'    => 1,
        ],
    ];

    /** @var TestLogger */
    protected $log;

    /** @var Response|MockObject */
    protected $response;

    /** @var Request */
    protected $request;

    /**
     * @covers \Handtuchsystem\Controllers\NewsController::__construct
     * @covers \Handtuchsystem\Controllers\NewsController::index
     * @covers \Handtuchsystem\Controllers\NewsController::meetings
     * @covers \Handtuchsystem\Controllers\NewsController::showOverview
     * @covers \Handtuchsystem\Controllers\NewsController::renderView
     */
    public function testIndex()
    {
        $this->request->attributes->set('page', 2);

        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);

        $n = 1;
        $this->response->expects($this->exactly(3))
            ->method('withView')
            ->willReturnCallback(
                function (string $page, array $data) use (&$n) {
                    $this->assertEquals('pages/news/overview.twig', $page);
                    /** @var Collection $news */
                    $news = $data['news'];

                    switch ($n) {
                        case 1:
                            // Show everything
                            $this->assertFalse($data['only_meetings']);
                            $this->assertTrue($news->isNotEmpty());
                            $this->assertEquals(3, $data['pages']);
                            $this->assertEquals(2, $data['page']);
                            break;
                        case 2:
                            // Show meetings
                            $this->assertTrue($data['only_meetings']);
                            $this->assertTrue($news->isNotEmpty());
                            $this->assertEquals(1, $data['pages']);
                            $this->assertEquals(1, $data['page']);
                            break;
                        default:
                            // No news found
                            $this->assertTrue($news->isEmpty());
                            $this->assertEquals(1, $data['pages']);
                            $this->assertEquals(1, $data['page']);
                    }

                    $n++;
                    return $this->response;
                }
            );

        $controller->index();
        $controller->meetings();

        News::query()->truncate();
        $controller->index();
    }

    /**
     * @covers \Handtuchsystem\Controllers\NewsController::show
     */
    public function testShow()
    {
        $this->request->attributes->set('id', 1);
        $this->response->expects($this->once())
            ->method('withView')
            ->with('pages/news/news.twig')
            ->willReturn($this->response);

        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);

        $controller->show($this->request);
    }

    /**
     * @covers \Handtuchsystem\Controllers\NewsController::show
     */
    public function testShowNotFound()
    {
        $this->request->attributes->set('id', 42);

        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);

        $this->expectException(ModelNotFoundException::class);
        $controller->show($this->request);
    }

    /**
     * @covers \Handtuchsystem\Controllers\NewsController::comment
     */
    public function testCommentInvalid()
    {
        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);
        $controller->setValidator(new Validator());

        $this->expectException(ValidationException::class);
        $controller->comment($this->request);
    }

    /**
     * @covers \Handtuchsystem\Controllers\NewsController::comment
     */
    public function testCommentNewsNotFound()
    {
        $this->request->attributes->set('id', 42);
        $this->request = $this->request->withParsedBody(['comment' => 'Foo bar!']);
        $this->addUser();

        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);
        $controller->setValidator(new Validator());

        $this->expectException(ModelNotFoundException::class);
        $controller->comment($this->request);
    }

    /**
     * @covers \Handtuchsystem\Controllers\NewsController::comment
     */
    public function testComment()
    {
        $this->request->attributes->set('id', 1);
        $this->request = $this->request->withParsedBody(['comment' => 'Foo bar!']);
        $this->addUser();

        $this->response->expects($this->once())
            ->method('redirectTo')
            ->willReturn($this->response);

        /** @var NewsController $controller */
        $controller = $this->app->make(NewsController::class);
        $controller->setValidator(new Validator());

        $controller->comment($this->request);
        $this->log->hasInfoThatContains('Created news comment');

        /** @var NewsComment $comment */
        $comment = NewsComment::whereNewsId(1)->first();
        $this->assertEquals('Foo bar!', $comment->text);
    }

    /**
     * Setup environment
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();

        $this->request = new Request();
        $this->app->instance('request', $this->request);
        $this->app->instance(Request::class, $this->request);
        $this->app->instance(ServerRequestInterface::class, $this->request);

        $this->response = $this->createMock(Response::class);
        $this->app->instance(Response::class, $this->response);

        $this->app->instance(Config::class, new Config(['display_news' => 2]));

        $this->log = new TestLogger();
        $this->app->instance(LoggerInterface::class, $this->log);

        $this->app->instance('session', new Session(new MockArraySessionStorage()));

        $this->auth = $this->createMock(Authenticator::class);
        $this->app->instance(Authenticator::class, $this->auth);

        $this->app->bind(UrlGeneratorInterface::class, UrlGenerator::class);

        $this->app->instance('config', new Config());

        foreach ($this->data as $news) {
            (new News($news))->save();
        }
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
