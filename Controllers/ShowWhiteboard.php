<?php

namespace Leantime\Plugins\Whiteboards\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Auth\Services\Auth;
use Leantime\Domain\Auth\Models\Roles;
use Leantime\Plugins\Whiteboards\Services\Whiteboards;
use Symfony\Component\HttpFoundation\Response;

class ShowWhiteboard extends Controller
{
    private Whiteboards $service;

    public function init(Whiteboards $service): void
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor, Roles::$commenter]);
        $this->service = $service;
    }

    /**
     * GET /whiteboards/showWhiteboard/{id}
     * Display the full-screen Excalidraw editor for a whiteboard.
     */
    public function get(): Response
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id === 0) {
            return new Response('Whiteboard ID is required', 400);
        }

        $whiteboard = $this->service->getById($id);

        if (!$whiteboard) {
            return new Response('Whiteboard not found', 404);
        }

        // Verify the whiteboard belongs to the current project
        $currentProject = (int) session('currentProject');
        if ($whiteboard['projectId'] != $currentProject) {
            return new Response('Access denied', 403);
        }

        $this->tpl->assign('whiteboard', $whiteboard);

        return $this->tpl->display('whiteboards.showWhiteboard');
    }
}
