<?php

namespace Leantime\Plugins\Whiteboards\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Auth\Services\Auth;
use Leantime\Domain\Auth\Models\Roles;
use Leantime\Plugins\Whiteboards\Services\Whiteboards;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Rename extends Controller
{
    private Whiteboards $service;

    public function init(Whiteboards $service): void
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);
        $this->service = $service;
    }

    /**
     * POST /whiteboards/rename/{id}
     * Rename a whiteboard and redirect back to the listing.
     */
    public function post(array $params): Response
    {
        $id = (int) ($_GET['id'] ?? 0);
        $title = trim($params['title'] ?? '');

        if ($id === 0 || $title === '') {
            return new RedirectResponse(BASE_URL . '/whiteboards/showAll');
        }

        $this->service->rename($id, $title);

        return new RedirectResponse(BASE_URL . '/whiteboards/showAll');
    }
}
