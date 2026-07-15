@extends($layout)

@section('content')

<div id="excalidraw-overlay" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:#fff;display:flex;flex-direction:column;">

    {{-- Top toolbar --}}
    <div style="flex:0 0 48px;display:flex;align-items:center;justify-content:space-between;padding:0 16px;background:#f5f5f5;border-bottom:1px solid #ddd;z-index:10001;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ BASE_URL }}/whiteboards/showAll" class="btn btn-default btn-sm">
                <span class="fa fa-arrow-left"></span> Back to List
            </a>
            <span style="font-weight:600;color:#333;font-size:15px;">{{ $tpl->escape($whiteboard['title']) }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <span id="save-status" style="font-size:12px;color:#666;"></span>
            <a href="javascript:void(0)" id="save-now-btn" onclick="window._whiteboardSaveNow &amp;&amp; window._whiteboardSaveNow()"
               class="btn btn-primary btn-sm">
                <span class="fa fa-save"></span> Save Now
            </a>
        </div>
    </div>

    {{-- Canvas area --}}
    <div id="excalidraw-app" style="flex:1 1 auto;position:relative;"></div>
    <div id="excalidraw-loading" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:10000;font-size:18px;color:#666;">Loading...</div>
    <div id="excalidraw-error" style="display:none;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:10000;color:red;text-align:center;max-width:500px;"></div>

</div>

<script src="{{ BASE_URL }}/userfiles/whiteboard/react.production.min.js"></script>
<script src="{{ BASE_URL }}/userfiles/whiteboard/react-dom.production.min.js"></script>
<script src="{{ BASE_URL }}/userfiles/whiteboard/excalidraw.min.js"></script>

<script>
(function() {
    try {
        const whiteboardId = {{ (int) ($whiteboard['id'] ?? 0) }};
        const saveUrl = '{{ BASE_URL }}/whiteboards/save/' + whiteboardId;
        const csrfToken = '{{ csrf_token() }}';
        console.log('[Whiteboard] id=' + whiteboardId + ' saveUrl=' + saveUrl);

        @php $rawData = $whiteboard['sceneData'] ?? ''; @endphp
        const rawData = @json($rawData ? json_decode($rawData, true) : null);

        var initialElements = [];
        var initialAppState = {};
        if (rawData && rawData.elements) initialElements = rawData.elements;
        if (rawData && rawData.appState) initialAppState = rawData.appState;

        document.getElementById('excalidraw-loading').style.display = 'none';

        var statusEl = document.getElementById('save-status');
        var saveTimer = null;
        var currentElements = initialElements;
        var currentAppState = initialAppState;

        function setStatus(text, color) {
            if (statusEl) {
                statusEl.textContent = text;
                statusEl.style.color = color || '#666';
            }
        }

        function saveScene(elements, appState) {
            setStatus('Saving...', '#666');
            var json = JSON.stringify({
                sceneData: JSON.stringify({
                    elements: elements || currentElements,
                    appState: appState || currentAppState
                })
            });
            console.log('[Whiteboard] Saving, body length=' + json.length);

            fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: json
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                console.log('[Whiteboard] Save response:', data);
                if (data.status === 'ok') {
                    setStatus('Saved ' + new Date().toLocaleTimeString(), 'green');
                } else {
                    setStatus('Save failed: ' + (data.message || ''), 'red');
                }
            })
            .catch(function(err) {
                console.error('[Whiteboard] Save error:', err);
                setStatus('Network error!', 'red');
            });
        }

        function onChange(elements, appState) {
            currentElements = elements;
            currentAppState = appState;
            if (saveTimer) clearTimeout(saveTimer);
            setStatus('Unsaved...', '#f0ad4e');
            saveTimer = setTimeout(function() {
                saveScene(elements, appState);
            }, 3000);
        }

        // Expose for manual save button
        window._whiteboardSaveNow = function() {
            if (saveTimer) clearTimeout(saveTimer);
            saveScene(currentElements, currentAppState);
        };

        // Ctrl+S: use capture phase to intercept BEFORE Excalidraw
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                window._whiteboardSaveNow();
            }
        }, true); // capture phase

        // Render
        var App = function() {
            return React.createElement('div',
                { style: { width: '100%', height: '100%' } },
                React.createElement(ExcalidrawLib.Excalidraw, {
                    initialData: {
                        elements: initialElements,
                        appState: initialAppState,
                        scrollToContent: true
                    },
                    onChange: onChange,
                    UIOptions: {
                        canvasActions: {
                            export: false,
                            saveAsImage: false,
                            loadScene: false,
                            saveToActiveFile: false
                        }
                    }
                })
            );
        };

        var container = document.getElementById('excalidraw-app');
        var root = ReactDOM.createRoot(container);
        root.render(React.createElement(App));
        setStatus('Ready', 'green');
        console.log('[Whiteboard] Loaded successfully');

    } catch(err) {
        console.error('[Whiteboard] Init error:', err);
        document.getElementById('excalidraw-loading').style.display = 'none';
        var errEl = document.getElementById('excalidraw-error');
        errEl.style.display = 'block';
        errEl.innerHTML = '<b>Error:</b> ' + err.message +
            '<br><small>Open browser console (F12) for details</small>';
    }
})();
</script>

@endsection
