<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = '127.0.0.1'; $db = 'qanil_rpg'; $user = 'root'; $pass = ''; $charset = 'utf8mb4';
$db_status = "Pendiente";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $db_status = "Conectado";
} catch (\PDOException $e) { $db_status = "Error: " . $e->getMessage(); }
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
        * { box-sizing: border-box; }
        body { font-family: 'Press Start 2P', cursive; background: #050200; color: #e2e8f0; margin: 0; overflow: hidden; }
        .pixel { image-rendering: pixelated; }
        #fade-overlay { pointer-events: none; transition: opacity 1s; }

        /* ── START MENU ─────────────────────────── */
        #start-menu {
            position: fixed; inset: 0; z-index: 200;
            background: radial-gradient(ellipse at 50% 60%, #1c0900 0%, #050200 100%);
            display: flex; align-items: center; justify-content: center;
            transition: opacity 0.4s;
        }
        #start-menu.hidden { opacity: 0; pointer-events: none; }

        .sm-wrap { position: relative; display: flex; flex-direction: column; align-items: center; }
        .sm-logo  { width: 380px; margin-bottom: -24px; position: relative; z-index: 2;
                    filter: drop-shadow(0 0 18px rgba(200,150,0,0.55)); }
        .sm-panel-wrap { position: relative; width: 420px; }
        .sm-panel-bg   { width: 420px; display: block; }
        .sm-panel-body {
            position: absolute; inset: 0;
            display: flex; flex-direction: column; align-items: center;
            padding-top: 52px;
        }
        .sm-subtitle { width: 290px; margin-bottom: 6px; }
        .sm-divider  { width: 90px;  margin-bottom: 12px; }

        .sm-btn-list { position: relative; display: flex; flex-direction: column; gap: 5px; }
        #sm-diamond  {
            position: absolute; width: 28px; right: calc(100% + 8px);
            top: 0; transition: top 0.1s ease;
            filter: drop-shadow(0 0 5px #4ade80);
        }
        .sm-btn { cursor: pointer; height: 48px; display: flex; align-items: center; }
        .sm-btn img { height: 42px; width: auto; transition: filter 0.1s; }
        .sm-btn:hover img { filter: brightness(1.12) drop-shadow(0 0 4px #fbbf24); }

        .sm-ornament-bot { width: 200px; margin-top: 8px; opacity: 0.9; }
        .sm-colgante { position: absolute; width: 58px; top: 130px; opacity: 0.85; }
        .sm-colgante.left  { left:  -52px; }
        .sm-colgante.right { right: -52px; transform: scaleX(-1); }
        .sm-plaque { width: 230px; margin-top: 10px; opacity: 0.75; }

        /* ── PAUSE MENU ─────────────────────────── */
        #pause-menu {
            position: fixed; inset: 0; z-index: 150;
            background: rgba(5,2,0,0.84);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none; transition: opacity 0.25s;
        }
        #pause-menu.active { opacity: 1; pointer-events: all; }

        .pm-wrap    { display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .pm-header  { width: 340px; }
        .pm-cols    { display: flex; gap: 14px; align-items: flex-start; }

        /* Left column: buttons panel */
        .pm-left    { position: relative; width: 270px; }
        .pm-left-bg { width: 230px; display: block; margin-left: 40px; }
        .pm-btn-overlay {
            position: absolute; top: 0; left: 40px; right: 0; bottom: 0;
            display: flex; flex-direction: column; align-items: center;
            padding-top: 42px; gap: 2px;
        }
        #pm-diamond {
            position: absolute; left: -14px; width: 20px;
            top: 42px; transition: top 0.1s ease;
            filter: drop-shadow(0 0 4px #4ade80);
        }
        .pm-btn { cursor: pointer; height: 42px; display: flex; align-items: center; }
        .pm-btn img { height: 36px; width: auto; transition: filter 0.1s; }
        .pm-btn:hover img { filter: brightness(1.12) drop-shadow(0 0 4px #fbbf24); }

        /* Right column */
        .pm-right  { display: flex; flex-direction: column; gap: 8px; }
        .pm-panel  { position: relative; }
        .pm-panel-bg { width: 270px; display: block; }
        .pm-panel-content {
            position: absolute; inset: 0; padding: 13px 15px;
            font-size: 6.5px; line-height: 1.5; color: #e2e8f0;
        }
        .pm-bar { height: 5px; background: #111; border: 1px solid #374151; margin-bottom: 5px; }
        .pm-bar-fill { height: 100%; transition: width 0.3s; }

        /* ── GAME CONTAINER ─────────────────────── */
        #game-wrap {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; min-height: 100vh; padding: 12px;
        }
        .bar-container { width: 90px; height: 6px; background: #111827; border: 1px solid #374151; margin-top: 3px; }
        .bar-fill { height: 100%; transition: width 0.3s; }
    </style>
</head>
<body>

<div id="fade-overlay" class="fixed inset-0 bg-black opacity-0 z-50"></div>

<!-- ═══════════════════════════════════════════════════════════
     START MENU
═══════════════════════════════════════════════════════════ -->
<div id="start-menu">
  <div class="sm-wrap">
    <img class="sm-logo pixel" src="ui_qanil_elementos_recortados/start_menu/logo/logo_cronicas_de_qanil.png">

    <div class="sm-panel-wrap">
      <img class="sm-panel-bg pixel" src="ui_qanil_elementos_recortados/start_menu/panels/panel_principal_pergamino.png">
      <div class="sm-panel-body">
        <img class="sm-subtitle pixel" src="ui_qanil_elementos_recortados/start_menu/logo/subtitle_el_ascenso_de_oscar_leon.png">
        <img class="sm-divider pixel"  src="ui_qanil_elementos_recortados/start_menu/ornaments/linea_divisoria_jade.png">

        <div class="sm-btn-list" id="sm-btns">
          <img id="sm-diamond" class="pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/selector_diamante_verde.png">
          <div class="sm-btn" data-idx="0"><img class="pixel" src="ui_qanil_elementos_recortados/start_menu/buttons_normal/btn_nueva_partida.png"></div>
          <div class="sm-btn" data-idx="1"><img class="pixel" src="ui_qanil_elementos_recortados/start_menu/buttons_normal/btn_continuar.png"></div>
          <div class="sm-btn" data-idx="2"><img class="pixel" src="ui_qanil_elementos_recortados/start_menu/buttons_normal/btn_cargar_partida.png"></div>
          <div class="sm-btn" data-idx="3"><img class="pixel" src="ui_qanil_elementos_recortados/start_menu/buttons_normal/btn_configuracion.png"></div>
          <div class="sm-btn" data-idx="4"><img class="pixel" src="ui_qanil_elementos_recortados/start_menu/buttons_normal/btn_creditos.png"></div>
          <div class="sm-btn" data-idx="5"><img class="pixel" src="ui_qanil_elementos_recortados/start_menu/buttons_normal/btn_salir.png"></div>
        </div>

        <img class="sm-ornament-bot pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/ornamentos_inferiores.png">
      </div>
    </div>

    <img class="sm-colgante left  pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/colgante_jade.png">
    <img class="sm-colgante right pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/colgante_jade.png">
    <img class="sm-plaque pixel" src="ui_qanil_elementos_recortados/start_menu/plaques/placa_version_hecho_en_guatemala.png">
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     PAUSE MENU
═══════════════════════════════════════════════════════════ -->
<div id="pause-menu">
  <div class="pm-wrap">
    <img class="pm-header pixel" src="ui_qanil_elementos_recortados/pause_menu/header/header_juego_en_pausa.png">

    <div class="pm-cols">
      <!-- Left: navigation buttons -->
      <div class="pm-left">
        <img class="pm-left-bg pixel" src="ui_qanil_elementos_recortados/pause_menu/panels/panel_menu_principal.png">
        <div class="pm-btn-overlay" id="pm-btns">
          <img id="pm-diamond" class="pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/selector_diamante_verde.png">
          <div class="pm-btn" data-idx="0"><img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/buttons_normal/btn_reanudar.png"></div>
          <div class="pm-btn" data-idx="1"><img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/buttons_normal/btn_guardar.png"></div>
          <div class="pm-btn" data-idx="2"><img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/buttons_normal/btn_inventario.png"></div>
          <div class="pm-btn" data-idx="3"><img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/buttons_normal/btn_misiones.png"></div>
          <div class="pm-btn" data-idx="4"><img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/buttons_normal/btn_mapa.png"></div>
          <div class="pm-btn" data-idx="5"><img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/buttons_normal/btn_habilidades.png"></div>
          <div class="pm-btn" data-idx="6"><img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/buttons_normal/btn_configuracion.png"></div>
          <div class="pm-btn" data-idx="7"><img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/buttons_normal/btn_salir_menu_principal.png"></div>
        </div>
      </div>

      <!-- Right: character status + objectives -->
      <div class="pm-right">
        <!-- Character panel -->
        <div class="pm-panel">
          <img class="pm-panel-bg pixel" src="ui_qanil_elementos_recortados/pause_menu/panels/panel_estado_personaje.png">
          <div class="pm-panel-content">
            <div style="display:flex;gap:8px;align-items:flex-start;margin-bottom:8px">
              <img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/icons/retrato_oscar.png" style="width:62px">
              <div style="color:#fbbf24">
                <div style="margin-bottom:2px">Oscar León</div>
                <div id="pm-level" style="color:#a78bfa;margin-bottom:4px">Nivel 1</div>
                <div id="pm-gold" style="color:#fbbf24">Oro: 0</div>
              </div>
            </div>
            <div id="pm-hp"  style="color:#f87171;margin-bottom:2px">HP: 100/100</div>
            <div class="pm-bar"><div id="pm-hp-fill"  class="pm-bar-fill" style="background:#ef4444;width:100%"></div></div>
            <div id="pm-eng" style="color:#60a5fa;margin-bottom:2px">ENG: 80/80</div>
            <div class="pm-bar"><div id="pm-eng-fill" class="pm-bar-fill" style="background:#3b82f6;width:100%"></div></div>
            <div id="pm-xp"  style="color:#a78bfa;margin-bottom:2px">XP: 0/100</div>
            <div class="pm-bar"><div id="pm-xp-fill"  class="pm-bar-fill" style="background:#8b5cf6;width:0%"></div></div>
          </div>
        </div>

        <!-- Objectives panel -->
        <div class="pm-panel">
          <img class="pm-panel-bg pixel" src="ui_qanil_elementos_recortados/pause_menu/panels/panel_objetivo_actual.png">
          <div class="pm-panel-content">
            <div id="pm-q1" style="margin-bottom:6px">◦ Cosecha 3 mazorcas (0/3)</div>
            <div id="pm-q2" style="color:#6b7280">◦ Viaja a Chiquimula</div>
          </div>
        </div>

        <img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/plaques/placa_version_hecho_en_guatemala.png"
             style="width:240px;opacity:0.7">
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     GAME
═══════════════════════════════════════════════════════════ -->
<div id="game-wrap">
    <div class="text-center mb-3">
        <h1 class="text-xl text-yellow-400 mb-1">Crónicas de Q'anil</h1>
        <h2 class="text-xs text-green-400">El Ascenso de Oscar León</h2>
    </div>

    <div class="bg-gray-800 border-4 border-gray-600 p-4 rounded-lg max-w-5xl w-full flex flex-col md:flex-row gap-4 shadow-2xl">
        <div class="flex-1">
            <div class="mb-2 text-xs text-gray-400 flex justify-between items-center">
                <span>Motor: <span class="text-green-500">Activo</span> | DB: <span class="text-blue-400"><?php echo htmlspecialchars($db_status); ?></span></span>
                <span id="player-gold" class="text-yellow-400 font-bold">Oro: 0</span>
            </div>

            <div class="w-full bg-black border-2 border-gray-700 relative overflow-hidden mb-3 rounded shadow-inner" style="height:380px">
                <canvas id="gameCanvas" width="672" height="380" class="pixel absolute inset-0 w-full h-full"></canvas>
                <div id="ui-layer" class="absolute inset-0 p-3 z-10 flex flex-col justify-between pointer-events-none">
                    <div class="flex justify-between items-start">
                        <div class="bg-black/60 p-2 rounded border border-white/10">
                            <p id="hp-bar-text" class="text-red-400 text-[9px] font-bold">Lvl 1 | HP: 100/100</p>
                            <div class="bar-container"><div id="hp-fill" class="bar-fill bg-red-600" style="width:100%"></div></div>
                            <p id="energy-bar-text" class="text-blue-400 text-[9px] font-bold mt-1">ENG: 80/80</p>
                            <div class="bar-container"><div id="energy-fill" class="bar-fill bg-blue-600" style="width:100%"></div></div>
                            <p id="xp-bar-text" class="text-purple-400 text-[9px] font-bold mt-1">XP: 0/100</p>
                            <div class="bar-container"><div id="xp-fill" class="bar-fill bg-purple-600" style="width:0%"></div></div>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <span id="time-display" class="text-[9px] px-2 py-1 bg-black/60 rounded border border-white/20">☀ Día</span>
                            <span id="region-display" class="text-[8px] text-white/70 bg-black/60 px-2 py-1 rounded border border-white/10 uppercase tracking-wider">Santiago, Zacapa</span>
                        </div>
                    </div>
                    <div id="dialogue-box" class="hidden self-center mb-2 bg-black/95 border-2 border-yellow-500 p-3 text-[9px] text-white max-w-[85%] pointer-events-auto shadow-2xl rounded">
                        <p id="dialogue-text" class="leading-relaxed"></p>
                        <div class="mt-2 flex justify-end">
                            <button onclick="closeDialogue()" class="text-yellow-400 hover:text-yellow-300 text-[8px] uppercase border border-yellow-600 px-2 py-1 rounded">Cerrar ▶</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center gap-2 flex-wrap">
                <button onclick="saveGame()"      class="bg-purple-700 hover:bg-purple-600 text-white px-3 py-2 border-b-4 border-purple-900 active:border-b-0 active:translate-y-1 text-[8px] rounded transition-all">↓ Guardar</button>
                <button onclick="openPauseMenu()" class="bg-gray-700   hover:bg-gray-600   text-white px-3 py-2 border-b-4 border-gray-900   active:border-b-0 active:translate-y-1 text-[8px] rounded transition-all">❚❚ Pausa</button>
                <span class="text-[7px] text-gray-500 self-center">WASD/Flechas · ESPACIO · E=Casa · M=Música · ESC=Pausa</span>
            </div>
        </div>

        <div class="w-full md:w-52 flex flex-col gap-3">
            <div class="bg-gray-900 border-2 border-gray-700 p-3 rounded shadow-lg">
                <h3 class="text-[9px] text-yellow-400 mb-2 border-b border-gray-700 pb-2">⚔ MISIONES</h3>
                <ul id="quest-list" class="text-[7px] space-y-2">
                    <li id="q1" class="text-white">◦ Cosecha 3 mazorcas (0/3)</li>
                    <li id="q2" class="text-gray-600">◦ Viaja a Chiquimula</li>
                </ul>
            </div>
            <div class="bg-gray-900 border-2 border-gray-700 p-3 flex-1 rounded shadow-lg">
                <h3 class="text-[9px] text-green-400 mb-2 border-b border-gray-700 pb-2">🎒 INVENTARIO</h3>
                <ul id="inventory-list" class="text-[7px] space-y-1">
                    <li class="text-gray-600 italic">Vacío</li>
                </ul>
            </div>
            <div class="bg-gray-900 border-2 border-gray-700 p-3 rounded">
                <h3 class="text-[8px] text-gray-400 mb-2 border-b border-gray-700 pb-2">CONTROLES</h3>
                <ul class="text-[6.5px] text-gray-500 space-y-1">
                    <li>WASD / Flechas: Mover</li>
                    <li>ESPACIO: Sembrar/Cosechar/Atacar</li>
                    <li>E (cerca casa): Descansar</li>
                    <li>M: Música ambiental</li>
                    <li>ESC / Botón: Pausa</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// ── CONSTANTS ────────────────────────────────────────────────
const canvas = document.getElementById('gameCanvas');
const ctx    = canvas.getContext('2d');
const W = canvas.width;   // 672
const H = canvas.height;  // 380
const TILE = 48;
const UI_PATH = 'ui_qanil_elementos_recortados/';

// ── GAME STATE ───────────────────────────────────────────────
let gameState = 'start'; // 'start' | 'playing' | 'paused'

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
const assets = { oscar: { down:[], up:[], left:[], right:[] }, santiago: {}, loaded: 0, total: 0, ready: false };

function loadImg(src) {
    const img = new Image();
    img.onload  = () => { assets.loaded++; if (assets.loaded >= assets.total) assets.ready = true; };
    img.onerror = () => { assets.loaded++; if (assets.loaded >= assets.total) assets.ready = true; console.warn('Missing:', src); };
    img.src = src;
    return img;
}

['down','up','left','right'].forEach(dir => {
    ['01','02','03'].forEach(f => assets.oscar[dir].push(loadImg(`public/assets/img/oscar/uniformes_3x4/oscar_leon_${dir}_${f}.png`)));
});

const TILES = {
    ground: 'terrain/dry_soil_01.png', ground2: 'terrain/dry_soil_cracked_01.png',
    path: 'terrain/dust_path_01.png', packed: 'terrain/packed_yard_01.png',
    tilled_soil: 'agriculture/tilled_soil_01.png',
    corn_seedling: 'agriculture/corn_seedling.png', corn_mid: 'agriculture/corn_mid.png', corn: 'agriculture/corn_grown.png',
    cactus: 'vegetation/organ_cactus_01.png', bush: 'vegetation/dry_bush_01.png',
    rock: 'rocks_mountains/gray_boulder_01.png',
    water: 'water_irrigation/motagua_water_01.png', river_bank: 'water_irrigation/motagua_bank_bottom.png',
    house_wall: 'buildings/adobe_wall_01.png', house_door: 'buildings/adobe_wall_door_01.png',
    house_roof: 'buildings/teja_roof_01.png', fence: 'buildings/wooden_fence_01.png',
};
Object.keys(TILES).forEach(k => { assets.santiago[k] = loadImg(`public/assets/img/tiles_santiago/${TILES[k]}`); });
assets.total = 12 + Object.keys(TILES).length;

// ── PLAYER ───────────────────────────────────────────────────
const player = {
    x: 300, y: 180, w: 32, h: 48,
    hp: 100, maxHp: 100, energy: 80, maxEnergy: 80,
    level: 1, xp: 0, nextLevelXp: 100, gold: 0,
    inventory: { maiz: 0 },
    dir: 'down', moving: false, attacking: false,
    atkFrame: 0, frame: 0, username: 'OscarLeon', dead: false,
};

// ── WORLD ────────────────────────────────────────────────────
function groundTile(gx, gy) { return ((gx*7+gy*13)%8===0) ? assets.santiago.ground2 : assets.santiago.ground; }

const world = {
    region: 'zacapa', time: 0, enemies: [], transitioning: false,
    clouds: [
        { x: 10,  y: 30, speed: 0.25, w: 70, h: 22 },
        { x: 280, y: 55, speed: 0.18, w: 90, h: 28 },
        { x: 520, y: 20, speed: 0.12, w: 60, h: 18 },
    ],
    decorations: [
        { x:50,  y:50,  type:'cactus' }, { x:560, y:140, type:'cactus' },
        { x:200, y:215, type:'rock'   }, { x:500, y:60,  type:'rock'   },
        { x:100, y:195, type:'fence'  }, { x:148, y:195, type:'fence'  }, { x:196, y:195, type:'fence' },
        { x:30,  y:140, type:'bush'   }, { x:600, y:90,  type:'bush'   }, { x:420, y:40,  type:'bush'  },
        { x:480, y:200, type:'shop'   },
    ],
    npcs: [
        { id:'marisol', name:'Marisol',   x:450, y:110, w:32, h:48, color:'#f472b6',
          dialogue:'¡Hijo! La tierra está lista. ESPACIO para sembrar, ESPACIO de nuevo cuando crezca.' },
        { id:'chepe',   name:'Don Chepe', x:530, y:215, w:32, h:48, color:'#fbbf24',
          dialogue:'Te compro el maíz a 10 de oro cada uno. ¡Buena cosecha!' },
    ],
    objects: [
        { id:'house',   x:288, y:30,  type:'house',  w:96,       h:96  },
        { id:'bridge',  x:288, y:300, type:'bridge', w:96,       h:48  },
        { id:'river_l', x:0,   y:300, type:'solid',  w:288,      h:48  },
        { id:'river_r', x:384, y:300, type:'solid',  w:W-384,    h:48  },
        { id:'plot1',   x:80,  y:80,  type:'soil', status:'empty', timer:0, w:48, h:48 },
        { id:'plot2',   x:128, y:80,  type:'soil', status:'empty', timer:0, w:48, h:48 },
        { id:'plot3',   x:176, y:80,  type:'soil', status:'empty', timer:0, w:48, h:48 },
    ],
    questsDone: { q1: false, q2: false },
};

// ── INPUT ────────────────────────────────────────────────────
const keys = {};
const GAME_KEYS = new Set(['ArrowUp','ArrowDown','ArrowLeft','ArrowRight','KeyW','KeyA','KeyS','KeyD','Space','KeyE']);

window.addEventListener('keydown', e => {
    // ESC: toggle pause
    if (e.code === 'Escape') {
        e.preventDefault();
        if (gameState === 'playing') openPauseMenu();
        else if (gameState === 'paused') closePauseMenu();
        return;
    }
    // Start menu navigation
    if (gameState === 'start') {
        e.preventDefault();
        if (e.code === 'ArrowDown' || e.code === 'KeyS') smSetIdx((smIdx + 1) % SM_DEFS.length);
        else if (e.code === 'ArrowUp'   || e.code === 'KeyW') smSetIdx((smIdx - 1 + SM_DEFS.length) % SM_DEFS.length);
        else if (e.code === 'Enter' || e.code === 'Space') smActivate();
        return;
    }
    // Pause menu navigation
    if (gameState === 'paused') {
        e.preventDefault();
        if (e.code === 'ArrowDown' || e.code === 'KeyS') pmSetIdx((pmIdx + 1) % PM_DEFS.length);
        else if (e.code === 'ArrowUp'   || e.code === 'KeyW') pmSetIdx((pmIdx - 1 + PM_DEFS.length) % PM_DEFS.length);
        else if (e.code === 'Enter' || e.code === 'Space') pmActivate();
        return;
    }
    // Game keys
    keys[e.code] = true;
    if (GAME_KEYS.has(e.code)) e.preventDefault();
    if (e.code === 'KeyM') audio.ambient && audio.ambient.play().catch(() => {});
});
window.addEventListener('keyup', e => { keys[e.code] = false; });

// ── HELPERS ──────────────────────────────────────────────────
function collides(a, b) {
    return a.x < b.x + (b.w||b.width)  && a.x + (a.w||a.width)  > b.x &&
           a.y < b.y + (b.h||b.height) && a.y + (a.h||a.height) > b.y;
}
function showDialogue(text) { ui.dialogText.innerText = text; ui.dialogue.classList.remove('hidden'); }
function closeDialogue()    { ui.dialogue.classList.add('hidden'); }

// ── LEVEL UP ─────────────────────────────────────────────────
function checkLevelUp() {
    if (player.xp >= player.nextLevelXp) {
        player.xp -= player.nextLevelXp;
        player.level++;
        player.nextLevelXp = Math.floor(player.nextLevelXp * 1.6);
        player.maxHp += 20; player.maxEnergy += 10;
        player.hp = player.maxHp; player.energy = player.maxEnergy;
        showDialogue(`¡NIVEL ${player.level}! HP y Energía al máximo. Siguiente: ${player.nextLevelXp} XP`);
        updateUI();
    }
}

// ── UI UPDATE ─────────────────────────────────────────────────
function updateUI() {
    ui.hpText.innerText  = `Lvl ${player.level} | HP: ${Math.floor(player.hp)}/${player.maxHp}`;
    ui.engText.innerText = `ENG: ${Math.floor(player.energy)}/${player.maxEnergy}`;
    ui.xpText.innerText  = `XP: ${player.xp}/${player.nextLevelXp}`;
    ui.hpFill.style.width  = `${(player.hp  / player.maxHp)  * 100}%`;
    ui.engFill.style.width = `${(player.energy / player.maxEnergy) * 100}%`;
    ui.xpFill.style.width  = `${Math.min(100, (player.xp / player.nextLevelXp) * 100)}%`;
    ui.gold.innerText = `Oro: ${player.gold}`;

    const maiz = player.inventory.maiz;
    ui.inventory.innerHTML = maiz > 0
        ? `<li class="text-green-400">🌽 Maíz x${maiz}</li>`
        : '<li class="text-gray-600 italic">Vacío</li>';

    ui.q1.className = world.questsDone.q1 ? 'text-green-500 line-through' : 'text-white';
    ui.q1.innerText = world.questsDone.q1 ? '✓ Cosecha 3 mazorcas' : `◦ Cosecha 3 mazorcas (${maiz}/3)`;
    ui.q2.className = world.questsDone.q2 ? 'text-green-500 line-through' : 'text-gray-600';
    ui.q2.innerText = world.questsDone.q2 ? '✓ Viaja a Chiquimula' : '◦ Viaja a Chiquimula';
}

// ── SAVE / LOAD ───────────────────────────────────────────────
async function saveGame() {
    try {
        const res  = await fetch('api/save_game.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: player.username, level: player.level, xp: player.xp,
                hp: player.hp, energy: player.energy, gold: player.gold,
                inventory: player.inventory, region: world.region }),
        });
        const data = await res.json();
        showDialogue(data.status === 'success' ? '¡Progreso Guardado!' : 'Error: ' + data.message);
    } catch(e) { showDialogue('Error de conexión al guardar.'); }
}

async function loadGame() {
    try {
        const data = await (await fetch('api/load_game.php')).json();
        if (data.status === 'success') {
            const p = data.player;
            player.level     = parseInt(p.level)     || 1;
            player.xp        = parseInt(p.xp)        || 0;
            player.hp        = parseInt(p.hp)        || 100;
            player.maxHp     = parseInt(p.max_hp)    || 100;
            player.energy    = parseInt(p.energy)    || 80;
            player.maxEnergy = parseInt(p.max_energy)|| 80;
            player.gold      = parseInt(p.gold)      || 0;
            player.nextLevelXp = Math.floor(100 * Math.pow(1.6, player.level - 1));
            player.dead = false;
            world.region = p.current_region || 'zacapa';
            ui.region.innerText = world.region === 'zacapa' ? 'Santiago, Zacapa' : 'San Juan, Chiquimula';
            updateUI();
            showDialogue(`Cargado: Lvl ${player.level} | ${player.gold} oro`);
        } else {
            showDialogue('No hay partida guardada.');
        }
    } catch(e) { showDialogue('Error de conexión al cargar.'); }
}

async function startNewGame() {
    player.level=1; player.xp=0; player.hp=100; player.maxHp=100;
    player.energy=80; player.maxEnergy=80; player.gold=0;
    player.inventory={maiz:0}; player.nextLevelXp=100; player.dead=false;
    world.region='zacapa'; world.enemies=[];
    world.objects.filter(o=>o.type==='soil').forEach(o=>{o.status='empty';o.timer=0;});
    world.questsDone={q1:false,q2:false};
    player.x=300; player.y=180;
    ui.region.innerText='Santiago, Zacapa';
    updateUI();
    try {
        await fetch('api/save_game.php',{
            method:'POST',headers:{'Content-Type':'application/json'},
            body:JSON.stringify({username:player.username,level:1,xp:0,hp:100,energy:80,gold:0,inventory:{maiz:0},region:'zacapa'}),
        });
    } catch(e) {}
}

// ── TRANSITIONS ───────────────────────────────────────────────
function transitionToRegion(newRegion) {
    if (world.transitioning) return;
    world.transitioning = true;
    ui.fade.style.opacity = '1';
    setTimeout(() => {
        world.region = newRegion;
        ui.region.innerText = newRegion==='zacapa' ? 'Santiago, Zacapa' : 'San Juan, Chiquimula';
        player.x = 40; player.y = 200; world.enemies = [];
        if (newRegion==='chiquimula' && !world.questsDone.q2) { world.questsDone.q2=true; updateUI(); }
        setTimeout(() => { ui.fade.style.opacity='0'; world.transitioning=false; }, 1000);
    }, 1000);
}

function spawnEnemy() {
    if (world.enemies.length < 4) {
        const sx = Math.random()<0.5 ? 0 : W-24;
        world.enemies.push({ x:sx, y:80+Math.random()*160, w:24, h:32, speed:1.0+Math.random()*0.5 });
    }
}

// ── UPDATE ────────────────────────────────────────────────────
function update() {
    if (gameState !== 'playing') return;
    if (world.transitioning) return;

    world.time = (world.time + 1) % 5000;
    player.frame++;

    world.clouds.forEach(c => { c.x += c.speed; if (c.x > W+c.w) c.x = -c.w; });

    const nightI = Math.max(0, Math.sin(world.time * 0.001257) * 0.75);
    if (nightI > 0.35 && Math.random() < 0.008) spawnEnemy();
    if (nightI < 0.15) world.enemies = [];
    ui.time.innerText = nightI > 0.3 ? '🌙 Noche' : '☀ Día';

    if (world.time % 120 === 0 && player.energy < player.maxEnergy && !player.dead) {
        player.energy = Math.min(player.maxEnergy, player.energy + 1);
        updateUI();
    }

    if (player.hp <= 0 && !player.dead) {
        player.dead = true; player.hp = 0;
        showDialogue('¡Has caído! Presiona E cerca de la casa para recuperarte.');
        updateUI(); return;
    }

    if (player.dead) {
        if ((keys['KeyE']||keys['Enter']) && collides(player, {...world.objects[0], w:120, h:120})) {
            player.hp=player.maxHp; player.energy=player.maxEnergy; player.dead=false; world.time=0;
            showDialogue('Has descansado. Fuerzas recuperadas.'); updateUI();
        }
        return;
    }

    // Grow crops
    world.objects.forEach(obj => {
        if (obj.status==='seedling') { obj.timer--; if (obj.timer<=0) obj.status='ready'; }
    });

    // SPACE: sow / harvest / attack
    if (keys['Space'] && !player.attacking) {
        if (player.energy >= 5) {
            player.attacking=true; player.atkFrame=0;
            audio.playHit();
            let acted=false;
            world.objects.forEach(obj => {
                if (obj.type==='soil' && collides(player,obj)) {
                    if (obj.status==='empty') {
                        obj.status='seedling'; obj.timer=300;
                        player.energy=Math.max(0,player.energy-10); acted=true;
                        showDialogue('¡Sembrado! El maíz tardará un momento en crecer. (-10 ENG)');
                    } else if (obj.status==='ready') {
                        obj.status='empty'; player.inventory.maiz++;
                        player.energy=Math.max(0,player.energy-5); acted=true;
                        player.xp+=15; checkLevelUp();
                        if (!world.questsDone.q1 && player.inventory.maiz>=3) {
                            world.questsDone.q1=true;
                            showDialogue('¡Misión completada! Cosechaste 3 mazorcas. (+50 XP)');
                            player.xp+=50; checkLevelUp();
                        } else {
                            showDialogue(`¡Cosechado! Maíz x${player.inventory.maiz} (-5 ENG)`);
                        }
                    }
                }
            });
            if (!acted) player.energy=Math.max(0,player.energy-2);
            updateUI();
        } else {
            showDialogue('Sin energía. Descansa en casa (E).');
        }
    }

    if (player.attacking) { player.atkFrame++; if (player.atkFrame>15) player.attacking=false; }

    // Movement
    let nx=player.x, ny=player.y;
    player.moving=false;
    if (!player.attacking) {
        if      (keys['ArrowUp']   ||keys['KeyW']) { ny-=4; player.dir='up';    player.moving=true; }
        else if (keys['ArrowDown'] ||keys['KeyS']) { ny+=4; player.dir='down';  player.moving=true; }
        else if (keys['ArrowLeft'] ||keys['KeyA']) { nx-=4; player.dir='left';  player.moving=true; }
        else if (keys['ArrowRight']||keys['KeyD']) { nx+=4; player.dir='right'; player.moving=true; }
    }

    // Rest at house
    if ((keys['KeyE']||keys['Enter']) && collides(player,{...world.objects[0],w:120,h:120})) {
        player.hp=player.maxHp; player.energy=player.maxEnergy; world.time=0;
        showDialogue('Has descansado en casa. ¡Fuerzas al máximo!'); updateUI();
    }

    if (player.x > W-24) transitionToRegion('chiquimula');
    if (player.x < 4 && world.region==='chiquimula') transitionToRegion('zacapa');

    // Axis-independent collision with 'solid' only
    let bx=false, by=false;
    world.objects.forEach(obj => {
        if (obj.type!=='solid') return;
        if (collides({x:nx, y:player.y, w:player.w, h:player.h}, obj)) bx=true;
        if (collides({x:player.x, y:ny, w:player.w, h:player.h}, obj)) by=true;
    });
    player.x = bx ? player.x : Math.max(0,  Math.min(W-player.w, nx));
    player.y = by ? player.y : Math.max(48, Math.min(H-player.h, ny));

    // Enemies
    world.enemies = world.enemies.filter(enemy => {
        const dx=player.x-enemy.x, dy=player.y-enemy.y;
        const dist=Math.sqrt(dx*dx+dy*dy);
        if (dist<200 && dist>0) { enemy.x+=(dx/dist)*enemy.speed; enemy.y+=(dy/dist)*enemy.speed; }
        if (player.attacking && player.atkFrame===5 &&
            collides({x:player.x-20,y:player.y-20,w:72,h:88},enemy)) {
            player.xp+=30; checkLevelUp(); updateUI(); return false;
        }
        if (collides(player,enemy)) { player.hp=Math.max(0,player.hp-0.25); updateUI(); }
        return true;
    });

    // NPCs
    world.npcs.forEach(npc => {
        if (collides(player,npc) && ui.dialogue.classList.contains('hidden') && player.moving) {
            showDialogue(`${npc.name}: ${npc.dialogue}`);
            if (npc.id==='chepe' && player.inventory.maiz>0) {
                const earned=player.inventory.maiz*10;
                player.gold+=earned; player.xp+=player.inventory.maiz*5;
                showDialogue(`Don Chepe: Aquí tienes ${earned} de oro. ¡Buen trabajo!`);
                player.inventory.maiz=0; checkLevelUp(); updateUI();
            }
        }
    });
}

// ── DRAW ─────────────────────────────────────────────────────
function draw() {
    ctx.clearRect(0, 0, W, H);

    const nightI = Math.max(0, Math.sin(world.time * 0.001257) * 0.75);
    const r=Math.floor(135-nightI*120), g=Math.floor(195-nightI*180), b=Math.floor(235-nightI*220);
    ctx.fillStyle=`rgb(${r},${g},${b})`; ctx.fillRect(0,0,W,H);

    if (nightI>0.3) {
        ctx.fillStyle=`rgba(255,255,255,${(nightI-0.3)*1.2})`;
        for(let s=0;s<40;s++) { ctx.fillRect((s*173+11)%W,(s*97+7)%120,1,1); }
    }

    const S=assets.santiago;
    for(let tx=0;tx<W;tx+=TILE) for(let ty=60;ty<H;ty+=TILE) {
        const t=groundTile(tx/TILE,ty/TILE);
        if(t.complete&&t.naturalWidth) ctx.drawImage(t,tx,ty,TILE,TILE);
        else { ctx.fillStyle='#78583a'; ctx.fillRect(tx,ty,TILE,TILE); }
    }

    if(S.path.complete&&S.path.naturalWidth) for(let px=0;px<W;px+=TILE) ctx.drawImage(S.path,px,252,TILE,24);

    const rb=S.river_bank;
    if(rb.complete&&rb.naturalWidth) for(let rx=0;rx<W;rx+=TILE) if(rx<288||rx>=384) ctx.drawImage(rb,rx,292,TILE,16);

    world.objects.forEach(obj => {
        const x=obj.x, y=obj.y, ow=obj.w||obj.width, oh=obj.h||obj.height;
        const d=(img,dx,dy,dw,dh,fb)=>{
            if(img&&img.complete&&img.naturalWidth) ctx.drawImage(img,dx,dy,dw,dh);
            else { ctx.fillStyle=fb; ctx.fillRect(dx,dy,dw,dh); }
        };

        if(obj.type==='soil') {
            d(S.tilled_soil,x,y,48,48,'#5c3d1e');
            ctx.strokeStyle='#3b1f0a'; ctx.lineWidth=2; ctx.strokeRect(x+3,y+3,42,42);
            if(obj.status==='seedling') {
                const img=obj.timer>150?S.corn_seedling:S.corn_mid;
                if(img&&img.complete&&img.naturalWidth) ctx.drawImage(img,x,y,48,48);
                else { ctx.fillStyle='#16a34a'; ctx.fillRect(x+20,y+28,8,16); }
            } else if(obj.status==='ready') d(S.corn,x,y,48,48,'#eab308');

        } else if(obj.type==='house') {
            d(S.house_wall,x,   y+48,48,48,'#a16207');
            d(S.house_door,x+48,y+48,48,48,'#92400e');
            d(S.house_roof,x,   y,   96,48,'#b91c1c');

        } else if(obj.type==='bridge') {
            ctx.fillStyle='#92400e'; ctx.fillRect(x,y,ow,oh);
            ctx.strokeStyle='#78350f'; ctx.lineWidth=2;
            for(let bx=x+8;bx<x+ow-4;bx+=16) { ctx.beginPath(); ctx.moveTo(bx,y); ctx.lineTo(bx,y+oh); ctx.stroke(); }

        } else if(obj.type==='solid') {
            for(let rx=x;rx<x+ow;rx+=TILE) d(S.water,rx,y,TILE,oh,'#1d4ed8');
        }
    });

    world.decorations.forEach(dec => {
        if(dec.type==='shop') {
            if(S.house_roof.complete&&S.house_roof.naturalWidth) ctx.drawImage(S.house_roof,dec.x,dec.y-24,64,32);
            ctx.fillStyle='#d97706'; ctx.fillRect(dec.x+6,dec.y+8,52,22);
            ctx.fillStyle='#1c1917'; ctx.font='6px monospace'; ctx.fillText('TIENDA',dec.x+10,dec.y+22);
        } else {
            const img=S[dec.type];
            if(img&&img.complete&&img.naturalWidth) ctx.drawImage(img,dec.x,dec.y,48,48);
            else { ctx.fillStyle='#15803d'; ctx.fillRect(dec.x+16,dec.y+8,16,36); }
        }
    });

    world.npcs.forEach(npc => {
        ctx.fillStyle=npc.color; ctx.fillRect(npc.x+8,npc.y+12,16,32);
        ctx.fillStyle='#fcd34d'; ctx.fillRect(npc.x+10,npc.y+4,12,12);
        ctx.fillStyle='rgba(0,0,0,0.5)'; ctx.fillRect(npc.x-4,npc.y-12,40,10);
        ctx.fillStyle='#fff'; ctx.font='5px monospace'; ctx.fillText(npc.name,npc.x-2,npc.y-4);
    });

    world.enemies.forEach(e => {
        ctx.save(); ctx.globalAlpha=0.75; ctx.fillStyle='#1e1b4b';
        ctx.beginPath(); ctx.ellipse(e.x+12,e.y+16,12,16,0,0,Math.PI*2); ctx.fill();
        ctx.fillStyle='#ef4444'; ctx.fillRect(e.x+6,e.y+10,3,3); ctx.fillRect(e.x+15,e.y+10,3,3);
        ctx.restore();
    });

    if(assets.ready) {
        const frame=player.moving?Math.floor(player.frame/10)%3:0;
        const spr=assets.oscar[player.dir][frame];
        if(spr&&spr.complete&&spr.naturalWidth) ctx.drawImage(spr,player.x-8,player.y,48,48);
    } else {
        ctx.fillStyle='#f97316'; ctx.fillRect(player.x+4,player.y+12,24,32);
        ctx.fillStyle='#fcd34d'; ctx.fillRect(player.x+8,player.y+4,16,12);
        const pct=Math.floor((assets.loaded/assets.total)*100);
        ctx.fillStyle='rgba(0,0,0,0.7)'; ctx.fillRect(W/2-80,H/2-20,160,40);
        ctx.fillStyle='#fbbf24'; ctx.font='8px monospace';
        ctx.fillText(`Cargando... ${pct}%`,W/2-55,H/2+4);
    }

    if(player.attacking&&player.atkFrame<8) {
        ctx.save(); ctx.globalAlpha=0.3; ctx.fillStyle='#fbbf24';
        ctx.beginPath(); ctx.arc(player.x+16,player.y+24,36,0,Math.PI*2); ctx.fill(); ctx.restore();
    }

    ctx.save(); ctx.globalAlpha=0.25; ctx.fillStyle='#fff';
    world.clouds.forEach(c => { ctx.beginPath(); ctx.ellipse(c.x,c.y,c.w/2,c.h/2,0,0,Math.PI*2); ctx.fill(); });
    ctx.restore();

    if(nightI>0) { ctx.fillStyle=`rgba(10,15,40,${nightI*0.8})`; ctx.fillRect(0,0,W,H); }
}

// ── MAIN LOOP ─────────────────────────────────────────────────
audio.init();
function loop() { update(); draw(); requestAnimationFrame(loop); }
loop();

// ── START MENU JS ─────────────────────────────────────────────
const SM_DEFS = [
    { action:'nueva',    file:'btn_nueva_partida'  },
    { action:'continuar',file:'btn_continuar'      },
    { action:'cargar',   file:'btn_cargar_partida' },
    { action:'config',   file:'btn_configuracion'  },
    { action:'creditos', file:'btn_creditos'       },
    { action:'salir',    file:'btn_salir'          },
];
let smIdx = 0;

function smSetIdx(i) {
    document.querySelectorAll('.sm-btn img').forEach((img, j) => {
        const state = j===i ? 'selected' : 'normal';
        img.src = UI_PATH + `start_menu/buttons_${state}/${SM_DEFS[j].file}.png`;
    });
    smIdx = i;
    // Position diamond: 48px btn + 5px gap = 53px per slot; diamond ~42px tall at 28px wide
    const diamond = document.getElementById('sm-diamond');
    diamond.style.top = (i * 53 + 3) + 'px';
}

async function smActivate() {
    const action = SM_DEFS[smIdx].action;
    if (action === 'nueva') {
        await startNewGame();
        enterGame();
        showDialogue('¡Partida Nueva! Habla con Marisol para empezar.');
    } else if (action === 'continuar' || action === 'cargar') {
        await loadGame();
        enterGame();
    } else if (action === 'config' || action === 'creditos') {
        showDialogue('Próximamente...');
        enterGame();
    } else if (action === 'salir') {
        showDialogue('Cierra la pestaña para salir del juego.');
        enterGame();
    }
}

function enterGame() {
    gameState = 'playing';
    document.getElementById('start-menu').classList.add('hidden');
}

// Init start menu buttons
document.querySelectorAll('.sm-btn').forEach((btn, i) => {
    btn.addEventListener('mouseenter', () => {
        if (i !== smIdx) {
            btn.querySelector('img').src = UI_PATH + `start_menu/buttons_hover/${SM_DEFS[i].file}.png`;
        }
    });
    btn.addEventListener('mouseleave', () => {
        const state = i===smIdx ? 'selected' : 'normal';
        btn.querySelector('img').src = UI_PATH + `start_menu/buttons_${state}/${SM_DEFS[i].file}.png`;
    });
    btn.addEventListener('click', () => { smSetIdx(i); smActivate(); });
});
smSetIdx(0);

// ── PAUSE MENU JS ─────────────────────────────────────────────
const PM_DEFS = [
    { action:'reanudar',  file:'btn_reanudar'           },
    { action:'guardar',   file:'btn_guardar'            },
    { action:'inventario',file:'btn_inventario'         },
    { action:'misiones',  file:'btn_misiones'           },
    { action:'mapa',      file:'btn_mapa'               },
    { action:'habilidades',file:'btn_habilidades'       },
    { action:'config',    file:'btn_configuracion'      },
    { action:'salir_menu',file:'btn_salir_menu_principal'},
];
let pmIdx = 0;

function pmSetIdx(i) {
    document.querySelectorAll('.pm-btn img').forEach((img, j) => {
        const state = j===i ? 'selected' : 'normal';
        img.src = UI_PATH + `pause_menu/buttons_${state}/${PM_DEFS[j].file}.png`;
    });
    pmIdx = i;
    // Position diamond: 42px btn + 2px gap = 44px/slot; diamond ~30px tall; padding-top 42px
    const diamond = document.getElementById('pm-diamond');
    diamond.style.top = (48 + i * 44) + 'px';
}

function pmActivate() {
    const action = PM_DEFS[pmIdx].action;
    if (action === 'reanudar') {
        closePauseMenu();
    } else if (action === 'guardar') {
        closePauseMenu();
        setTimeout(() => saveGame(), 200);
    } else if (action === 'salir_menu') {
        closePauseMenu();
        gameState = 'start';
        document.getElementById('start-menu').classList.remove('hidden');
    } else if (['inventario','mapa','habilidades','config'].includes(action)) {
        showDialogue('Función próximamente disponible.');
        closePauseMenu();
    } else if (action === 'misiones') {
        showDialogue(
            (world.questsDone.q1 ? '✓' : '◦') + ' Cosecha 3 mazorcas\n' +
            (world.questsDone.q2 ? '✓' : '◦') + ' Viaja a Chiquimula'
        );
        closePauseMenu();
    }
}

function updatePauseStats() {
    document.getElementById('pm-level').innerText   = 'Nivel ' + player.level;
    document.getElementById('pm-hp').innerText      = `HP: ${Math.floor(player.hp)}/${player.maxHp}`;
    document.getElementById('pm-eng').innerText     = `ENG: ${Math.floor(player.energy)}/${player.maxEnergy}`;
    document.getElementById('pm-xp').innerText      = `XP: ${player.xp}/${player.nextLevelXp}`;
    document.getElementById('pm-gold').innerText    = `Oro: ${player.gold}`;
    document.getElementById('pm-hp-fill').style.width  = `${player.hp/player.maxHp*100}%`;
    document.getElementById('pm-eng-fill').style.width = `${player.energy/player.maxEnergy*100}%`;
    document.getElementById('pm-xp-fill').style.width  = `${Math.min(100,player.xp/player.nextLevelXp*100)}%`;
    const q1=world.questsDone.q1, q2=world.questsDone.q2, m=player.inventory.maiz;
    document.getElementById('pm-q1').style.cssText   = q1 ? 'text-decoration:line-through;color:#4ade80' : 'color:#e2e8f0';
    document.getElementById('pm-q1').innerText       = (q1?'✓':'◦')+' Cosecha 3 mazorcas'+(q1?'':` (${m}/3)`);
    document.getElementById('pm-q2').style.color     = q2 ? '#4ade80' : '#6b7280';
    document.getElementById('pm-q2').innerText       = (q2?'✓':'◦')+' Viaja a Chiquimula';
}

function openPauseMenu() {
    gameState = 'paused';
    updatePauseStats();
    pmSetIdx(0);
    document.getElementById('pause-menu').classList.add('active');
}
function closePauseMenu() {
    gameState = 'playing';
    document.getElementById('pause-menu').classList.remove('active');
}

// Init pause menu buttons
document.querySelectorAll('.pm-btn').forEach((btn, i) => {
    btn.addEventListener('mouseenter', () => pmSetIdx(i));
    btn.addEventListener('click',      () => { pmSetIdx(i); pmActivate(); });
});
pmSetIdx(0);

// Expose for HTML
window.saveGame       = saveGame;
window.openPauseMenu  = openPauseMenu;
window.closeDialogue  = closeDialogue;
</script>
</body>
</html>
