<?php

namespace Handtuchsystem\Controllers\Admin;

use Handtuchsystem\Controllers\BaseController;
use Handtuchsystem\Http\Request;
use Handtuchsystem\Http\Response;
use Handtuchsystem\Models\LogEntry;

class LogsController extends BaseController
{
    /** @var LogEntry */
    protected $log;

    /** @var Response */
    protected $response;

    /** @var array */
    protected $permissions = [
        'admin_log',
    ];

    /**
     * @param LogEntry $log
     * @param Response $response
     */
    public function __construct(LogEntry $log, Response $response)
    {
        $this->log = $log;
        $this->response = $response;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $entries = $this->log->filter($search);

        return $this->response->withView(
            'admin/log.twig',
            ['entries' => $entries, 'search' => $search]
        );
    }
}
