<?php

namespace Leantime\Plugins\Whiteboards\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Leantime\Core\Db\Db;

class WhiteboardRepository
{
    private \Illuminate\Database\ConnectionInterface $connection;

    /**
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $db = app()->make(Db::class);
        $this->connection = $db->getConnection();
    }

    /**
     * Get all whiteboards for a project.
     */
    public function getAllByProject(int $projectId): array
    {
        $results = $this->connection->table('zp_whiteboards')
            ->where('projectId', $projectId)
            ->orderBy('title')
            ->get();

        return array_map(fn ($item) => (array) $item, $results->toArray());
    }

    /**
     * Get a single whiteboard by ID.
     */
    public function getById(int $id): ?array
    {
        $result = $this->connection->table('zp_whiteboards')
            ->where('id', $id)
            ->first();

        return $result ? (array) $result : null;
    }

    /**
     * Create a new whiteboard and return its ID.
     */
    public function create(array $values): int
    {
        $id = $this->connection->table('zp_whiteboards')
            ->insertGetId([
                'title' => $values['title'] ?? '',
                'projectId' => $values['projectId'] ?? 0,
                'author' => $values['author'] ?? 0,
                'sceneData' => $values['sceneData'] ?? '{}',
                'created' => $values['created'] ?? now(),
                'modified' => $values['modified'] ?? now(),
            ]);

        return (int) $id;
    }

    /**
     * Update a whiteboard's fields.
     */
    public function update(int $id, array $values): void
    {
        $this->connection->table('zp_whiteboards')
            ->where('id', $id)
            ->update($values);
    }

    /**
     * Delete a whiteboard.
     */
    public function delete(int $id): void
    {
        $this->connection->table('zp_whiteboards')
            ->where('id', $id)
            ->delete();
    }

    /**
     * Save scene data (JSON) for a whiteboard.
     */
    public function saveSceneData(int $id, string $sceneData): void
    {
        $this->connection->table('zp_whiteboards')
            ->where('id', $id)
            ->update([
                'sceneData' => $sceneData,
                'modified' => now(),
            ]);
    }

    /**
     * Rename a whiteboard.
     */
    public function rename(int $id, string $title): void
    {
        $this->connection->table('zp_whiteboards')
            ->where('id', $id)
            ->update([
                'title' => $title,
                'modified' => now(),
            ]);
    }
}
