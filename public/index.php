<?php
// Activar errores para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = '127.0.0.1';
$db   = 'qanil_rpg';
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

$db_status = "Pendiente";

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
    $db_status = "Conectado";
} catch (\PDOException $e) {
    $db_status = "Error DB: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crónicas de Q'anil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
        body { font-family: 'Press Start 2P', cursive; background-color: #1a202c; color: #e2e8f0; }
        .pixel-rendering { image-rendering: pixelated; }
        #fade-overlay { pointer-events: none; transition: opacity 1s ease-in-out; }
    </style>
</head>
<body class="h-screen w-screen flex flex-col items-center justify-center p-4">

    <div id="fade-overlay" class="fixed inset-0 bg-black opacity-0 z-50"></div>

    <div class="text-center mb-6">
        <h1 class="text-3xl text-yellow-500 mb-2">Crónicas de Q'anil</h1>
        <h2 class="text-lg text-green-400">El Ascenso de Oscar León</h2>
    </div>

    <div class="bg-gray-800 border-4 border-gray-600 p-6 rounded-lg max-w-6xl w-full flex flex-col md:flex-row gap-4 shadow-2xl">
        <div class="flex-1">
            <div class="mb-4 text-xs text-gray-400 flex justify-between items-center">
                <span>Motor: <span class="text-green-500">Activo</span> | DB: <span class="text-blue-400"><?php echo $db_status; ?></span></span>
                <span id="player-gold" class="text-yellow-500 text-sm">Oro: 0</span>
            </div>
            
            <div class="w-full h-[400px] bg-black border-2 border-gray-700 relative flex items-center justify-center overflow-hidden mb-4 rounded shadow-inner">
                <canvas id="gameCanvas" width="640" height="320" class="pixel-rendering absolute inset-0 w-full h-full"></canvas>
                <div id="ui-layer" class="absolute inset-0 p-3 z-10 flex flex-col justify-between pointer-events-none">
                    <div class="flex justify-between items-start">
                        <div class="bg-black/40 p-2 rounded">
                            <p id="hp-bar" class="text-red-500 text-[10px] font-bold">Lvl 1 | HP: 100/100</p>
                            <div class="w-24 h-2 bg-gray-900 border border-gray-700 mt-1">
                                <div id="hp-fill" class="h-full bg-red-600 transition-all duration-300" style="width: 100%;"></div>
                            </div>
                        </div>
                        <span id="time-display" class="text-[10px] px-2 py-1 bg-black/50 rounded border border-white/20">Día</span>
                        <div class="bg-black/40 p-2 rounded text-right">
                             <p id="energy-bar" class="text-blue-500 text-[10px] font-bold">ENG: 80/80</p>
                             <div class="w-24 h-2 bg-gray-900 border border-gray-700 mt-1 ml-auto">
                                <div id="energy-fill" class="h-full bg-blue-600 transition-all duration-300" style="width: 100%;"></div>
                            </div>
                        </div>
                    </div>

                    <div id="dialogue-box" class="hidden self-center mb-8 bg-black/95 border-2 border-white p-4 text-[10px] text-white max-w-[85%] pointer-events-auto shadow-2xl rounded-sm">
                        <p id="dialogue-text" class="leading-relaxed"></p>
                        <div class="mt-3 flex justify-end">
                            <button onclick="closeDialogue()" class="text-yellow-500 hover:text-yellow-400 underline uppercase tracking-widest">Cerrar</button>
                        </div>
                    </div>

                    <div id="region-display" class="text-center text-[8px] text-white/70 bg-black/60 p-1 rounded-full w-fit self-center border border-white/10 uppercase tracking-widest">
                         Santiago, Zacapa
                    </div>
                </div>
            </div>

            <div class="flex justify-center gap-3">
                <button onclick="startNewGame()" class="bg-green-700 hover:bg-green-600 text-white px-4 py-2 border-b-4 border-green-900 active:border-b-0 active:mt-1 text-[10px] rounded transition-all">Nueva Partida</button>
                <button onclick="loadGame()" class="bg-blue-700 hover:bg-blue-600 text-white px-4 py-2 border-b-4 border-blue-900 active:border-b-0 active:mt-1 text-[10px] rounded transition-all">Cargar</button>
                <button onclick="saveGame()" class="bg-purple-700 hover:bg-purple-600 text-white px-4 py-2 border-b-4 border-purple-900 active:border-b-0 active:mt-1 text-[10px] rounded transition-all">Guardar</button>
            </div>
        </div>

        <div class="w-full md:w-64 flex flex-col gap-4">
            <div class="bg-gray-900 border-2 border-gray-700 p-4 rounded shadow-lg">
                <h3 class="text-[10px] text-yellow-500 mb-3 border-b border-gray-800 pb-2 flex items-center gap-2">MISIONES</h3>
                <ul id="quest-list" class="text-[8px] space-y-3">
                    <li id="q1" class="text-white">• Cosecha 3 mazorcas (0/3)</li>
                    <li id="q2" class="text-gray-500">• Viaja a Chiquimula</li>
                </ul>
            </div>
            <div class="bg-gray-900 border-2 border-gray-700 p-4 flex-1 rounded shadow-lg">
                <h3 class="text-[10px] text-green-500 mb-3 border-b border-gray-800 pb-2 flex items-center gap-2">INVENTARIO</h3>
                <ul id="inventory-list" class="text-[8px] space-y-2">
                    <li class="text-gray-500 italic">Vacio</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        const inventoryList = document.getElementById('inventory-list');
        const dialogueBox = document.getElementById('dialogue-box');
        const dialogueText = document.getElementById('dialogue-text');
        const timeDisplay = document.getElementById('time-display');
        const hpFill = document.getElementById('hp-fill');
        const hpBarText = document.getElementById('hp-bar');
        const energyFill = document.getElementById('energy-fill');
        const energyBarText = document.getElementById('energy-bar');
        const goldDisplay = document.getElementById('player-gold');
        const regionDisplay = document.getElementById('region-display');
        const fadeOverlay = document.getElementById('fade-overlay');

        const config = { fps: 60, tileSize: 48, playerSpeed: 4 };

        const audio = {
            ambient: null, hit: null,
            init() {
                this.ambient = new Audio('assets/audio/ambient_zacapa.mp3');
                this.ambient.loop = true;
                this.hit = new Audio('assets/audio/hit.mp3');
            },
            playHit() { if(this.hit) this.hit.play().catch(e=>{}); }
        };

        const assets = {
            oscar: { down: [], up: [], left: [], right: [] },
            santiago: {},
            loaded: 0,
            total: 12 + 10 
        };

        const spritePaths = { down: ['01', '02', '03'], up: ['01', '02', '03'], left: ['01', '02', '03'], right: ['01', '02', '03'] };
        Object.keys(spritePaths).forEach(dir => {
            spritePaths[dir].forEach(frame => {
                const img = new Image();
                img.src = `assets/img/oscar/uniformes_3x4/oscar_leon_${dir}_${frame}.png`;
                img.onload = () => assets.loaded++;
                assets.oscar[dir].push(img);
            });
        });

        const santiagoTiles = {
            ground: 'terrain/dry_soil_01.png',
            path: 'terrain/dust_path_01.png',
            corn: 'agriculture/corn_grown.png',
            cactus: 'vegetation/organ_cactus_01.png',
            rock: 'rocks_mountains/gray_boulder_01.png',
            water: 'water_irrigation/motagua_water_01.png',
            house_wall: 'buildings/adobe_wall_01.png',
            house_door: 'buildings/adobe_wall_door_01.png',
            house_roof: 'buildings/teja_roof_01.png',
            fence: 'buildings/wooden_fence_01.png'
        };

        Object.keys(santiagoTiles).forEach(key => {
            const img = new Image();
            img.src = `assets/img/tiles_santiago/${santiagoTiles[key]}`;
            img.onload = () => assets.loaded++;
            assets.santiago[key] = img;
        });

        const player = {
            x: 300, y: 150, width: 32, height: 48,
            hp: 100, maxHp: 100, energy: 80, maxEnergy: 80,
            level: 1, xp: 0, nextLevelXp: 100,
            inventory: { maiz: 0 },
            dir: 'down', isMoving: false, isAttacking: false,
            attackFrame: 0, frame: 0, gold: 0, username: 'OscarLeon'
        };

        const world = {
            region: 'zacapa', time: 0, enemies: [], isTransitioning: false,
            clouds: [
                { x: 10, y: 30, speed: 0.2, w: 60, h: 20 },
                { x: 250, y: 60, speed: 0.15, w: 80, h: 25 }
            ],
            decorations: [
                { x: 50, y: 50, type: 'cactus' },
                { x: 550, y: 150, type: 'cactus' },
                { x: 200, y: 220, type: 'rock' },
                { x: 100, y: 200, type: 'fence' },
                { x: 148, y: 200, type: 'fence' },
                { x: 480, y: 210, type: 'shop' }
            ],
            npcs: [
                { id: 'marisol', name: 'Marisol', x: 450, y: 100, width: 32, height: 48, color: '#f472b6', dialogue: '¡Hijo! La tierra está lista. Usa ESPACIO para sembrar.' },
                { id: 'chepe', name: 'Don Chepe', x: 520, y: 220, width: 32, height: 48, color: '#fbbf24', dialogue: 'Te compro el maíz por 10 de oro cada uno.' }
            ],
            objects: [
                { id: 'house', x: 300, y: 40, type: 'house', width: 96, height: 96 },
                { id: 'bridge', x: 300, y: 280, type: 'bridge', width: 96, height: 48 },
                { id: 'river_l', x: 0, y: 280, type: 'solid', width: 300, height: 48 },
                { id: 'river_r', x: 396, y: 280, type: 'solid', width: 244, height: 48 },
                { id: 'plot1', x: 80, y: 80, type: 'soil', status: 'empty', timer: 0, width: 48, height: 48 },
                { id: 'plot2', x: 128, y: 80, type: 'soil', status: 'empty', timer: 0, width: 48, height: 48 },
                { id: 'plot3', x: 176, y: 80, type: 'soil', status: 'empty', timer: 0, width: 48, height: 48 }
            ]
        };

        const keys = {};
        window.addEventListener('keydown', e => { 
            keys[e.code] = true; 
            if(e.code === 'KeyM') audio.ambient.play().catch(()=>{});
        });
        window.addEventListener('keyup', e => keys[e.code] = false);

        function checkCollision(rect1, rect2) {
            return rect1.x < rect2.x + rect2.width && rect1.x + rect1.width > rect2.x &&
                   rect1.y < rect2.y + rect2.height && rect1.y + rect1.height > rect2.y;
        }

        function showDialogue(text) { dialogueText.innerText = text; dialogueBox.classList.remove('hidden'); }
        function closeDialogue() { dialogueBox.classList.add('hidden'); }

        async function saveGame() {
            try {
                await fetch('../api/save_game.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        username: player.username, level: player.level, xp: player.xp,
                        hp: player.hp, energy: player.energy, gold: player.gold, inventory: player.inventory, region: world.region
                    })
                });
                showDialogue("¡Progreso Guardado!");
            } catch (e) { console.error(e); }
        }

        async function loadGame() {
            try {
                const res = await fetch('../api/load_game.php');
                const data = await res.json();
                if (data.status === 'success') {
                    const p = data.player;
                    player.level = parseInt(p.level); player.xp = parseInt(p.xp);
                    player.hp = parseInt(p.hp); player.energy = parseInt(p.energy || 80);
                    player.gold = parseInt(p.gold); updateUI(); showDialogue("Cargado: Lvl " + p.level);
                }
            } catch (e) { console.error(e); }
        }

        async function startNewGame() {
            try {
                await fetch('../api/save_game.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: player.username, level: 1, xp: 0, hp: 100, energy: 80, gold: 0 })
                });
                showDialogue("¡Partida Iniciada!"); updateUI();
            } catch (e) { console.error(e); }
        }

        function transitionToRegion(newRegion) {
            if (world.isTransitioning) return;
            world.isTransitioning = true;
            fadeOverlay.style.opacity = "1";
            setTimeout(() => {
                world.region = newRegion;
                regionDisplay.innerText = newRegion === 'zacapa' ? "Santiago, Zacapa" : "San Juan, Chiquimula";
                player.x = 40;
                setTimeout(() => { fadeOverlay.style.opacity = "0"; world.isTransitioning = false; }, 1000);
            }, 1000);
        }

        function spawnShadow() {
            if (world.enemies.length < 3) {
                world.enemies.push({ x: Math.random() * canvas.width, y: Math.random() * 200, width: 24, height: 32, speed: 1.2 });
            }
        }

        function update() {
            if (world.isTransitioning) return;
            world.time = (world.time + 1) % 4000;
            player.frame++;

            world.clouds.forEach(c => { c.x += c.speed; if (c.x > canvas.width) c.x = -c.w; });

            const nightIntensity = Math.max(0, Math.sin(world.time * 0.00157) * 0.7);
            if (nightIntensity > 0.4 && Math.random() < 0.01) spawnShadow();
            if (nightIntensity < 0.2) world.enemies = [];

            if (keys['Space'] && !player.isAttacking) { 
                if (player.energy >= 5) {
                    player.isAttacking = true; player.attackFrame = 0; audio.playHit();
                    let acted = false;
                    world.objects.forEach(obj => {
                        if (obj.type === 'soil' && checkCollision(player, obj)) {
                            if (obj.status === 'empty') {
                                obj.status = 'seedling'; obj.timer = 300; player.energy -= 10; acted = true; showDialogue("¡Sembrado! (-10 ENG)");
                            } else if (obj.status === 'ready') {
                                obj.status = 'empty'; player.inventory.maiz++; player.energy -= 5; acted = true; showDialogue("¡Cosechado! (-5 ENG)");
                            }
                        }
                    });
                    if (!acted) player.energy -= 2;
                    updateUI();
                } else {
                    showDialogue("Necesitas descansar...");
                }
            }
            if (player.isAttacking) { player.attackFrame++; if (player.attackFrame > 15) player.isAttacking = false; }

            let nextX = player.x; let nextY = player.y; player.isMoving = false;
            if (!player.isAttacking) {
                if (keys['ArrowUp'] || keys['KeyW'] || keys['Keyw']) { nextY -= config.playerSpeed; player.dir = 'up'; player.isMoving = true; }
                else if (keys['ArrowDown'] || keys['KeyS'] || keys['Keys']) { nextY += config.playerSpeed; player.dir = 'down'; player.isMoving = true; }
                else if (keys['ArrowLeft'] || keys['KeyA'] || keys['Keya']) { nextX -= config.playerSpeed; player.dir = 'left'; player.isMoving = true; }
                else if (keys['ArrowRight'] || keys['KeyD'] || keys['Keyd']) { nextX += config.playerSpeed; player.dir = 'right'; player.isMoving = true; }
            }

            if ((keys['KeyE'] || keys['Keye'] || keys['Enter']) && checkCollision(player, { ...world.objects[0], height: 120 })) {
                player.hp = player.maxHp; player.energy = player.maxEnergy; world.time = 0;
                showDialogue("Has descansado en casa."); updateUI();
            }

            world.objects.forEach(obj => { if (obj.status === 'seedling') { obj.timer--; if (obj.timer <= 0) obj.status = 'ready'; } });
            if (player.x > canvas.width - 20) transitionToRegion('chiquimula');

            let canMove = true;
            world.objects.forEach(obj => { if (obj.type === 'solid' && checkCollision({ x: nextX, y: nextY, width: player.width, height: player.height }, obj)) canMove = false; });
            if (canMove) { player.x = Math.max(0, Math.min(canvas.width - player.width, nextX)); player.y = Math.max(0, Math.min(canvas.height - player.height, nextY)); }

            world.enemies.forEach((enemy, index) => {
                const dx = player.x - enemy.x; const dy = player.y - enemy.y; const dist = Math.sqrt(dx*dx + dy*dy);
                if (dist < 180) { enemy.x += (dx / dist) * enemy.speed; enemy.y += (dy / dist) * enemy.speed; }
                if (player.isAttacking && player.attackFrame === 5 && checkCollision({ x: player.x - 20, y: player.y - 20, width: 72, height: 88 }, enemy)) {
                    world.enemies.splice(index, 1); player.xp += 30; updateUI();
                }
                if (checkCollision(player, enemy)) { player.hp -= 0.3; updateUI(); }
            });

            world.npcs.forEach(npc => { if (checkCollision(player, npc) && dialogueBox.classList.contains('hidden') && player.isMoving) {
                showDialogue(`${npc.name}: ${npc.dialogue}`);
                if(npc.id === 'chepe' && player.inventory.maiz > 0) {
                    player.gold += player.inventory.maiz * 10; player.inventory.maiz = 0; updateUI();
                }
            }});
        }

        function updateUI() {
            hpBarText.innerText = `Lvl ${player.level} | HP: ${Math.floor(player.hp)}/100`;
            hpFill.style.width = `${(player.hp / player.maxHp) * 100}%`;
            energyBarText.innerText = `ENG: ${Math.floor(player.energy)}/80`;
            energyFill.style.width = `${(player.energy / player.maxEnergy) * 100}%`;
            goldDisplay.innerText = `Oro: ${player.gold}`;
            inventoryList.innerHTML = player.inventory.maiz > 0 ? `<li class="text-green-400">Maiz x${player.inventory.maiz}</li>` : '<li class="text-gray-500 italic">Vacio</li>';
            document.getElementById('q1').innerText = `• Cosecha 3 mazorcas (${player.inventory.maiz}/3)`;
        }

        function draw() {
            const ground = assets.santiago.ground;
            if (assets.loaded >= assets.total && ground) {
                for (let x = 0; x < canvas.width; x += config.tileSize) {
                    for (let y = 0; y < canvas.height; y += config.tileSize) { ctx.drawImage(ground, x, y, config.tileSize, config.tileSize); }
                }
            }
            world.decorations.forEach(dec => {
                if (dec.type === 'shop') {
                    ctx.drawImage(assets.santiago.house_roof, dec.x, dec.y - 24, 64, 32);
                    ctx.fillStyle = '#fbbf24'; ctx.fillRect(dec.x + 10, dec.y + 10, 44, 20);
                } else if (assets.santiago[dec.type]) {
                    ctx.drawImage(assets.santiago[dec.type], dec.x, dec.y, 48, 48);
                }
            });
            world.objects.forEach(obj => {
                if (obj.type === 'soil') {
                    ctx.drawImage(assets.santiago.ground, obj.x, obj.y, 48, 48);
                    ctx.strokeStyle = '#451a03'; ctx.strokeRect(obj.x+4, obj.y+4, 40, 40);
                    if (obj.status === 'seedling') { ctx.fillStyle = '#166534'; ctx.fillRect(obj.x + 20, obj.y + 30, 8, 14); }
                    else if (obj.status === 'ready') { ctx.drawImage(assets.santiago.corn, obj.x, obj.y, 48, 48); }
                } else if (obj.type === 'house') {
                    ctx.drawImage(assets.santiago.house_wall, obj.x, obj.y + 48, 48, 48);
                    ctx.drawImage(assets.santiago.house_door, obj.x + 48, obj.y + 48, 48, 48);
                    ctx.drawImage(assets.santiago.house_roof, obj.x, obj.y, 96, 48);
                } else if (obj.type === 'bridge') {
                    ctx.fillStyle = '#451a03'; ctx.fillRect(obj.x, obj.y, obj.width, obj.height);
                } else if (obj.type === 'solid') {
                    for(let rx = obj.x; rx < obj.x + obj.width; rx += 48) ctx.drawImage(assets.santiago.water, rx, obj.y, 48, 48);
                }
            });
            world.npcs.forEach(npc => { ctx.fillStyle = npc.color; ctx.fillRect(npc.x + 8, npc.y + 12, 16, 32); ctx.fillStyle = '#fcd34d'; ctx.fillRect(npc.x + 10, npc.y + 4, 12, 12); });
            world.enemies.forEach(enemy => { ctx.fillStyle = 'rgba(0, 0, 0, 0.7)'; ctx.beginPath(); ctx.ellipse(enemy.x + 12, enemy.y + 16, 12, 16, 0, 0, Math.PI * 2); ctx.fill(); });
            if (assets.loaded >= assets.total) {
                const currentFrame = player.isMoving ? Math.floor(player.frame / 10) % 3 : 0;
                ctx.drawImage(assets.oscar[player.dir][currentFrame], player.x - 8, player.y, 48, 48);
            }
            ctx.fillStyle = 'rgba(255, 255, 255, 0.3)';
            world.clouds.forEach(c => { ctx.beginPath(); ctx.ellipse(c.x, c.y, c.w/2, c.h/2, 0, 0, Math.PI * 2); ctx.fill(); });
            const nightIntensity = Math.max(0, Math.sin(world.time * 0.00157) * 0.7);
            ctx.fillStyle = `rgba(15, 23, 42, ${nightIntensity})`; ctx.fillRect(0, 0, canvas.width, canvas.height);
            timeDisplay.innerText = nightIntensity > 0.3 ? "Noche" : "Día";
        }
        audio.init();
        function loop() { update(); draw(); requestAnimationFrame(loop); }
        loop();
        window.startNewGame = startNewGame; window.loadGame = loadGame; window.saveGame = saveGame; window.closeDialogue = closeDialogue;
    </script>
</body>
</html>