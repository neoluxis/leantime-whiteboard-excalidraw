<?php

namespace Leantime\Plugins\Whiteboards\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Auth\Services\Auth;
use Leantime\Domain\Auth\Models\Roles;
use Leantime\Plugins\Whiteboards\Services\Whiteboards;
use Symfony\Component\HttpFoundation\Response;

class Create extends Controller
{
    private Whiteboards $service;

    public function init(Whiteboards $service): void
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);
        $this->service = $service;
    }

    /**
     * POST /whiteboards/create
     * Create a new whiteboard and redirect to the editor.
     */
    public function post(array $params): Response
    {
        $title = trim($params['title'] ?? '');

        if ($title === '') {
            $title = __('label.untitled_whiteboard');
        }

        $projectId = (int) session('currentProject');
        $authorId = (int) Auth::getUserId();

        $newId = $this->service->create($projectId, $title, $authorId);

        return new \Symfony\Component\HttpFoundation\RedirectResponse(
            BASE_URL . '/whiteboards/showWhiteboard/' . $newId
        );
    }
}
