<?php

namespace Leantime\Plugins\Whiteboards\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Leantime\Plugins\Whiteboards\Repositories\WhiteboardRepository;

class Whiteboards
{
    private WhiteboardRepository $repository;

    public function __construct()
    {
        $this->repository = new WhiteboardRepository();
    }

    /**
     * Install the plugin: create the zp_whiteboards table.
     */
    public function install(): void
    {
        if (!Schema::hasTable('zp_whiteboards')) {
            Schema::create('zp_whiteboards', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->integer('projectId');
                $table->integer('author');
                $table->longText('sceneData')->nullable();
                $table->dateTime('created')->nullable();
                $table->dateTime('modified')->nullable();
                $table->index('projectId');
            });
        }
    }

    /**
     * Uninstall the plugin: drop the zp_whiteboards table.
     */
    public function uninstall(): void
    {
        Schema::dropIfExists('zp_whiteboards');
    }

    /**
     * Get all whiteboards for a project.
     */
    public function getAllByProject(int $projectId): array
    {
        return $this->repository->getAllByProject($projectId);
    }

    /**
     * Get a single whiteboard by ID.
     */
    public function getById(int $id): ?array
    {
        return $this->repository->getById($id);
    }

    /**
     * Create a new whiteboard.
     */
    public function create(int $projectId, string $title, int $authorId): int
    {
        return $this->repository->create([
            'title' => $title,
            'projectId' => $projectId,
            'author' => $authorId,
            'sceneData' => '{}',
            'created' => now(),
            'modified' => now(),
        ]);
    }

    /**
     * Save scene data for a whiteboard.
     * Validates that the whiteboard belongs to the current project.
     */
    public function updateSceneData(int $id, string $sceneData): void
    {
        $whiteboard = $this->repository->getById($id);
        if (!$whiteboard) {
            throw new \RuntimeException('Whiteboard not found');
        }

        $currentProject = session('currentProject');
        if ($whiteboard['projectId'] != $currentProject) {
            throw new \RuntimeException('Access denied: whiteboard does not belong to current project');
        }

        $this->repository->saveSceneData($id, $sceneData);
    }

    /**
     * Rename a whiteboard.
     */
    public function rename(int $id, string $title): void
    {
        $this->repository->rename($id, $title);
    }

    /**
     * Delete a whiteboard.
     */
    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}
