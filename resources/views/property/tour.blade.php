<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>360 Tour - {{ $property->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #panorama { width: 100vw; height: 100vh; }

        .hotspot-arrow {
            width: 60px;
            height: 60px;
            cursor: pointer;
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
    </style>
</head>
<body class="bg-black overflow-hidden relative">

    {{-- Top Bar --}}
    <div class="absolute top-0 left-0 w-full p-4 z-10 flex justify-between items-start bg-gradient-to-b from-black/70 to-transparent">
        <div>
            <h1 class="text-white font-bold text-xl">{{ $property->title }}</h1>
            <p class="text-gray-300 text-sm" id="currentRoomLabel">📍 {{ $scenes->first()->room_name ?? $property->city }}</p>
        </div>
        <a href="{{ route('property.show', ['id' => $property->id, 'slug' => $property->slug]) }}" 
           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg backdrop-blur-sm text-sm font-bold transition">
            ✕ {{ __('Close Tour') }}
        </a>
    </div>

    <div id="panorama"></div>
    <div id="transitionOverlay" 
        style="position:fixed;inset:0;background:black;opacity:0;z-index:50;pointer-events:none;transition:opacity 0.3s;">
    </div>
    {{-- Room Selector Bar --}}
    @if($scenes->count() > 1)
    <div class="absolute bottom-16 left-1/2 -translate-x-1/2 z-20 flex gap-2 bg-black/60 backdrop-blur-sm px-4 py-3 rounded-2xl">
        @foreach($scenes as $scene)
        <button onclick="switchRoom({{ $scene->id }})"
            id="btn-{{ $scene->id }}"
            class="room-btn px-4 py-2 rounded-xl text-sm font-bold transition text-white border border-white/20 hover:bg-white/20">
            {{ $scene->room_name ?: '🏠 Room ' . ($loop->iteration) }}
        </button>
        @endforeach
    </div>
    @endif

    {{-- Bottom Hint --}}
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 bg-black/60 text-white text-xs px-4 py-2 rounded-full flex gap-4">
        <span>🖱 {{ __('Drag to look around') }}</span>
        <span>🔍 {{ __('Scroll to zoom') }}</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script>
        @php
        $scenesData = $scenes->map(function($s) {
            return [
                'id' => $s->id,
                'room_name' => $s->room_name ?: 'Room',
                'url' => asset('storage/' . $s->file_path),
                'hotspots' => $s->hotspots->map(function($h) {
                    return [
                        'pitch' => $h->pitch,
                        'yaw' => $h->yaw,
                        'label' => $h->label,
                        'to_media_id' => $h->to_media_id,
                        'to_room' => $h->toMedia->room_name ?? 'Next Room',
                    ];
                })->values()->toArray()
            ];
        })->values()->toArray();
        @endphp

        const scenes = {!! json_encode($scenesData) !!};
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
                    hotSpotDiv.onclick = () => switchRoom(args.to_media_id);
                },
                createTooltipArgs: { 
                    to_media_id: h.to_media_id,
                    label: h.label, 
                    to_room: h.to_room 
                },
            }));
        }

        function switchRoom(sceneId) {
            const scene = scenes.find(s => s.id == sceneId);
            if (!scene) return;

            // Step 1: Zoom in fast (feels like walking forward)
            if (viewer) {
                let fov = viewer.getHfov();
                const zoomIn = setInterval(() => {
                    fov -= 4;
                    viewer.setHfov(fov, false);
                    if (fov <= 40) {
                        clearInterval(zoomIn);

                        // Step 2: Fade to black
                        // Fade to black
                        const overlay = document.getElementById('transitionOverlay');
                        overlay.style.opacity = '1';

                        setTimeout(() => {
                            try { viewer.destroy(); } catch(e) {}

                            viewer = pannellum.viewer('panorama', {
                                type: 'equirectangular',
                                panorama: scene.url,
                                autoLoad: true,
                                autoRotate: -2,
                                showControls: true,
                                hfov: 140,
                                hotSpots: buildHotspots(sceneId),
                            });

                            viewer.on('load', function() {
                                overlay.style.opacity = '0'; // Fade back in

                                let wfov = 140;
                                const zoomSettle = setInterval(() => {
                                    wfov -= 3;
                                    viewer.setHfov(wfov, false);
                                    if (wfov <= 100) clearInterval(zoomSettle);
                                }, 16);
                            });

                            document.getElementById('currentRoomLabel').textContent = '📍 ' + scene.room_name;
                            document.querySelectorAll('.room-btn').forEach(btn => btn.classList.remove('bg-white/30'));
                            const activeBtn = document.getElementById('btn-' + sceneId);
                            if (activeBtn) activeBtn.classList.add('bg-white/30');

                        }, 300);
                    }
                }, 16); // ~60fps
            } else {
                // First load — no animation
                viewer = pannellum.viewer('panorama', {
                    type: 'equirectangular',
                    panorama: scene.url,
                    autoLoad: true,
                    autoRotate: -2,
                    showControls: true,
                    hotSpots: buildHotspots(sceneId),
                });
            }
        }

        switchRoom(scenes[0]?.id);
    </script>
</body>
</html>