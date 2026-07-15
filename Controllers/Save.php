<?php

namespace Leantime\Plugins\Whiteboards\Controllers;

use Leantime\Core\Controller\Controller;
use Leantime\Domain\Auth\Services\Auth;
use Leantime\Domain\Auth\Models\Roles;
use Leantime\Plugins\Whiteboards\Services\Whiteboards;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Save extends Controller
{
    private Whiteboards $service;

    public function init(Whiteboards $service): void
    {
        Auth::authOrRedirect([Roles::$owner, Roles::$admin, Roles::$manager, Roles::$editor]);
        $this->service = $service;
    }

    /**
     * POST /whiteboards/save/{id}
     * AJAX endpoint to save Excalidraw scene data.
     * Accepts both form-encoded and JSON request bodies.
     */
    public function post(array $params): Response
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id === 0) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid whiteboard ID'], 400);
        }

        // Handle JSON body (Content-Type: application/json)
        $sceneData = $params['sceneData'] ?? '';
        if (empty($sceneData)) {
            $content = $this->incomingRequest->getContent();
            if (!empty($content)) {
                $json = json_decode($content, true);
                $sceneData = $json['sceneData'] ?? '';
            }
        }

        if (empty($sceneData)) {
            return new JsonResponse(['status' => 'error', 'message' => 'No scene data provided'], 400);
        }

        try {
            $this->service->updateSceneData($id, $sceneData);
            return new JsonResponse(['status' => 'ok']);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 403);
        }
    }
}
