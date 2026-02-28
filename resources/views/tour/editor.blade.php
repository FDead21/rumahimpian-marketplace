<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotspot Editor — {{ $property->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #panorama { width: 100%; height: 100vh; }

        .hotspot-arrow {
            width: 60px;
            height: 60px;
            cursor: pointer;
            transform-style: preserve-3d;
            perspective: 200px;
        }

        .hotspot-inner {
            width: 60px;
            height: 60px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: float 2s ease-in-out infinite;
        }

        .hotspot-chevron {
            width: 0;
            height: 0;
            border-left: 18px solid transparent;
            border-right: 18px solid transparent;
            border-bottom: 28px solid rgba(255, 255, 255, 0.95);
            filter: drop-shadow(0 0 8px rgba(99, 102, 241, 1)) drop-shadow(0 2px 4px rgba(0,0,0,0.8));
        }

        .hotspot-chevron-2 {
            width: 0;
            height: 0;
            border-left: 12px solid transparent;
            border-right: 12px solid transparent;
            border-bottom: 20px solid rgba(99, 102, 241, 0.7);
            margin-top: -8px;
            filter: drop-shadow(0 0 6px rgba(99, 102, 241, 0.8));
        }

        .hotspot-ring {
            position: absolute;
            bottom: -8px;
            width: 40px;
            height: 10px;
            border-radius: 50%;
            background: radial-gradient(ellipse, rgba(99,102,241,0.6) 0%, transparent 70%);
            animation: ring-pulse 2s ease-in-out infinite;
        }

        .hotspot-label {
            position: absolute;
            bottom: -28px;
            white-space: nowrap;
            background: rgba(0,0,0,0.75);
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 10px;
            backdrop-blur: 4px;
            border: 1px solid rgba(255,255,255,0.2);
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }

        @keyframes ring-pulse {
            0%, 100% { transform: scaleX(1); opacity: 0.6; }
            50% { transform: scaleX(1.4); opacity: 0.2; }
        }

        .hotspot-arrow:hover .hotspot-chevron {
            border-bottom-color: rgba(129, 140, 248, 1);
            filter: drop-shadow(0 0 14px rgba(129, 140, 248, 1)) drop-shadow(0 2px 4px rgba(0,0,0,0.8));
        }
    </style>
</head>
<body class="bg-gray-900 text-white flex flex-col h-screen overflow-hidden">

    {{-- Top Bar --}}
    <div class="flex items-center justify-between px-6 py-3 bg-gray-800 border-b border-gray-700 z-20">
        <div>
            <h1 class="font-bold text-lg">🗺 Hotspot Editor</h1>
            <p class="text-gray-400 text-sm">{{ $property->title }}</p>
        </div>
        <a href="{{ url()->previous() }}" class="text-sm bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg">← Back</a>
    </div>

    {{-- Main Layout --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- Sidebar --}}
        <div class="w-72 bg-gray-800 border-r border-gray-700 flex flex-col overflow-y-auto p-4 gap-4 z-10">

            {{-- Room Selector --}}
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase mb-1 block">Current Room</label>
                <select id="roomSelector" onchange="switchRoom(this.value)" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm outline-none">
                    @foreach($scenes as $scene)
                        <option value="{{ $scene->id }}" data-url="{{ asset('storage/' . $scene->file_path) }}">
                            {{ $scene->room_name ?: 'Room #' . $loop->iteration }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Add Hotspot --}}
            <div class="bg-gray-700 rounded-xl p-4 space-y-3">
                <p class="text-sm font-bold">➕ Add Hotspot</p>
                <p class="text-xs text-gray-400">Click on the 360 view to place an arrow, then fill details below.</p>

                <div>
                    <label class="text-xs text-gray-400">Pitch</label>
                    <input id="pitchInput" type="number" step="0.1" class="w-full bg-gray-600 rounded px-3 py-1 text-sm mt-1 outline-none" placeholder="Click map to fill">
                </div>
                <div>
                    <label class="text-xs text-gray-400">Yaw</label>
                    <input id="yawInput" type="number" step="0.1" class="w-full bg-gray-600 rounded px-3 py-1 text-sm mt-1 outline-none" placeholder="Click map to fill">
                </div>
                <div>
                    <label class="text-xs text-gray-400">Points To Room</label>
                    <select id="toRoomSelect" class="w-full bg-gray-600 rounded px-3 py-1 text-sm mt-1 outline-none">
                        @foreach($scenes as $scene)
                            <option value="{{ $scene->id }}">{{ $scene->room_name ?: 'Room #' . $loop->iteration }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-400">Label (optional)</label>
                    <input id="labelInput" type="text" class="w-full bg-gray-600 rounded px-3 py-1 text-sm mt-1 outline-none" placeholder="e.g. Go to Bedroom">
                </div>
                <button onclick="saveHotspot()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-sm transition">
                    Save Hotspot
                </button>
            </div>

            {{-- Existing Hotspots List --}}
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase mb-2">Saved Hotspots</p>
                <div id="hotspotList" class="space-y-2 text-sm"></div>
            </div>
        </div>

        {{-- Pannellum Viewer --}}
        <div class="flex-1 relative">
            <div id="panorama"></div>
            <div id="clickHint" class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-black/60 text-white text-sm px-4 py-2 rounded-full pointer-events-none">
                🖱 Click anywhere to place a hotspot
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script>
        const scenes = {{ Js::from($scenes->map(function($s) use ($scenes) {
            return [
                'id' => $s->id,
                'room_name' => $s->room_name ?: 'Room #' . ($scenes->search($s) + 1),
                'url' => asset('storage/' . $s->file_path),
                'hotspots' => $s->hotspots->map(function($h) {
                    return [
                        'id' => $h->id,
                        'pitch' => $h->pitch,
                        'yaw' => $h->yaw,
                        'label' => $h->label,
                        'to_media_id' => $h->to_media_id,
                        'to_room' => $h->toMedia->room_name ?? 'Room',
                    ];
                })
            ];
        })) }};

        let currentSceneId = scenes[0]?.id;
        let viewer = null;

        function buildHotspots(sceneId) {
            const scene = scenes.find(s => s.id == sceneId);
            return (scene?.hotspots || []).map(h => ({
                pitch: h.pitch,
                yaw: h.yaw,
                type: 'custom',
                cssClass: 'hotspot-arrow',
                clickHandlerFunc: (e, args) => switchRoom(args.to_media_id),
                clickHandlerArgs: { to_media_id: h.to_media_id },
                createTooltipFunc: (hotSpotDiv, args) => {
                    hotSpotDiv.innerHTML = `
                        <div class="hotspot-inner">
                            <div class="hotspot-chevron"></div>
                            <div class="hotspot-chevron-2"></div>
                            <div class="hotspot-ring"></div>
                            <div class="hotspot-label">${args.label || args.to_room}</div>
                        </div>
                    `;
                    hotSpotDiv.style.cursor = 'pointer';
                    hotSpotDiv.onclick = () => switchRoom(args.to_media_id);
                },
                createTooltipArgs: { 
                    to_media_id: h.to_media_id,
                    label: h.label, 
                    to_room: h.to_room 
                },
            }));
        }

        function initViewer(sceneId) {
            const scene = scenes.find(s => s.id == sceneId);
            if (!scene) return;
            currentSceneId = sceneId;

            if (viewer) { try { viewer.destroy(); } catch(e) {} }

            viewer = pannellum.viewer('panorama', {
                type: 'equirectangular',
                panorama: scene.url,
                autoLoad: true,
                autoRotate: 0,
                showControls: true,
                hotSpots: buildHotspots(sceneId),
            });

            // Wait for viewer to load, then attach click listener
            viewer.on('load', function() {
                const container = document.getElementById('panorama');
                
                let mouseDownPos = { x: 0, y: 0 };

                container.addEventListener('mousedown', function(e) {
                    mouseDownPos = { x: e.clientX, y: e.clientY };
                });

                container.addEventListener('mouseup', function(e) {
                    // Only register as a click if mouse didn't move much (not a drag)
                    const dx = Math.abs(e.clientX - mouseDownPos.x);
                    const dy = Math.abs(e.clientY - mouseDownPos.y);
                    if (dx > 5 || dy > 5) return;

                    const coords = viewer.mouseEventToCoords(e);
                    document.getElementById('pitchInput').value = coords[0].toFixed(2);
                    document.getElementById('yawInput').value = coords[1].toFixed(2);

                    // Visual feedback
                    document.getElementById('clickHint').textContent = `✅ Position set! Pitch: ${coords[0].toFixed(1)}, Yaw: ${coords[1].toFixed(1)}`;
                });
            });

            renderHotspotList(sceneId);
            document.getElementById('roomSelector').value = sceneId;

            const toSelect = document.getElementById('toRoomSelect');
            Array.from(toSelect.options).forEach(opt => {
                opt.disabled = opt.value == sceneId;
            });
        }

        function switchRoom(sceneId) {
            initViewer(sceneId);
        }

        function renderHotspotList(sceneId) {
            const scene = scenes.find(s => s.id == sceneId);
            const list = document.getElementById('hotspotList');
            if (!scene || scene.hotspots.length === 0) {
                list.innerHTML = '<p class="text-gray-500 text-xs">No hotspots yet.</p>';
                return;
            }
            list.innerHTML = scene.hotspots.map(h => `
                <div class="bg-gray-700 rounded-lg p-2 flex justify-between items-center">
                    <div>
                        <div class="font-medium">→ ${h.to_room}</div>
                        <div class="text-gray-400 text-xs">p: ${h.pitch}, y: ${h.yaw}</div>
                    </div>
                    <button onclick="deleteHotspot(${h.id})" class="text-red-400 hover:text-red-300 text-xs px-2">✕</button>
                </div>
            `).join('');
        }

        async function saveHotspot() {
            const pitch = document.getElementById('pitchInput').value;
            const yaw = document.getElementById('yawInput').value;
            const toId = document.getElementById('toRoomSelect').value;
            const label = document.getElementById('labelInput').value;

            if (!pitch || !yaw) { alert('Please click on the 360 view first to set position.'); return; }

            const res = await fetch('{{ route("hotspots.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ from_media_id: currentSceneId, to_media_id: toId, pitch, yaw, label })
            });

            const data = await res.json();
            if (data.success) {
                // Add to local state
                const scene = scenes.find(s => s.id == currentSceneId);
                const toScene = scenes.find(s => s.id == toId);
                scene.hotspots.push({
                    id: data.hotspot.id,
                    pitch: parseFloat(pitch),
                    yaw: parseFloat(yaw),
                    label,
                    to_media_id: parseInt(toId),
                    to_room: toScene?.room_name || 'Room',
                });
                initViewer(currentSceneId); // Refresh viewer with new hotspot
                document.getElementById('pitchInput').value = '';
                document.getElementById('yawInput').value = '';
                document.getElementById('labelInput').value = '';
            }
        }

        async function deleteHotspot(id) {
            if (!confirm('Delete this hotspot?')) return;
            const res = await fetch(`/portal-api/hotspots/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await res.json();
            if (data.success) {
                scenes.forEach(s => { s.hotspots = s.hotspots.filter(h => h.id !== id); });
                initViewer(currentSceneId);
            }
        }

        // Boot
        initViewer(currentSceneId);
    </script>
</body>
</html>