<?php
ini_set('display_errors', 1);
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
        body { font-family: 'Press Start 2P', cursive; background-color: #0f172a; color: #e2e8f0; }
        .pixel-rendering { image-rendering: pixelated; }
        #fade-overlay { pointer-events: none; transition: opacity 1s ease-in-out; }
        .bar-container { width: 96px; height: 6px; background: #111827; border: 1px solid #374151; margin-top: 3px; }
        .bar-fill { height: 100%; transition: width 0.3s; }
    </style>
</head>
<body class="min-h-screen w-screen flex flex-col items-center justify-center p-4">

    <div id="fade-overlay" class="fixed inset-0 bg-black opacity-0 z-50"></div>

    <div class="text-center mb-4">
        <h1 class="text-2xl text-yellow-400 mb-1">Crónicas de Q'anil</h1>
        <h2 class="text-sm text-green-400">El Ascenso de Oscar León</h2>
    </div>

    <div class="bg-gray-800 border-4 border-gray-600 p-4 rounded-lg max-w-6xl w-full flex flex-col md:flex-row gap-4 shadow-2xl">

        <!-- PANEL IZQUIERDO: juego -->
        <div class="flex-1">
            <div class="mb-3 text-xs text-gray-400 flex justify-between items-center">
                <span>Motor: <span class="text-green-500">Activo</span> | DB: <span class="text-blue-400"><?php echo htmlspecialchars($db_status); ?></span></span>
                <span id="player-gold" class="text-yellow-400 font-bold">Oro: 0</span>
            </div>

            <!-- Canvas -->
            <div class="w-full bg-black border-2 border-gray-700 relative overflow-hidden mb-3 rounded shadow-inner" style="height:380px;">
                <canvas id="gameCanvas" width="672" height="380" class="pixel-rendering absolute inset-0 w-full h-full"></canvas>

                <!-- HUD overlay -->
                <div id="ui-layer" class="absolute inset-0 p-3 z-10 flex flex-col justify-between pointer-events-none">
                    <!-- Top HUD -->
                    <div class="flex justify-between items-start">
                        <!-- HP / Energy / XP -->
                        <div class="bg-black/60 p-2 rounded border border-white/10">
                            <p id="hp-bar-text" class="text-red-400 text-[9px] font-bold">Lvl 1 | HP: 100/100</p>
                            <div class="bar-container"><div id="hp-fill" class="bar-fill bg-red-600" style="width:100%"></div></div>
                            <p id="energy-bar-text" class="text-blue-400 text-[9px] font-bold mt-1">ENG: 80/80</p>
                            <div class="bar-container"><div id="energy-fill" class="bar-fill bg-blue-600" style="width:100%"></div></div>
                            <p id="xp-bar-text" class="text-purple-400 text-[9px] font-bold mt-1">XP: 0/100</p>
                            <div class="bar-container"><div id="xp-fill" class="bar-fill bg-purple-600" style="width:0%"></div></div>
                        </div>

                        <!-- Day/Night + Region -->
                        <div class="flex flex-col items-center gap-1">
                            <span id="time-display" class="text-[9px] px-2 py-1 bg-black/60 rounded border border-white/20">Día</span>
                            <span id="region-display" class="text-[8px] text-white/70 bg-black/60 px-2 py-1 rounded border border-white/10 uppercase tracking-wider">Santiago, Zacapa</span>
                        </div>
                    </div>

                    <!-- Dialogue -->
                    <div id="dialogue-box" class="hidden self-center mb-2 bg-black/95 border-2 border-yellow-500 p-3 text-[9px] text-white max-w-[85%] pointer-events-auto shadow-2xl rounded">
                        <p id="dialogue-text" class="leading-relaxed"></p>
                        <div class="mt-2 flex justify-end">
                            <button onclick="closeDialogue()" class="text-yellow-400 hover:text-yellow-300 text-[8px] uppercase tracking-widest border border-yellow-600 px-2 py-1 rounded">Cerrar ▶</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-center gap-3 flex-wrap">
                <button onclick="startNewGame()" class="bg-green-700 hover:bg-green-600 text-white px-3 py-2 border-b-4 border-green-900 active:border-b-0 active:translate-y-1 text-[9px] rounded transition-all">▶ Nueva Partida</button>
                <button onclick="loadGame()" class="bg-blue-700 hover:bg-blue-600 text-white px-3 py-2 border-b-4 border-blue-900 active:border-b-0 active:translate-y-1 text-[9px] rounded transition-all">↑ Cargar</button>
                <button onclick="saveGame()" class="bg-purple-700 hover:bg-purple-600 text-white px-3 py-2 border-b-4 border-purple-900 active:border-b-0 active:translate-y-1 text-[9px] rounded transition-all">↓ Guardar</button>
                <span class="text-[8px] text-gray-500 self-center">WASD/Flechas · ESPACIO · E=Casa · M=Música</span>
            </div>
        </div>

        <!-- PANEL DERECHO: misiones + inventario -->
        <div class="w-full md:w-56 flex flex-col gap-3">
            <div class="bg-gray-900 border-2 border-gray-700 p-3 rounded shadow-lg">
                <h3 class="text-[9px] text-yellow-400 mb-2 border-b border-gray-700 pb-2">⚔ MISIONES</h3>
                <ul id="quest-list" class="text-[8px] space-y-2">
                    <li id="q1" class="text-white">◦ Cosecha 3 mazorcas (0/3)</li>
                    <li id="q2" class="text-gray-600">◦ Viaja a Chiquimula</li>
                </ul>
            </div>
            <div class="bg-gray-900 border-2 border-gray-700 p-3 flex-1 rounded shadow-lg">
                <h3 class="text-[9px] text-green-400 mb-2 border-b border-gray-700 pb-2">🎒 INVENTARIO</h3>
                <ul id="inventory-list" class="text-[8px] space-y-1">
                    <li class="text-gray-600 italic">Vacío</li>
                </ul>
            </div>
            <div class="bg-gray-900 border-2 border-gray-700 p-3 rounded shadow-lg">
                <h3 class="text-[9px] text-gray-400 mb-2 border-b border-gray-700 pb-2">ℹ CONTROLES</h3>
                <ul class="text-[7px] text-gray-500 space-y-1">
                    <li>WASD / ↑↓←→ Mover</li>
                    <li>ESPACIO Sembrar/Cosechar</li>
                    <li>E cerca de casa: Descansar</li>
                    <li>M: Música ambiental</li>
                </ul>
            </div>
        </div>
    </div>

<script>
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const W = canvas.width;
const H = canvas.height;

// ── UI REFS ──────────────────────────────────────────────────
const ui = {
    inventory:  document.getElementById('inventory-list'),
    dialogue:   document.getElementById('dialogue-box'),
    dialogText: document.getElementById('dialogue-text'),
    time:       document.getElementById('time-display'),
    hpText:     document.getElementById('hp-bar-text'),
    hpFill:     document.getElementById('hp-fill'),
    engText:    document.getElementById('energy-bar-text'),
    engFill:    document.getElementById('energy-fill'),
    xpText:     document.getElementById('xp-bar-text'),
    xpFill:     document.getElementById('xp-fill'),
    gold:       document.getElementById('player-gold'),
    region:     document.getElementById('region-display'),
    q1:         document.getElementById('q1'),
    q2:         document.getElementById('q2'),
    fade:       document.getElementById('fade-overlay'),
};

// ── AUDIO ────────────────────────────────────────────────────
const audio = {
    ambient: null, hit: null,
    init() {
        this.ambient = new Audio('public/assets/audio/ambient_zacapa.mp3');
        this.ambient.loop = true;
        this.hit = new Audio('public/assets/audio/hit.mp3');
    },
    playHit() { try { if (this.hit) { this.hit.currentTime = 0; this.hit.play().catch(() => {}); } } catch(e) {} }
};

// ── ASSET LOADING ────────────────────────────────────────────
const assets = {
    oscar:    { down: [], up: [], left: [], right: [] },
    santiago: {},
    loaded:   0,
    total:    0,
    ready:    false,
};

function loadImg(src, onReady) {
    const img = new Image();
    img.onload  = () => { assets.loaded++; checkAllLoaded(); };
    img.onerror = () => { assets.loaded++; checkAllLoaded(); console.warn('Asset missing:', src); };
    img.src = src;
    return img;
}

function checkAllLoaded() {
    if (!assets.ready && assets.loaded >= assets.total) assets.ready = true;
}

// Sprites del jugador (12 imágenes: 4 dir × 3 frames)
['down','up','left','right'].forEach(dir => {
    ['01','02','03'].forEach(frame => {
        assets.oscar[dir].push(loadImg(`public/assets/img/oscar/uniformes_3x4/oscar_leon_${dir}_${frame}.png`));
    });
});

// Tiles de Santiago
const TILES = {
    ground:        'terrain/dry_soil_01.png',
    ground2:       'terrain/dry_soil_cracked_01.png',
    path:          'terrain/dust_path_01.png',
    packed:        'terrain/packed_yard_01.png',
    tilled_soil:   'agriculture/tilled_soil_01.png',
    corn_seedling: 'agriculture/corn_seedling.png',
    corn_mid:      'agriculture/corn_mid.png',
    corn:          'agriculture/corn_grown.png',
    cactus:        'vegetation/organ_cactus_01.png',
    bush:          'vegetation/dry_bush_01.png',
    rock:          'rocks_mountains/gray_boulder_01.png',
    water:         'water_irrigation/motagua_water_01.png',
    river_bank:    'water_irrigation/motagua_bank_bottom.png',
    house_wall:    'buildings/adobe_wall_01.png',
    house_door:    'buildings/adobe_wall_door_01.png',
    house_roof:    'buildings/teja_roof_01.png',
    fence:         'buildings/wooden_fence_01.png',
};

Object.keys(TILES).forEach(key => {
    assets.santiago[key] = loadImg(`public/assets/img/tiles_santiago/${TILES[key]}`);
});

assets.total = 12 + Object.keys(TILES).length;

// ── PLAYER ───────────────────────────────────────────────────
const player = {
    x: 300, y: 180,
    w: 32, h: 48,
    hp: 100, maxHp: 100,
    energy: 80, maxEnergy: 80,
    level: 1, xp: 0, nextLevelXp: 100,
    gold: 0,
    inventory: { maiz: 0 },
    dir: 'down', moving: false, attacking: false,
    atkFrame: 0, frame: 0,
    username: 'OscarLeon',
    dead: false,
};

// ── WORLD ────────────────────────────────────────────────────
const TILE = 48;

// Deterministic tile variety based on grid position
function groundTile(gx, gy) {
    return ((gx * 7 + gy * 13) % 8 === 0) ? assets.santiago.ground2 : assets.santiago.ground;
}

const world = {
    region: 'zacapa',
    time: 0,
    enemies: [],
    transitioning: false,
    clouds: [
        { x: 10,  y: 30, speed: 0.25, w: 70, h: 22 },
        { x: 280, y: 55, speed: 0.18, w: 90, h: 28 },
        { x: 520, y: 20, speed: 0.12, w: 60, h: 18 },
    ],
    decorations: [
        { x: 50,  y: 50,  type: 'cactus' },
        { x: 560, y: 140, type: 'cactus' },
        { x: 200, y: 215, type: 'rock'   },
        { x: 500, y: 60,  type: 'rock'   },
        { x: 100, y: 195, type: 'fence'  },
        { x: 148, y: 195, type: 'fence'  },
        { x: 196, y: 195, type: 'fence'  },
        { x: 30,  y: 140, type: 'bush'   },
        { x: 600, y: 90,  type: 'bush'   },
        { x: 420, y: 40,  type: 'bush'   },
        { x: 480, y: 200, type: 'shop'   },
    ],
    npcs: [
        { id: 'marisol', name: 'Marisol', x: 450, y: 110, w: 32, h: 48, color: '#f472b6',
          dialogue: '¡Hijo! La tierra está lista. ESPACIO para sembrar, ESPACIO de nuevo cuando crezca.' },
        { id: 'chepe',   name: 'Don Chepe', x: 530, y: 215, w: 32, h: 48, color: '#fbbf24',
          dialogue: 'Te compro el maíz a 10 de oro cada uno. ¡Buena cosecha!' },
    ],
    objects: [
        { id: 'house',   x: 288, y: 30,  type: 'house',  w: 96, h: 96 },
        { id: 'bridge',  x: 288, y: 300, type: 'bridge', w: 96, h: 48 },
        { id: 'river_l', x: 0,   y: 300, type: 'solid',  w: 288, h: 48 },
        { id: 'river_r', x: 384, y: 300, type: 'solid',  w: W - 384, h: 48 },
        { id: 'plot1',   x: 80,  y: 80,  type: 'soil', status: 'empty', timer: 0, w: 48, h: 48 },
        { id: 'plot2',   x: 128, y: 80,  type: 'soil', status: 'empty', timer: 0, w: 48, h: 48 },
        { id: 'plot3',   x: 176, y: 80,  type: 'soil', status: 'empty', timer: 0, w: 48, h: 48 },
    ],
    questsDone: { q1: false, q2: false },
};

// ── INPUT ────────────────────────────────────────────────────
const keys = {};
window.addEventListener('keydown', e => {
    keys[e.code] = true;
    if (e.code === 'KeyM') audio.ambient && audio.ambient.play().catch(() => {});
});
window.addEventListener('keyup', e => { keys[e.code] = false; });

// ── HELPERS ──────────────────────────────────────────────────
function collides(a, b) {
    return a.x < b.x + (b.w || b.width)  && a.x + (a.w || a.width)  > b.x &&
           a.y < b.y + (b.h || b.height) && a.y + (a.h || a.height) > b.y;
}

function showDialogue(text) {
    ui.dialogText.innerText = text;
    ui.dialogue.classList.remove('hidden');
}
function closeDialogue() { ui.dialogue.classList.add('hidden'); }

// ── LEVEL UP ─────────────────────────────────────────────────
function checkLevelUp() {
    if (player.xp >= player.nextLevelXp) {
        player.xp -= player.nextLevelXp;
        player.level++;
        player.nextLevelXp = Math.floor(player.nextLevelXp * 1.6);
        player.maxHp += 20;
        player.maxEnergy += 10;
        player.hp = player.maxHp;
        player.energy = player.maxEnergy;
        showDialogue(`¡NIVEL ${player.level}! HP y Energía al máximo. Siguiente nivel: ${player.nextLevelXp} XP`);
        updateUI();
    }
}

// ── UI UPDATE ────────────────────────────────────────────────
function updateUI() {
    const hp  = Math.floor(player.hp);
    const eng = Math.floor(player.energy);
    ui.hpText.innerText  = `Lvl ${player.level} | HP: ${hp}/${player.maxHp}`;
    ui.engText.innerText = `ENG: ${eng}/${player.maxEnergy}`;
    ui.xpText.innerText  = `XP: ${player.xp}/${player.nextLevelXp}`;
    ui.hpFill.style.width  = `${(player.hp  / player.maxHp)  * 100}%`;
    ui.engFill.style.width = `${(player.energy / player.maxEnergy) * 100}%`;
    ui.xpFill.style.width  = `${Math.min(100, (player.xp / player.nextLevelXp) * 100)}%`;
    ui.gold.innerText = `Oro: ${player.gold}`;

    const maiz = player.inventory.maiz;
    ui.inventory.innerHTML = maiz > 0
        ? `<li class="text-green-400">🌽 Maíz x${maiz}</li>`
        : '<li class="text-gray-600 italic">Vacío</li>';

    // Quest 1
    const done1 = world.questsDone.q1;
    ui.q1.className = done1 ? 'text-green-500 line-through' : 'text-white';
    ui.q1.innerText = done1 ? '✓ Cosecha 3 mazorcas' : `◦ Cosecha 3 mazorcas (${maiz}/3)`;

    // Quest 2
    ui.q2.className = world.questsDone.q2 ? 'text-green-500 line-through' : 'text-gray-500';
    ui.q2.innerText = world.questsDone.q2 ? '✓ Viaja a Chiquimula' : '◦ Viaja a Chiquimula';
}

// ── SAVE / LOAD ──────────────────────────────────────────────
async function saveGame() {
    try {
        const res = await fetch('api/save_game.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: player.username, level: player.level, xp: player.xp,
                hp: player.hp, energy: player.energy, gold: player.gold,
                inventory: player.inventory, region: world.region,
            }),
        });
        const data = await res.json();
        showDialogue(data.status === 'success' ? '¡Progreso Guardado!' : 'Error al guardar: ' + data.message);
    } catch (e) { showDialogue('Error de conexión al guardar.'); }
}

async function loadGame() {
    try {
        const res  = await fetch('api/load_game.php');
        const data = await res.json();
        if (data.status === 'success') {
            const p = data.player;
            player.level      = parseInt(p.level)    || 1;
            player.xp         = parseInt(p.xp)       || 0;
            player.hp         = parseInt(p.hp)        || 100;
            player.maxHp      = parseInt(p.max_hp)    || 100;
            player.energy     = parseInt(p.energy)    || 80;
            player.maxEnergy  = parseInt(p.max_energy)|| 80;
            player.gold       = parseInt(p.gold)      || 0;
            player.nextLevelXp= Math.floor(100 * Math.pow(1.6, player.level - 1));
            player.dead       = false;
            world.region      = p.current_region || 'zacapa';
            ui.region.innerText = world.region === 'zacapa' ? 'Santiago, Zacapa' : 'San Juan, Chiquimula';
            updateUI();
            showDialogue(`Cargado: Lvl ${player.level} | ${player.gold} oro`);
        } else {
            showDialogue('No hay partida guardada.');
        }
    } catch (e) { showDialogue('Error de conexión al cargar.'); }
}

async function startNewGame() {
    player.level = 1; player.xp = 0; player.hp = 100; player.maxHp = 100;
    player.energy = 80; player.maxEnergy = 80; player.gold = 0;
    player.inventory = { maiz: 0 };
    player.nextLevelXp = 100; player.dead = false;
    world.region = 'zacapa'; world.enemies = [];
    world.objects.filter(o => o.type === 'soil').forEach(o => { o.status = 'empty'; o.timer = 0; });
    world.questsDone = { q1: false, q2: false };
    player.x = 300; player.y = 180;
    ui.region.innerText = 'Santiago, Zacapa';
    updateUI();

    try {
        await fetch('api/save_game.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: player.username, level: 1, xp: 0,
                hp: 100, energy: 80, gold: 0,
                inventory: { maiz: 0 }, region: 'zacapa',
            }),
        });
    } catch (e) {}
    showDialogue('¡Partida Nueva! Habla con Marisol para empezar.');
}

// ── REGION TRANSITION ────────────────────────────────────────
function transitionToRegion(newRegion) {
    if (world.transitioning) return;
    world.transitioning = true;
    ui.fade.style.opacity = '1';
    setTimeout(() => {
        world.region = newRegion;
        ui.region.innerText = newRegion === 'zacapa' ? 'Santiago, Zacapa' : 'San Juan, Chiquimula';
        player.x = 40; player.y = 200;
        world.enemies = [];
        if (newRegion === 'chiquimula' && !world.questsDone.q2) {
            world.questsDone.q2 = true;
            updateUI();
        }
        setTimeout(() => { ui.fade.style.opacity = '0'; world.transitioning = false; }, 1000);
    }, 1000);
}

// ── ENEMY SPAWN ──────────────────────────────────────────────
function spawnEnemy() {
    if (world.enemies.length < 4) {
        const side = Math.random() < 0.5 ? 0 : W - 24;
        world.enemies.push({ x: side, y: 80 + Math.random() * 160, w: 24, h: 32, speed: 1.0 + Math.random() * 0.5 });
    }
}

// ── UPDATE ───────────────────────────────────────────────────
function update() {
    if (world.transitioning) return;

    world.time = (world.time + 1) % 5000;
    player.frame++;

    // Clouds
    world.clouds.forEach(c => { c.x += c.speed; if (c.x > W + c.w) c.x = -c.w; });

    // Night cycle
    const nightI = Math.max(0, Math.sin(world.time * 0.001257) * 0.75);
    if (nightI > 0.35 && Math.random() < 0.008) spawnEnemy();
    if (nightI < 0.15) world.enemies = [];
    ui.time.innerText = nightI > 0.3 ? '🌙 Noche' : '☀ Día';

    // Energy regen (slow, 1 per ~2 seconds at 60fps)
    if (world.time % 120 === 0 && player.energy < player.maxEnergy && !player.dead) {
        player.energy = Math.min(player.maxEnergy, player.energy + 1);
        updateUI();
    }

    // Death state
    if (player.hp <= 0 && !player.dead) {
        player.dead = true;
        player.hp = 0;
        showDialogue('¡Has caído! Presiona E cerca de la casa para recuperarte.');
        updateUI();
        return;
    }

    if (player.dead) {
        // Can only rest at house to recover
        if ((keys['KeyE'] || keys['Enter']) && collides(player, { ...world.objects[0], w: 120, h: 120 })) {
            player.hp = player.maxHp;
            player.energy = player.maxEnergy;
            player.dead = false;
            world.time = 0;
            showDialogue('Has descansado. Fuerzas recuperadas.');
            updateUI();
        }
        return;
    }

    // Grow crops
    world.objects.forEach(obj => {
        if (obj.status === 'seedling') {
            obj.timer--;
            if (obj.timer <= 0) obj.status = 'ready';
        }
    });

    // SPACE: interact (sow / harvest)
    if (keys['Space'] && !player.attacking) {
        if (player.energy >= 5) {
            player.attacking = true; player.atkFrame = 0;
            audio.playHit();
            let acted = false;
            world.objects.forEach(obj => {
                if (obj.type === 'soil' && collides(player, obj)) {
                    if (obj.status === 'empty') {
                        obj.status = 'seedling'; obj.timer = 300;
                        player.energy = Math.max(0, player.energy - 10);
                        acted = true;
                        showDialogue('¡Sembrado! El maíz tardará un momento en crecer. (-10 ENG)');
                    } else if (obj.status === 'ready') {
                        obj.status = 'empty';
                        player.inventory.maiz++;
                        player.energy = Math.max(0, player.energy - 5);
                        acted = true;
                        player.xp += 15;
                        checkLevelUp();
                        // Quest 1
                        if (!world.questsDone.q1 && player.inventory.maiz >= 3) {
                            world.questsDone.q1 = true;
                            showDialogue('¡Misión completada! Cosechaste 3 mazorcas. (+50 XP)');
                            player.xp += 50;
                            checkLevelUp();
                        } else {
                            showDialogue(`¡Cosechado! Maíz x${player.inventory.maiz} (-5 ENG)`);
                        }
                    }
                }
            });
            if (!acted) player.energy = Math.max(0, player.energy - 2);
            updateUI();
        } else {
            showDialogue('Sin energía. Descansa en casa (E).');
        }
    }

    if (player.attacking) {
        player.atkFrame++;
        if (player.atkFrame > 15) player.attacking = false;
    }

    // Movement
    let nx = player.x, ny = player.y;
    player.moving = false;
    if (!player.attacking) {
        if      (keys['ArrowUp']    || keys['KeyW']) { ny -= 4; player.dir = 'up';    player.moving = true; }
        else if (keys['ArrowDown']  || keys['KeyS']) { ny += 4; player.dir = 'down';  player.moving = true; }
        else if (keys['ArrowLeft']  || keys['KeyA']) { nx -= 4; player.dir = 'left';  player.moving = true; }
        else if (keys['ArrowRight'] || keys['KeyD']) { nx += 4; player.dir = 'right'; player.moving = true; }
    }

    // Rest at house
    if ((keys['KeyE'] || keys['Enter']) && collides(player, { ...world.objects[0], w: 120, h: 120 })) {
        player.hp = player.maxHp; player.energy = player.maxEnergy; world.time = 0;
        showDialogue('Has descansado en casa. ¡Fuerzas al máximo!'); updateUI();
    }

    // Region transition (right edge → Chiquimula)
    if (player.x > W - 24) transitionToRegion('chiquimula');
    // Chiquimula → back to Zacapa (left edge)
    if (player.x < 4 && world.region === 'chiquimula') transitionToRegion('zacapa');

    // Collision with solid objects
    let blocked = false;
    world.objects.forEach(obj => {
        if ((obj.type === 'solid' || obj.type === 'house') &&
            collides({ x: nx, y: ny, w: player.w, h: player.h }, obj)) {
            blocked = true;
        }
    });
    if (!blocked) {
        player.x = Math.max(0, Math.min(W - player.w, nx));
        player.y = Math.max(48, Math.min(H - player.h, ny));
    }

    // Enemies — use filter to avoid splice-in-forEach bug
    world.enemies = world.enemies.filter(enemy => {
        const dx = player.x - enemy.x;
        const dy = player.y - enemy.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 200 && dist > 0) {
            enemy.x += (dx / dist) * enemy.speed;
            enemy.y += (dy / dist) * enemy.speed;
        }
        // Attack hit
        if (player.attacking && player.atkFrame === 5 &&
            collides({ x: player.x - 20, y: player.y - 20, w: 72, h: 88 }, enemy)) {
            player.xp += 30;
            checkLevelUp();
            updateUI();
            return false; // destroy enemy
        }
        // Enemy hits player
        if (collides(player, enemy)) {
            player.hp = Math.max(0, player.hp - 0.25);
            updateUI();
        }
        return true;
    });

    // NPC interaction (trigger once per contact)
    world.npcs.forEach(npc => {
        const rect = { x: npc.x, y: npc.y, w: npc.w, h: npc.h };
        if (collides(player, rect) && ui.dialogue.classList.contains('hidden') && player.moving) {
            showDialogue(`${npc.name}: ${npc.dialogue}`);
            if (npc.id === 'chepe' && player.inventory.maiz > 0) {
                const earned = player.inventory.maiz * 10;
                player.gold += earned;
                player.xp   += player.inventory.maiz * 5;
                showDialogue(`Don Chepe: Aquí tienes ${earned} de oro por el maíz. ¡Buen trabajo!`);
                player.inventory.maiz = 0;
                checkLevelUp();
                updateUI();
            }
        }
    });
}

// ── DRAW ─────────────────────────────────────────────────────
function draw() {
    ctx.clearRect(0, 0, W, H);

    // Sky fill (transitions with night)
    const nightI = Math.max(0, Math.sin(world.time * 0.001257) * 0.75);
    const r = Math.floor(135 - nightI * 120);
    const g = Math.floor(195 - nightI * 180);
    const b = Math.floor(235 - nightI * 220);
    ctx.fillStyle = `rgb(${r},${g},${b})`;
    ctx.fillRect(0, 0, W, H);

    // Stars at night
    if (nightI > 0.3) {
        ctx.fillStyle = `rgba(255,255,255,${(nightI - 0.3) * 1.2})`;
        for (let s = 0; s < 40; s++) {
            const sx = ((s * 173 + 11) % W);
            const sy = ((s * 97 + 7)  % 120);
            ctx.fillRect(sx, sy, 1, 1);
        }
    }

    const S = assets.santiago;

    // Ground tiles (with variety)
    for (let tx = 0; tx < W; tx += TILE) {
        for (let ty = 60; ty < H; ty += TILE) {
            const tile = groundTile(tx / TILE, ty / TILE);
            if (tile.complete && tile.naturalWidth) ctx.drawImage(tile, tx, ty, TILE, TILE);
            else { ctx.fillStyle = '#78583a'; ctx.fillRect(tx, ty, TILE, TILE); }
        }
    }

    // Horizontal path toward bridge
    if (S.path.complete && S.path.naturalWidth) {
        for (let px = 0; px < W; px += TILE) {
            ctx.drawImage(S.path, px, 252, TILE, 24);
        }
    }

    // River bank strip (just above water)
    const riverBankImg = S.river_bank;
    if (riverBankImg.complete && riverBankImg.naturalWidth) {
        for (let rx = 0; rx < W; rx += TILE) {
            if (rx < 288 || rx >= 384) {
                ctx.drawImage(riverBankImg, rx, 292, TILE, 16);
            }
        }
    }

    // Objects
    world.objects.forEach(obj => {
        const x = obj.x, y = obj.y, ow = obj.w || obj.width, oh = obj.h || obj.height;

        if (obj.type === 'soil') {
            const base = S.tilled_soil;
            if (base.complete && base.naturalWidth) ctx.drawImage(base, x, y, 48, 48);
            else { ctx.fillStyle = '#5c3d1e'; ctx.fillRect(x + 2, y + 2, 44, 44); }
            ctx.strokeStyle = '#3b1f0a';
            ctx.lineWidth = 2;
            ctx.strokeRect(x + 3, y + 3, 42, 42);

            if (obj.status === 'seedling') {
                const img = obj.timer > 150 ? S.corn_seedling : S.corn_mid;
                if (img && img.complete && img.naturalWidth) ctx.drawImage(img, x, y, 48, 48);
                else { ctx.fillStyle = '#16a34a'; ctx.fillRect(x + 20, y + 28, 8, 16); }
            } else if (obj.status === 'ready') {
                if (S.corn.complete && S.corn.naturalWidth) ctx.drawImage(S.corn, x, y, 48, 48);
                else { ctx.fillStyle = '#eab308'; ctx.fillRect(x + 12, y + 8, 24, 36); }
            }

        } else if (obj.type === 'house') {
            const drawOrFill = (img, dx, dy, dw, dh, fb) => {
                if (img && img.complete && img.naturalWidth) ctx.drawImage(img, dx, dy, dw, dh);
                else { ctx.fillStyle = fb; ctx.fillRect(dx, dy, dw, dh); }
            };
            drawOrFill(S.house_wall, x,      y + 48, 48, 48, '#a16207');
            drawOrFill(S.house_door, x + 48, y + 48, 48, 48, '#92400e');
            drawOrFill(S.house_roof, x,      y,      96, 48, '#b91c1c');

        } else if (obj.type === 'bridge') {
            ctx.fillStyle = '#92400e';
            ctx.fillRect(x, y, ow, oh);
            ctx.strokeStyle = '#78350f'; ctx.lineWidth = 2;
            for (let bx = x + 8; bx < x + ow - 4; bx += 16) {
                ctx.beginPath(); ctx.moveTo(bx, y); ctx.lineTo(bx, y + oh); ctx.stroke();
            }

        } else if (obj.type === 'solid') {
            // River water
            for (let rx = x; rx < x + ow; rx += TILE) {
                if (S.water.complete && S.water.naturalWidth) ctx.drawImage(S.water, rx, y, TILE, oh);
                else { ctx.fillStyle = '#1d4ed8'; ctx.fillRect(rx, y, TILE, oh); }
            }
        }
    });

    // Decorations
    world.decorations.forEach(dec => {
        if (dec.type === 'shop') {
            if (S.house_roof.complete && S.house_roof.naturalWidth)
                ctx.drawImage(S.house_roof, dec.x, dec.y - 24, 64, 32);
            ctx.fillStyle = '#d97706';
            ctx.fillRect(dec.x + 6, dec.y + 8, 52, 22);
            ctx.fillStyle = '#1c1917';
            ctx.font = '6px monospace';
            ctx.fillText('TIENDA', dec.x + 10, dec.y + 22);
        } else {
            const img = S[dec.type];
            if (img && img.complete && img.naturalWidth) ctx.drawImage(img, dec.x, dec.y, 48, 48);
            else {
                ctx.fillStyle = '#15803d';
                ctx.fillRect(dec.x + 16, dec.y + 8, 16, 36);
            }
        }
    });

    // NPCs
    world.npcs.forEach(npc => {
        // Body
        ctx.fillStyle = npc.color;
        ctx.fillRect(npc.x + 8, npc.y + 12, 16, 32);
        // Head
        ctx.fillStyle = '#fcd34d';
        ctx.fillRect(npc.x + 10, npc.y + 4, 12, 12);
        // Name tag
        ctx.fillStyle = 'rgba(0,0,0,0.5)';
        ctx.fillRect(npc.x - 4, npc.y - 12, 40, 10);
        ctx.fillStyle = '#fff';
        ctx.font = '5px monospace';
        ctx.fillText(npc.name, npc.x - 2, npc.y - 4);
    });

    // Enemies (shadow spirit)
    world.enemies.forEach(enemy => {
        ctx.save();
        ctx.globalAlpha = 0.75;
        ctx.fillStyle = '#1e1b4b';
        ctx.beginPath();
        ctx.ellipse(enemy.x + 12, enemy.y + 16, 12, 16, 0, 0, Math.PI * 2);
        ctx.fill();
        // Glowing eyes
        ctx.fillStyle = '#ef4444';
        ctx.fillRect(enemy.x + 6, enemy.y + 10, 3, 3);
        ctx.fillRect(enemy.x + 15, enemy.y + 10, 3, 3);
        ctx.restore();
    });

    // Player
    if (assets.ready) {
        const frame = player.moving ? Math.floor(player.frame / 10) % 3 : 0;
        const sprite = assets.oscar[player.dir][frame];
        if (sprite && sprite.complete && sprite.naturalWidth) {
            ctx.drawImage(sprite, player.x - 8, player.y, 48, 48);
        }
    } else {
        // Placeholder while loading
        ctx.fillStyle = '#f97316';
        ctx.fillRect(player.x + 4, player.y + 12, 24, 32);
        ctx.fillStyle = '#fcd34d';
        ctx.fillRect(player.x + 8, player.y + 4, 16, 12);
    }

    // Attack flash
    if (player.attacking && player.atkFrame < 8) {
        ctx.save();
        ctx.globalAlpha = 0.3;
        ctx.fillStyle = '#fbbf24';
        ctx.beginPath();
        ctx.arc(player.x + 16, player.y + 24, 36, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
    }

    // Clouds
    ctx.save();
    ctx.globalAlpha = 0.25;
    ctx.fillStyle = '#ffffff';
    world.clouds.forEach(c => {
        ctx.beginPath();
        ctx.ellipse(c.x, c.y, c.w / 2, c.h / 2, 0, 0, Math.PI * 2);
        ctx.fill();
    });
    ctx.restore();

    // Night overlay
    if (nightI > 0) {
        ctx.fillStyle = `rgba(10,15,40,${nightI * 0.8})`;
        ctx.fillRect(0, 0, W, H);
    }

    // Loading indicator
    if (!assets.ready) {
        const pct = Math.floor((assets.loaded / assets.total) * 100);
        ctx.fillStyle = 'rgba(0,0,0,0.7)';
        ctx.fillRect(W / 2 - 80, H / 2 - 20, 160, 40);
        ctx.fillStyle = '#fbbf24';
        ctx.font = '8px monospace';
        ctx.fillText(`Cargando... ${pct}%`, W / 2 - 55, H / 2 + 4);
    }
}

// ── MAIN LOOP ────────────────────────────────────────────────
audio.init();

function loop() {
    update();
    draw();
    requestAnimationFrame(loop);
}
loop();

// Expose for HTML buttons
window.startNewGame = startNewGame;
window.loadGame     = loadGame;
window.saveGame     = saveGame;
window.closeDialogue = closeDialogue;
</script>
</body>
</html>
