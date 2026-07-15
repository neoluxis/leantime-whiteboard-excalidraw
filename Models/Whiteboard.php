<?php

namespace Leantime\Plugins\Whiteboards\Models;

class Whiteboard
{
    public function __construct(
        public int $id = 0,
        public string $title = '',
        public int $projectId = 0,
        public int $author = 0,
        public string $sceneData = '{}',
        public ?string $created = null,
        public ?string $modified = null,
    ) {}
}
