<?php

use Leantime\Core\Events\EventDispatcher;
use Leantime\Plugins\Whiteboards\Services\Whiteboards;

// ============================================================
// 1. Load Language Strings (direct merge, no middleware needed)
// ============================================================
$languageFile = __DIR__ . '/Language/en-US.ini';
if (file_exists($languageFile)) {
    $whiteboardLang = parse_ini_file($languageFile, true);
    if (is_array($whiteboardLang)) {
        app()->make(\Leantime\Core\Language::class)->ini_array = array_merge(
            app()->make(\Leantime\Core\Language::class)->ini_array,
            $whiteboardLang
        );
    }
}

// ============================================================
// 2. Add "Whiteboards" menu item to the project sidebar
//    Placed in the "Make" submenu (position 10) at position 45
// ============================================================
function addWhiteboardMenuItem($menuStructure)
{
    if (isset($menuStructure['default'][10]['submenu'])) {
        $menuStructure['default'][10]['submenu'][45] = [
            'type' => 'item',
            'module' => 'whiteboards',
            'title' => 'menu.whiteboards',
            'icon' => 'fa fa-fw fa-chalkboard',
            'tooltip' => 'menu.whiteboards_tooltip',
            'href' => '/whiteboards/showAll',
            'active' => ['showAll', 'showWhiteboard'],
        ];
    }
    return $menuStructure;
}

EventDispatcher::add_filter_listener(
    "leantime.domain.menu.repositories.menu.*.menuStructures",
    'addWhiteboardMenuItem'
);

// ============================================================
// 3. Add "Whiteboards" tab to the project detail page
// ============================================================
function addWhiteboardProjectTab($params)
{
    echo '<li><a href="#whiteboards"><span class="fa fa-chalkboard"></span> ' . __('tabs.whiteboards') . '</a></li>';
}
EventDispatcher::add_event_listener('projectTabsList', 'addWhiteboardProjectTab');

function addWhiteboardProjectTabContent($params)
{
    $projectId = session('currentProject') ? (int) session('currentProject') : 0;

    if ($projectId > 0) {
        try {
            $service = app()->make(Whiteboards::class);
            $whiteboards = $service->getAllByProject($projectId);
        } catch (\Exception $e) {
            $whiteboards = [];
        }
    } else {
        $whiteboards = [];
    }
    ?>
    <div id="whiteboards">
        <div class="row-fluid">
            <div class="span12">
                <h4 class="widgettitle title-light">
                    <span class="fa fa-chalkboard"></span> <?= __('headlines.whiteboards') ?>
                </h4>
            </div>
        </div>

        <?php if (empty($whiteboards)): ?>
            <div class="alert alert-info">
                <?= __('text.no_whiteboards') ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($whiteboards as $wb): ?>
                <div class="col-md-3">
                    <div class="well" style="text-align: center; padding: 15px; margin-bottom: 15px; border-radius: 12px;">
                        <span class="fa fa-chalkboard" style="font-size: 32px; color: #666; display: block; margin-bottom: 10px;"></span>
                        <h5 style="margin: 0 0 5px 0;"><?= htmlspecialchars($wb['title']) ?></h5>
                        <a href="<?= BASE_URL ?>/whiteboards/showWhiteboard/<?= $wb['id'] ?>"
                           class="btn btn-primary btn-sm">
                            <span class="fa fa-external-link"></span> <?= __('buttons.open_whiteboard') ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row-fluid" style="margin-top: 20px;">
            <div class="span12">
                <h4 class="widgettitle title-light">
                    <span class="fa fa-plus"></span> <?= __('buttons.create_whiteboard') ?>
                </h4>
                <form method="post" action="<?= BASE_URL ?>/whiteboards/create" class="form-inline">
                    <input type="text" name="title" placeholder="<?= __('label.whiteboard_title') ?>" required
                           style="width: 300px; margin-right: 10px;" class="form-control" />
                    <button type="submit" class="btn btn-primary"><?= __('buttons.create_whiteboard') ?></button>
                </form>
            </div>
        </div>
    </div>
    <?php
}
EventDispatcher::add_event_listener('projectTabsContent', 'addWhiteboardProjectTabContent');
