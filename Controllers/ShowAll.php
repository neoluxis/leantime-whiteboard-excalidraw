<?php

namespace Leantime\Plugins\Whiteboards\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Auth\Services\Auth;
use Leantime\Domain\Auth\Models\Roles;
use Leantime\Plugins\Whiteboards\Services\Whiteboards;
use Symfony\Component\HttpFoundation\Response;

class ShowAll extends Controller
{
    private Whiteboards $service;

    public function init(Whiteboards $service): void
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor, Roles::$commenter]);
        $this->service = $service;
    }

    /**
     * GET /whiteboards/showAll
     * List all whiteboards for the current project.
     */
    public function get(): Response
    {
        $currentProject = session('currentProject');
        $whiteboards = $this->service->getAllByProject((int) $currentProject);

        $this->tpl->assign('whiteboards', $whiteboards);
        $this->tpl->assign('currentProject', $currentProject);

        return $this->tpl->display('whiteboards.showAll');
    }
}
