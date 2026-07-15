<?php

namespace Leantime\Plugins\Whiteboards\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Auth\Services\Auth;
use Leantime\Domain\Auth\Models\Roles;
use Leantime\Plugins\Whiteboards\Services\Whiteboards;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Delete extends Controller
{
    private Whiteboards $service;

    public function init(Whiteboards $service): void
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);
        $this->service = $service;
    }

    /**
     * GET /whiteboards/delete/{id}
     * Delete a whiteboard and redirect back to the listing.
     */
    public function get(): Response
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id > 0) {
            $this->service->delete($id);
        }

        return new RedirectResponse(BASE_URL . '/whiteboards/showAll');
    }
}
