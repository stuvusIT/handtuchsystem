<?php

namespace Handtuchsystem\Test\Unit\Controllers\Admin;

use Handtuchsystem\Controllers\Admin\LogsController;
use Handtuchsystem\Http\Request;
use Handtuchsystem\Http\Response;
use Handtuchsystem\Models\LogEntry;
use Handtuchsystem\Test\Unit\HasDatabase;
use Handtuchsystem\Test\Unit\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Psr\Log\LogLevel;

class LogsControllerTest extends TestCase
{
    use HasDatabase;

    /**
     * @covers \Handtuchsystem\Controllers\Admin\LogsController::index
     * @covers \Handtuchsystem\Controllers\Admin\LogsController::__construct
     */
    public function testIndex()
    {
        $log = new LogEntry();
        $alert = $log->create(['level' => LogLevel::ALERT, 'message' => 'Alert test']);
        $alert = $log->find($alert)->first();
        $error = $log->create(['level' => LogLevel::ERROR, 'message' => 'Error test']);
        $error = $log->find($error)->first();

        $response = $this->createMock(Response::class);
        $response->expects($this->exactly(2))
            ->method('withView')
            ->withConsecutive(
                ['admin/log.twig', ['entries' => new Collection([$error, $alert]), 'search' => null]],
                ['admin/log.twig', ['entries' => new Collection([$error]), 'search' => 'error']]
            )
            ->willReturn($response);

        $request = Request::create('/');

        $controller = new LogsController($log, $response);
        $controller->index($request);

        $request->request->set('search', 'error');
        $controller->index($request);
    }

    /**
     * Setup the DB
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->initDatabase();
    }
}
