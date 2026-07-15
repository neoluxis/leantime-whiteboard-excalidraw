# Leantime Whiteboard (Excalidraw) Plugin

Adds collaborative whiteboards to Leantime projects using [Excalidraw](https://excalidraw.com).

## Features

- Multiple whiteboards per project
- Full Excalidraw editor integration with auto-save
- `Ctrl+S` to save instantly
- Whiteboard listing with inline rename and delete
- Sidebar menu item under "Make" section
- Project detail page tab

## Requirements

- Leantime >= 3.8.0
- Docker deployment (for JS assets)

## Installation

### 1. Deploy plugin files

```bash
# Copy plugin to Leantime's writable plugins directory
cp -r leantime-whiteboard-excalidraw /srv/leantime/data/plugins/Whiteboards
```

> **Important**: The folder **must** be named `Whiteboards` to match URL routing (`/whiteboards/...`).

### 2. Copy JS assets (Docker)

```bash
# Download React + Excalidraw to local public directory
docker exec leantime mkdir -p /var/www/html/public/userfiles/whiteboard
docker exec leantime curl -sL -o /var/www/html/public/userfiles/whiteboard/react.production.min.js https://unpkg.com/react@18/umd/react.production.min.js
docker exec leantime curl -sL -o /var/www/html/public/userfiles/whiteboard/react-dom.production.min.js https://unpkg.com/react-dom@18/umd/react-dom.production.min.js
docker exec leantime curl -sL -o /var/www/html/public/userfiles/whiteboard/excalidraw.min.js https://unpkg.com/@excalidraw/excalidraw@0.17.6/dist/excalidraw.production.min.js
```

### 3. Enable plugin

1. Go to Leantime → Plugins → Installed Plugins
2. Find **Whiteboard Excalidraw** → click **Enable**

Enabling the plugin will automatically create the `zp_whiteboards` database table.

### 4. Restart

```bash
docker restart leantime
```

## Usage

1. Enter any project
2. Click **Whiteboards** in the sidebar "Make" menu
3. Create a new whiteboard with a title
4. Draw freely on the Excalidraw canvas
5. Changes auto-save after 3 seconds, or press `Ctrl+S` / click **Save Now**

## File Structure

```
Whiteboards/
├── composer.json
├── register.php              # Hooks: menu, project tabs, language
├── Controllers/
│   ├── ShowAll.php           # GET  /whiteboards/showAll
│   ├── ShowWhiteboard.php    # GET  /whiteboards/showWhiteboard/{id}
│   ├── Create.php            # POST /whiteboards/create
│   ├── Save.php              # POST /whiteboards/save/{id}
│   ├── Rename.php            # POST /whiteboards/rename/{id}
│   └── Delete.php            # GET  /whiteboards/delete/{id}
├── Models/Whiteboard.php
├── Repositories/WhiteboardRepository.php
├── Services/Whiteboards.php
├── Templates/
│   ├── showAll.blade.php
│   ├── showWhiteboard.blade.php
│   └── partials/whiteboardCard.blade.php
├── Js/whiteboardController.js
├── Language/en-US.ini
└── Middleware/GetLanguageAssets.php
```

## Database

Table `zp_whiteboards`:

| Column     | Type         |
|------------|-------------|
| id         | BIGINT PK AI |
| title      | VARCHAR(255) |
| projectId  | INT          |
| author     | INT          |
| sceneData  | LONGTEXT     |
| created    | DATETIME     |
| modified   | DATETIME     |

## License

Apache-2.0
