<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
$host='127.0.0.1';$db='qanil_rpg';$user='root';$pass='';$charset='utf8mb4';
$db_status="Pendiente";
try{$pdo=new PDO("mysql:host=$host;dbname=$db;charset=$charset",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);$db_status="Conectado";}catch(\PDOException $e){$db_status="Error: ".$e->getMessage();}
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Crónicas de Q'anil</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
*{box-sizing:border-box}
body{font-family:'Press Start 2P',cursive;background:#050200;color:#e2e8f0;margin:0;overflow:hidden}
.pixel{image-rendering:pixelated}
#fade-overlay{pointer-events:none;transition:opacity 1s}

/* ── START MENU ─────────────────── */
#start-menu{position:fixed;inset:0;z-index:200;background:radial-gradient(ellipse at 50% 60%,#1c0900,#050200);display:flex;align-items:center;justify-content:center;transition:opacity .4s}
#start-menu.hidden{opacity:0;pointer-events:none}
.sm-wrap{position:relative;display:flex;flex-direction:column;align-items:center}
.sm-logo{width:380px;margin-bottom:-24px;position:relative;z-index:2;filter:drop-shadow(0 0 18px rgba(200,150,0,.55))}
.sm-panel-wrap{position:relative;width:420px}
.sm-panel-bg{width:420px;display:block}
.sm-panel-body{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;padding-top:52px}
.sm-subtitle{width:290px;margin-bottom:6px}
.sm-divider{width:90px;margin-bottom:12px}
.sm-btn-list{position:relative;display:flex;flex-direction:column;gap:5px}
#sm-diamond{position:absolute;width:28px;right:calc(100% + 8px);top:0;transition:top .1s ease;filter:drop-shadow(0 0 5px #4ade80)}
.sm-btn{cursor:pointer;height:48px;display:flex;align-items:center}
.sm-btn img{height:42px;width:auto;transition:filter .1s}
.sm-btn:hover img{filter:brightness(1.12) drop-shadow(0 0 4px #fbbf24)}
.sm-ornament-bot{width:200px;margin-top:8px;opacity:.9}
.sm-colgante{position:absolute;width:58px;top:130px;opacity:.85}
.sm-colgante.left{left:-52px}.sm-colgante.right{right:-52px;transform:scaleX(-1)}
.sm-plaque{width:230px;margin-top:10px;opacity:.75}

/* ── CREDITS OVERLAY ────────────── */
#creditos{position:fixed;inset:0;z-index:210;background:rgba(5,2,0,.96);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .3s}
#creditos.active{opacity:1;pointer-events:all}
.cred-box{max-width:440px;padding:28px;text-align:center;line-height:2.2;font-size:7.5px}

/* ── PAUSE MENU ─────────────────── */
#pause-menu{position:fixed;inset:0;z-index:150;background:rgba(5,2,0,.85);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .25s}
#pause-menu.active{opacity:1;pointer-events:all}
.pm-wrap{display:flex;flex-direction:column;align-items:center;gap:10px}
.pm-header{width:340px}
.pm-cols{display:flex;gap:14px;align-items:flex-start}
.pm-left{position:relative;width:270px}
.pm-left-bg{width:230px;display:block;margin-left:40px}
.pm-btn-overlay{position:absolute;top:0;left:40px;right:0;bottom:0;display:flex;flex-direction:column;align-items:center;padding-top:42px;gap:2px}
#pm-diamond{position:absolute;left:-14px;width:20px;top:42px;transition:top .1s ease;filter:drop-shadow(0 0 4px #4ade80)}
.pm-btn{cursor:pointer;height:42px;display:flex;align-items:center}
.pm-btn img{height:36px;width:auto;transition:filter .1s}
.pm-btn:hover img{filter:brightness(1.12) drop-shadow(0 0 4px #fbbf24)}
.pm-right{display:flex;flex-direction:column;gap:8px;width:272px}
.pm-panel{position:relative}
.pm-panel-bg{width:272px;display:block}
.pm-panel-content{position:absolute;inset:0;padding:13px 15px;font-size:6.5px;line-height:1.55;color:#e2e8f0;overflow:hidden}
.pm-bar{height:5px;background:#111;border:1px solid #374151;margin-bottom:5px}
.pm-bar-fill{height:100%;transition:width .3s}
.cfg-btn{border:1px solid #4b5563;background:rgba(0,0,0,.4);color:#e2e8f0;padding:3px 8px;cursor:pointer;font-family:inherit;font-size:6px;margin-top:3px}
.cfg-btn:hover{border-color:#fbbf24;color:#fbbf24}

/* ── GAME ───────────────────────── */
#game-wrap{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;padding:12px}
.bar-container{width:90px;height:6px;background:#111827;border:1px solid #374151;margin-top:3px}
.bar-fill{height:100%;transition:width .3s}
</style>
</head>
<body>

<div id="fade-overlay" class="fixed inset-0 bg-black opacity-0 z-50"></div>

<!-- START MENU -->
<div id="start-menu">
 <div class="sm-wrap">
  <img class="sm-logo pixel" src="ui_qanil_elementos_recortados/start_menu/logo/logo_cronicas_de_qanil.png">
  <div class="sm-panel-wrap">
   <img class="sm-panel-bg pixel" src="ui_qanil_elementos_recortados/start_menu/panels/panel_principal_pergamino.png">
   <div class="sm-panel-body">
    <img class="sm-subtitle pixel" src="ui_qanil_elementos_recortados/start_menu/logo/subtitle_el_ascenso_de_oscar_leon.png">
    <img class="sm-divider pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/linea_divisoria_jade.png">
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

<!-- CRÉDITOS -->
<div id="creditos">
 <div class="cred-box">
  <img class="pixel" src="ui_qanil_elementos_recortados/start_menu/logo/logo_cronicas_de_qanil.png" style="width:220px;margin-bottom:14px;filter:drop-shadow(0 0 12px rgba(200,150,0,.5))">
  <div style="color:#fbbf24;font-size:9px;margin-bottom:4px">Crónicas de Q'anil</div>
  <div style="color:#a78bfa;margin-bottom:14px">El Ascenso de Oscar León</div>
  <img class="pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/linea_divisoria_jade.png" style="width:80px;margin-bottom:12px">
  <div style="color:#e2e8f0">Desarrollo &amp; Diseño</div>
  <div style="color:#4ade80;margin-bottom:8px">Oscar León</div>
  <div style="color:#e2e8f0">Arte &amp; Sprites</div>
  <div style="color:#4ade80;margin-bottom:8px">Q'anil Art Pack — Guatemala</div>
  <div style="color:#e2e8f0">Música</div>
  <div style="color:#4ade80;margin-bottom:8px">Ambient · Oriente Guatemalteco</div>
  <div style="color:#e2e8f0">Región</div>
  <div style="color:#4ade80;margin-bottom:14px">Santiago Guálán, Zacapa, Guatemala</div>
  <img class="pixel" src="ui_qanil_elementos_recortados/start_menu/plaques/rotulo_santiago_gualan_zacapa.png" style="width:220px;margin-bottom:14px;opacity:.8">
  <div style="color:#6b7280;font-size:6.5px">Versión 0.2 Alpha · 2024</div>
  <button onclick="closeCreditos()" class="cfg-btn" style="margin-top:16px;font-size:8px;padding:6px 16px;border-color:#fbbf24;color:#fbbf24">◀ Regresar</button>
 </div>
</div>

<!-- PAUSE MENU -->
<div id="pause-menu">
 <div class="pm-wrap">
  <img class="pm-header pixel" src="ui_qanil_elementos_recortados/pause_menu/header/header_juego_en_pausa.png">
  <div class="pm-cols">
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
   <div class="pm-right">
    <!-- DEFAULT: personaje + objetivos -->
    <div id="pmv-default">
     <div class="pm-panel">
      <img class="pm-panel-bg pixel" src="ui_qanil_elementos_recortados/pause_menu/panels/panel_estado_personaje.png">
      <div class="pm-panel-content">
       <div style="display:flex;gap:8px;align-items:flex-start;margin-bottom:8px">
        <img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/icons/retrato_oscar.png" style="width:58px">
        <div><div style="color:#fbbf24;margin-bottom:2px">Oscar León</div><div id="pm-level" style="color:#a78bfa">Nivel 1</div><div id="pm-gold" style="color:#fbbf24;margin-top:3px">Oro: 0</div></div>
       </div>
       <div id="pm-hp" style="color:#f87171;margin-bottom:1px">HP: 100/100</div>
       <div class="pm-bar"><div id="pm-hp-fill" class="pm-bar-fill" style="background:#ef4444;width:100%"></div></div>
       <div id="pm-eng" style="color:#60a5fa;margin-bottom:1px">ENG: 80/80</div>
       <div class="pm-bar"><div id="pm-eng-fill" class="pm-bar-fill" style="background:#3b82f6;width:100%"></div></div>
       <div id="pm-xp" style="color:#a78bfa;margin-bottom:1px">XP: 0/100</div>
       <div class="pm-bar"><div id="pm-xp-fill" class="pm-bar-fill" style="background:#8b5cf6;width:0%"></div></div>
       <div id="pm-kills" style="color:#f87171;margin-top:3px">Enemigos: 0</div>
      </div>
     </div>
     <div class="pm-panel" style="margin-top:8px">
      <img class="pm-panel-bg pixel" src="ui_qanil_elementos_recortados/pause_menu/panels/panel_objetivo_actual.png">
      <div class="pm-panel-content" id="pm-quests-default"></div>
     </div>
     <img class="pixel" src="ui_qanil_elementos_recortados/pause_menu/plaques/placa_version_hecho_en_guatemala.png" style="width:240px;opacity:.7;margin-top:8px">
    </div>
    <!-- INVENTARIO -->
    <div id="pmv-inventario" style="display:none">
     <div class="pm-panel">
      <img class="pm-panel-bg pixel" src="ui_qanil_elementos_recortados/pause_menu/panels/panel_estado_personaje.png">
      <div class="pm-panel-content">
       <div style="color:#4ade80;margin-bottom:8px;font-size:8px">INVENTARIO</div>
       <img class="pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/linea_divisoria_jade.png" style="width:80px;margin-bottom:8px">
       <div id="pm-inv-content" style="line-height:2"></div>
      </div>
     </div>
    </div>
    <!-- MISIONES -->
    <div id="pmv-misiones" style="display:none">
     <div class="pm-panel">
      <img class="pm-panel-bg pixel" src="ui_qanil_elementos_recortados/pause_menu/panels/panel_estado_personaje.png">
      <div class="pm-panel-content">
       <div style="color:#fbbf24;margin-bottom:8px;font-size:8px">MISIONES</div>
       <img class="pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/linea_divisoria_jade.png" style="width:80px;margin-bottom:8px">
       <div id="pm-mis-content" style="line-height:2.2"></div>
      </div>
     </div>
    </div>
    <!-- MAPA -->
    <div id="pmv-mapa" style="display:none">
     <div class="pm-panel">
      <img class="pm-panel-bg pixel" src="ui_qanil_elementos_recortados/pause_menu/panels/panel_estado_personaje.png">
      <div class="pm-panel-content">
       <div style="color:#60a5fa;margin-bottom:8px;font-size:8px">MAPA</div>
       <img class="pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/linea_divisoria_jade.png" style="width:80px;margin-bottom:8px">
       <canvas id="minimap" width="230" height="130" style="border:1px solid #374151;display:block"></canvas>
      </div>
     </div>
    </div>
    <!-- HABILIDADES -->
    <div id="pmv-habilidades" style="display:none">
     <div class="pm-panel">
      <img class="pm-panel-bg pixel" src="ui_qanil_elementos_recortados/pause_menu/panels/panel_estado_personaje.png">
      <div class="pm-panel-content">
       <div style="color:#a78bfa;margin-bottom:8px;font-size:8px">HABILIDADES</div>
       <img class="pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/linea_divisoria_jade.png" style="width:80px;margin-bottom:8px">
       <div id="pm-hab-content" style="line-height:2.2"></div>
      </div>
     </div>
    </div>
    <!-- CONFIGURACIÓN -->
    <div id="pmv-config" style="display:none">
     <div class="pm-panel">
      <img class="pm-panel-bg pixel" src="ui_qanil_elementos_recortados/pause_menu/panels/panel_estado_personaje.png">
      <div class="pm-panel-content">
       <div style="color:#fbbf24;margin-bottom:8px;font-size:8px">CONFIG</div>
       <img class="pixel" src="ui_qanil_elementos_recortados/start_menu/ornaments/linea_divisoria_jade.png" style="width:80px;margin-bottom:10px">
       <div style="margin-bottom:8px">🎵 Música: <span id="cfg-music" style="color:#4ade80">OFF</span></div>
       <button class="cfg-btn" onclick="toggleMusic()">Alternar Música</button>
       <div style="margin:10px 0 8px">🔊 Efectos: <span id="cfg-sfx" style="color:#4ade80">ON</span></div>
       <button class="cfg-btn" onclick="toggleSFX()">Alternar Efectos</button>
       <div style="margin:10px 0 8px">⚡ Vel. Juego:</div>
       <button class="cfg-btn" onclick="cycleSpeed()">Normal <span id="cfg-speed">1x</span></button>
      </div>
     </div>
    </div>
   </div>
  </div>
 </div>
</div>

<!-- GAME -->
<div id="game-wrap">
 <div class="text-center mb-3">
  <h1 class="text-xl text-yellow-400 mb-1">Crónicas de Q'anil</h1>
  <h2 class="text-xs text-green-400">El Ascenso de Oscar León</h2>
 </div>
 <div class="bg-gray-800 border-4 border-gray-600 p-4 rounded-lg max-w-5xl w-full flex flex-col md:flex-row gap-4 shadow-2xl">
  <div class="flex-1">
   <div class="mb-2 text-xs text-gray-400 flex justify-between items-center">
    <span>Motor: <span class="text-green-500">Activo</span> | DB: <span class="text-blue-400"><?php echo htmlspecialchars($db_status);?></span></span>
    <span id="player-gold" class="text-yellow-400 font-bold">Oro: 0</span>
   </div>
   <div class="w-full bg-black border-2 border-gray-700 relative overflow-hidden mb-3 rounded shadow-inner" style="height:380px">
    <canvas id="gameCanvas" width="672" height="380" class="pixel absolute inset-0 w-full h-full"></canvas>
    <div class="absolute inset-0 p-3 z-10 flex flex-col justify-between pointer-events-none">
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
      <p id="dialogue-text" class="leading-relaxed whitespace-pre-line"></p>
      <div class="mt-2 flex justify-end">
       <button onclick="closeDialogue()" class="text-yellow-400 hover:text-yellow-300 text-[8px] uppercase border border-yellow-600 px-2 py-1 rounded">Cerrar [Enter] ▶</button>
      </div>
     </div>
    </div>
   </div>
   <div class="flex justify-center gap-2 flex-wrap">
    <button onclick="saveGame()" class="bg-purple-700 hover:bg-purple-600 text-white px-3 py-2 border-b-4 border-purple-900 active:border-b-0 active:translate-y-1 text-[8px] rounded transition-all">↓ Guardar</button>
    <button onclick="openPauseMenu()" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 border-b-4 border-gray-900 active:border-b-0 active:translate-y-1 text-[8px] rounded transition-all">❚❚ Pausa</button>
    <span class="text-[7px] text-gray-500 self-center">WASD · ESPACIO=Acción · E=Interactuar · M=Música · ESC=Pausa</span>
   </div>
  </div>
  <div class="w-full md:w-52 flex flex-col gap-3">
   <div class="bg-gray-900 border-2 border-gray-700 p-3 rounded shadow-lg">
    <h3 class="text-[9px] text-yellow-400 mb-2 border-b border-gray-700 pb-2">⚔ MISIONES</h3>
    <ul id="quest-list" class="text-[7px] space-y-2">
     <li id="q1" class="text-white">◦ Cosecha 3 mazorcas (0/3)</li>
     <li id="q2" class="text-gray-600">◦ Viaja a Chiquimula</li>
     <li id="q3" class="text-gray-600">◦ Gana 50 de oro (0/50)</li>
     <li id="q4" class="text-gray-600">◦ Derrota 5 enemigos (0/5)</li>
    </ul>
   </div>
   <div class="bg-gray-900 border-2 border-gray-700 p-3 flex-1 rounded shadow-lg">
    <h3 class="text-[9px] text-green-400 mb-2 border-b border-gray-700 pb-2">🎒 INVENTARIO</h3>
    <ul id="inventory-list" class="text-[7px] space-y-1"><li class="text-gray-600 italic">Vacío</li></ul>
   </div>
   <div class="bg-gray-900 border-2 border-gray-700 p-3 rounded">
    <h3 class="text-[8px] text-gray-400 mb-2 border-b border-gray-700 pb-2">CONTROLES</h3>
    <ul class="text-[6.5px] text-gray-500 space-y-1">
     <li>WASD / Flechas: Mover</li>
     <li>ESPACIO: Sembrar/Cosechar/Atacar</li>
     <li>E: Interactuar (NPC / Casa)</li>
     <li>M: Música · ESC: Pausa</li>
    </ul>
   </div>
  </div>
 </div>
</div>

<script>
// ── CONSTANTS ────────────────────────────────────────────────
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const W = canvas.width, H = canvas.height, TILE = 48;
const UI_PATH = 'ui_qanil_elementos_recortados/';

// ── GAME STATE ───────────────────────────────────────────────
let gameState = 'start'; // 'start'|'playing'|'paused'
const cfg = { musicOn: false, sfxOn: true, speed: 1 };

// ── UI REFS ──────────────────────────────────────────────────
const ui = {
    invList:    document.getElementById('inventory-list'),
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
    q1: document.getElementById('q1'), q2: document.getElementById('q2'),
    q3: document.getElementById('q3'), q4: document.getElementById('q4'),
    fade: document.getElementById('fade-overlay'),
};

// ── AUDIO ────────────────────────────────────────────────────
const audio = {
    ambient: null, hit: null,
    init() {
        this.ambient = new Audio('public/assets/audio/ambient_zacapa.mp3');
        this.ambient.loop = true;
        this.hit = new Audio('public/assets/audio/hit.mp3');
    },
    playHit() {
        if (!cfg.sfxOn) return;
        try { if (this.hit) { this.hit.currentTime = 0; this.hit.play().catch(()=>{}); } } catch(e){}
    }
};

// ── ASSETS ───────────────────────────────────────────────────
const assets = { oscar:{down:[],up:[],left:[],right:[]}, santiago:{}, loaded:0, total:0, ready:false };
function loadImg(src) {
    const img = new Image();
    img.onload  = ()=>{ assets.loaded++; if(assets.loaded>=assets.total) assets.ready=true; };
    img.onerror = ()=>{ assets.loaded++; if(assets.loaded>=assets.total) assets.ready=true; console.warn('Missing:',src); };
    img.src = src; return img;
}
['down','up','left','right'].forEach(dir=>['01','02','03'].forEach(f=>assets.oscar[dir].push(loadImg(`public/assets/img/oscar/uniformes_3x4/oscar_leon_${dir}_${f}.png`))));
const TILES = {
    ground:'terrain/dry_soil_01.png', ground2:'terrain/dry_soil_cracked_01.png',
    path:'terrain/dust_path_01.png', packed:'terrain/packed_yard_01.png',
    tilled_soil:'agriculture/tilled_soil_01.png',
    corn_seedling:'agriculture/corn_seedling.png', corn_mid:'agriculture/corn_mid.png', corn:'agriculture/corn_grown.png',
    cactus:'vegetation/organ_cactus_01.png', bush:'vegetation/dry_bush_01.png',
    rock:'rocks_mountains/gray_boulder_01.png',
    water:'water_irrigation/motagua_water_01.png', river_bank:'water_irrigation/motagua_bank_bottom.png',
    house_wall:'buildings/adobe_wall_01.png', house_door:'buildings/adobe_wall_door_01.png',
    house_roof:'buildings/teja_roof_01.png', fence:'buildings/wooden_fence_01.png',
};
Object.keys(TILES).forEach(k=>{ assets.santiago[k]=loadImg(`public/assets/img/tiles_santiago/${TILES[k]}`); });
assets.total = 12 + Object.keys(TILES).length;

// ── SOIL STATE (persists across region transitions) ───────────
const soilState = [
    {id:'plot1',x:80, y:80, status:'empty',timer:0},
    {id:'plot2',x:128,y:80, status:'empty',timer:0},
    {id:'plot3',x:176,y:80, status:'empty',timer:0},
];
function soilAsObjects() {
    return soilState.map(s=>({...s, type:'soil', w:48, h:48}));
}
function saveSoilFromWorld(objects) {
    objects.filter(o=>o.type==='soil').forEach(obj=>{
        const s=soilState.find(s=>s.id===obj.id);
        if(s){s.status=obj.status;s.timer=obj.timer;}
    });
}

// ── PLAYER ───────────────────────────────────────────────────
const player = {
    x:300, y:180, w:32, h:48,
    hp:100, maxHp:100, energy:80, maxEnergy:80,
    level:1, xp:0, nextLevelXp:100, gold:0,
    inventory:{maiz:0},
    dir:'down', moving:false, attacking:false,
    atkFrame:0, frame:0, username:'OscarLeon', dead:false,
    killCount:0,
};

// ── REGION DATA ───────────────────────────────────────────────
function buildZacapa() {
    return {
        name:'Santiago, Zacapa',
        sky:[135,195,235],
        objects:[
            {id:'house',  x:288,y:30, type:'house', w:96,      h:96 },
            {id:'bridge', x:288,y:300,type:'bridge',w:96,      h:48 },
            {id:'river_l',x:0,  y:300,type:'solid', w:288,     h:48 },
            {id:'river_r',x:384,y:300,type:'solid', w:W-384,   h:48 },
            ...soilAsObjects(),
        ],
        decorations:[
            {x:50, y:50, type:'cactus'},{x:560,y:140,type:'cactus'},
            {x:200,y:215,type:'rock'  },{x:500,y:60, type:'rock'  },
            {x:100,y:195,type:'fence' },{x:148,y:195,type:'fence' },{x:196,y:195,type:'fence'},
            {x:30, y:140,type:'bush'  },{x:600,y:90, type:'bush'  },{x:420,y:40, type:'bush' },
            {x:480,y:200,type:'shop'  },
        ],
        npcs:[
            {id:'marisol',name:'Marisol',   x:450,y:110,w:32,h:48,color:'#f472b6',
             dialogue:'¡Hijo! Presiona E cerca de los campos para sembrar.\nCuando el maíz crezca, usa E de nuevo para cosechar.'},
            {id:'chepe',  name:'Don Chepe', x:530,y:215,w:32,h:48,color:'#fbbf24',
             dialogue:'Presiona E aquí para venderme el maíz.\nPago 10 oro por mazorca.'},
        ],
        enterX:300, enterY:180,
        exitRight:'chiquimula', exitLeft:null,
    };
}
function buildChiquimula() {
    return {
        name:'San Juan, Chiquimula',
        sky:[160,130,90],
        objects:[
            {id:'market', x:500,y:40, type:'house', w:96,h:96},
            {id:'well',   x:200,y:170,type:'solid', w:36,h:36},
            {id:'cliff_l',x:0,  y:300,type:'solid', w:140,h:80},
            {id:'cliff_r',x:540,y:300,type:'solid', w:132,h:80},
        ],
        decorations:[
            {x:30, y:40, type:'rock'  },{x:110,y:25, type:'rock'  },{x:580,y:30, type:'rock' },
            {x:380,y:220,type:'cactus'},{x:80, y:200,type:'bush'  },{x:300,y:35, type:'bush' },
            {x:460,y:180,type:'bush'  },{x:150,y:50, type:'cactus'},
        ],
        npcs:[
            {id:'mercader',name:'Mercader', x:550,y:180,w:32,h:48,color:'#34d399',
             dialogue:'¡Bienvenido a Chiquimula!\nAquí encontrarás el comercio del oriente.\nVuelve con más maíz y te daré buen precio.'},
            {id:'anciano', name:'Anciano',  x:160,y:110,w:32,h:48,color:'#94a3b8',
             dialogue:'Los Jaguarundíes aparecen de noche.\nSon rápidos. Ataca primero, presiona ESPACIO.'},
        ],
        enterX:40, enterY:200,
        exitRight:null, exitLeft:'zacapa',
    };
}

// ── WORLD ────────────────────────────────────────────────────
function groundTile(gx,gy){return((gx*7+gy*13)%8===0)?assets.santiago.ground2:assets.santiago.ground;}

let regionData = buildZacapa();
const world = {
    region:'zacapa', time:0, enemies:[], transitioning:false,
    questsDone:{q1:false,q2:false,q3:false,q4:false},
    clouds:[
        {x:10, y:30,speed:.25,w:70,h:22},
        {x:280,y:55,speed:.18,w:90,h:28},
        {x:520,y:20,speed:.12,w:60,h:18},
    ],
    get objects(){ return regionData.objects; },
    get decorations(){ return regionData.decorations; },
    get npcs(){ return regionData.npcs; },
};

// ── INPUT ────────────────────────────────────────────────────
const keys = {};
let spacePending = false;
let ePending = false;
const GAME_KEYS = new Set(['ArrowUp','ArrowDown','ArrowLeft','ArrowRight','KeyW','KeyA','KeyS','KeyD','Space','KeyE']);

window.addEventListener('keydown', e => {
    if (e.code === 'Escape') {
        e.preventDefault();
        if      (gameState==='playing') openPauseMenu();
        else if (gameState==='paused')  closePauseMenu();
        return;
    }
    if (e.code === 'Enter' || e.code === 'NumpadEnter') {
        if (!ui.dialogue.classList.contains('hidden')) { closeDialogue(); e.preventDefault(); return; }
    }
    if (gameState === 'start') {
        e.preventDefault();
        if (e.code==='ArrowDown'||e.code==='KeyS') smSetIdx((smIdx+1)%SM_DEFS.length);
        else if (e.code==='ArrowUp'||e.code==='KeyW') smSetIdx((smIdx-1+SM_DEFS.length)%SM_DEFS.length);
        else if (e.code==='Enter'||e.code==='Space') smActivate();
        return;
    }
    if (gameState === 'paused') {
        e.preventDefault();
        if (e.code==='ArrowDown'||e.code==='KeyS') pmSetIdx((pmIdx+1)%PM_DEFS.length);
        else if (e.code==='ArrowUp'||e.code==='KeyW') pmSetIdx((pmIdx-1+PM_DEFS.length)%PM_DEFS.length);
        else if (e.code==='Enter'||e.code==='Space') pmActivate();
        return;
    }
    if (e.code==='Space' && !keys['Space']) spacePending = true;
    if (e.code==='KeyE'  && !keys['KeyE'])  ePending     = true;
    keys[e.code] = true;
    if (GAME_KEYS.has(e.code)) e.preventDefault();
    if (e.code==='KeyM') toggleMusic();
});
window.addEventListener('keyup', e => { keys[e.code]=false; });

// ── HELPERS ──────────────────────────────────────────────────
function collides(a,b) {
    return a.x<b.x+(b.w||b.width)&&a.x+(a.w||a.width)>b.x&&a.y<b.y+(b.h||b.height)&&a.y+(a.h||a.height)>b.y;
}
function nearEnough(a,b,dist=80) {
    const ax=a.x+(a.w||32)/2, ay=a.y+(a.h||48)/2;
    const bx=b.x+(b.w||48)/2, by=b.y+(b.h||48)/2;
    return Math.sqrt((ax-bx)**2+(ay-by)**2)<dist;
}
function showDialogue(text){ ui.dialogText.innerText=text; ui.dialogue.classList.remove('hidden'); }
function closeDialogue()   { ui.dialogue.classList.add('hidden'); }

function getAttackBox() {
    const ax=player.x+8, ay=player.y+16, s=44;
    return {
        down:  {x:ax-4, y:ay+18,w:s,h:s},
        up:    {x:ax-4, y:ay-s,  w:s,h:s},
        left:  {x:ax-s, y:ay-4,  w:s,h:s},
        right: {x:ax+20,y:ay-4,  w:s,h:s},
    }[player.dir];
}

function drawPrompt(x,y,label) {
    ctx.save();
    const mw=ctx.measureText(label).width;
    const pw=mw+14, ph=14;
    ctx.fillStyle='rgba(0,0,0,.75)'; ctx.fillRect(x-pw/2,y-ph,pw,ph);
    ctx.strokeStyle='#fbbf24'; ctx.lineWidth=1; ctx.strokeRect(x-pw/2,y-ph,pw,ph);
    ctx.fillStyle='#fbbf24'; ctx.font='7px monospace'; ctx.textAlign='center';
    ctx.fillText(label,x,y-3);
    ctx.restore();
}

// ── LEVEL UP ─────────────────────────────────────────────────
function checkLevelUp() {
    while (player.xp >= player.nextLevelXp) {
        player.xp -= player.nextLevelXp;
        player.level++;
        player.nextLevelXp = Math.floor(player.nextLevelXp*1.6);
        player.maxHp+=20; player.maxEnergy+=10;
        player.hp=player.maxHp; player.energy=player.maxEnergy;
        showDialogue(`¡NIVEL ${player.level}! HP y Energía al máximo.\nSiguiente nivel: ${player.nextLevelXp} XP`);
    }
}

// ── UI UPDATE ─────────────────────────────────────────────────
function updateUI() {
    ui.hpText.innerText  = `Lvl ${player.level} | HP: ${Math.floor(player.hp)}/${player.maxHp}`;
    ui.engText.innerText = `ENG: ${Math.floor(player.energy)}/${player.maxEnergy}`;
    ui.xpText.innerText  = `XP: ${player.xp}/${player.nextLevelXp}`;
    ui.hpFill.style.width  = `${player.hp/player.maxHp*100}%`;
    ui.engFill.style.width = `${player.energy/player.maxEnergy*100}%`;
    ui.xpFill.style.width  = `${Math.min(100,player.xp/player.nextLevelXp*100)}%`;
    ui.gold.innerText = `Oro: ${player.gold}`;
    const m=player.inventory.maiz;
    ui.invList.innerHTML = m>0 ? `<li class="text-green-400">🌽 Maíz x${m}</li>` : '<li class="text-gray-600 italic">Vacío</li>';
    const k=player.killCount, g=player.gold;
    const qd=world.questsDone;
    const qCls=(done)=>done?'text-green-500 line-through':'text-white';
    const qClsI=(done)=>done?'text-green-500 line-through':'text-gray-600';
    ui.q1.className=qCls(qd.q1); ui.q1.innerText=qd.q1?'✓ Cosecha 3 mazorcas':`◦ Cosecha 3 mazorcas (${m}/3)`;
    ui.q2.className=qClsI(qd.q2); ui.q2.innerText=qd.q2?'✓ Viaja a Chiquimula':'◦ Viaja a Chiquimula';
    ui.q3.className=qClsI(qd.q3); ui.q3.innerText=qd.q3?'✓ Gana 50 de oro':`◦ Gana 50 de oro (${Math.min(g,50)}/50)`;
    ui.q4.className=qClsI(qd.q4); ui.q4.innerText=qd.q4?'✓ Derrota 5 enemigos':`◦ Derrota 5 enemigos (${Math.min(k,5)}/5)`;
}

// ── CONFIG TOGGLES ────────────────────────────────────────────
function toggleMusic() {
    cfg.musicOn = !cfg.musicOn;
    if (cfg.musicOn) audio.ambient&&audio.ambient.play().catch(()=>{});
    else audio.ambient&&audio.ambient.pause();
    const el=document.getElementById('cfg-music');
    if(el) el.innerText=cfg.musicOn?'ON':'OFF';
    el&&(el.style.color=cfg.musicOn?'#4ade80':'#ef4444');
}
function toggleSFX() {
    cfg.sfxOn = !cfg.sfxOn;
    const el=document.getElementById('cfg-sfx');
    if(el){el.innerText=cfg.sfxOn?'ON':'OFF'; el.style.color=cfg.sfxOn?'#4ade80':'#ef4444';}
}
const SPEEDS=[1,1.5,2,0.5];
let speedIdx=0;
function cycleSpeed() {
    speedIdx=(speedIdx+1)%SPEEDS.length; cfg.speed=SPEEDS[speedIdx];
    const el=document.getElementById('cfg-speed');
    if(el) el.innerText=cfg.speed+'x';
}

// ── SAVE / LOAD ───────────────────────────────────────────────
async function saveGame() {
    try {
        const res = await fetch('api/save_game.php',{
            method:'POST',headers:{'Content-Type':'application/json'},
            body:JSON.stringify({username:player.username,level:player.level,xp:player.xp,
                hp:player.hp,energy:player.energy,gold:player.gold,
                inventory:player.inventory,region:world.region}),
        });
        const data = await res.json();
        showDialogue(data.status==='success'?'¡Progreso Guardado!':'Error: '+data.message);
    } catch(e){showDialogue('Error de conexión al guardar.');}
}

async function loadGame() {
    try {
        const data = await (await fetch('api/load_game.php')).json();
        if (data.status==='success') {
            const p=data.player;
            player.level      = parseInt(p.level)     ||1;
            player.xp         = parseInt(p.xp)        ||0;
            player.hp         = parseInt(p.hp)        ||100;
            player.maxHp      = parseInt(p.max_hp)    ||100;
            player.energy     = parseInt(p.energy)    ||80;
            player.maxEnergy  = parseInt(p.max_energy)||80;
            player.gold       = parseInt(p.gold)      ||0;
            player.nextLevelXp= Math.floor(100*Math.pow(1.6,player.level-1));
            player.dead=false;
            loadRegion(p.current_region||'zacapa');
            updateUI();
            return true;
        }
    } catch(e){}
    return false;
}

async function startNewGame() {
    player.level=1;player.xp=0;player.hp=100;player.maxHp=100;
    player.energy=80;player.maxEnergy=80;player.gold=0;player.killCount=0;
    player.inventory={maiz:0};player.nextLevelXp=100;player.dead=false;
    soilState.forEach(s=>{s.status='empty';s.timer=0;});
    world.questsDone={q1:false,q2:false,q3:false,q4:false};
    world.enemies=[];
    loadRegion('zacapa');
    updateUI();
    try { await fetch('api/save_game.php',{method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({username:player.username,level:1,xp:0,hp:100,energy:80,gold:0,inventory:{maiz:0},region:'zacapa'})}); } catch(e){}
}

// ── REGION LOAD ───────────────────────────────────────────────
function loadRegion(name) {
    saveSoilFromWorld(regionData.objects);
    world.region = name;
    regionData = name==='chiquimula' ? buildChiquimula() : buildZacapa();
    ui.region.innerText = regionData.name;
    if (world.questsDone.q2===false && name==='chiquimula') {
        world.questsDone.q2=true; updateUI();
    }
    player.x=regionData.enterX; player.y=regionData.enterY;
    world.enemies=[];
}

function transitionToRegion(name) {
    if (world.transitioning||!name) return;
    world.transitioning=true;
    ui.fade.style.opacity='1';
    setTimeout(()=>{
        loadRegion(name);
        updateUI();
        setTimeout(()=>{ui.fade.style.opacity='0';world.transitioning=false;},900);
    },900);
}

// ── INTERACTION (E key) ───────────────────────────────────────
function handleInteraction() {
    if (player.dead) {
        const house=regionData.objects.find(o=>o.id==='house'||o.id==='market');
        if (house && nearEnough(player,{...house,w:140,h:140},90)) {
            player.hp=player.maxHp;player.energy=player.maxEnergy;player.dead=false;world.time=0;
            showDialogue('Te recuperaste. ¡Fuerzas al máximo!'); updateUI();
        }
        return;
    }
    // House/market: rest
    const house=regionData.objects.find(o=>o.id==='house'||o.id==='market');
    if (house && nearEnough(player,{...house,w:120,h:120},90)) {
        player.hp=player.maxHp;player.energy=player.maxEnergy;world.time=0;
        showDialogue('Has descansado. ¡Fuerzas al máximo!'); updateUI();
        return;
    }
    // Soil plots
    for (const obj of regionData.objects) {
        if (obj.type==='soil' && collides({...player,x:player.x-8,w:48,h:56},obj)) {
            if (obj.status==='empty') {
                if (player.energy<10){showDialogue('Sin energía. Descansa en casa primero.');return;}
                obj.status='seedling';obj.timer=Math.floor(300/cfg.speed);
                player.energy=Math.max(0,player.energy-10);
                showDialogue('¡Sembrado! Espera a que crezca. (-10 ENG)');
            } else if (obj.status==='seedling') {
                showDialogue(`Creciendo... ${Math.ceil(obj.timer/60)} segundos aprox.`);
            } else if (obj.status==='ready') {
                obj.status='empty';player.inventory.maiz++;
                player.energy=Math.max(0,player.energy-5);
                player.xp+=15;checkLevelUp();
                if (!world.questsDone.q1&&player.inventory.maiz>=3) {
                    world.questsDone.q1=true;player.xp+=50;checkLevelUp();
                    showDialogue('¡Misión completada!\nCosechaste 3 mazorcas. (+50 XP)');
                } else {
                    showDialogue(`¡Cosechado! Maíz x${player.inventory.maiz} (-5 ENG)`);
                }
            }
            audio.playHit(); updateUI(); return;
        }
    }
    // NPCs
    for (const npc of regionData.npcs) {
        if (nearEnough(player,npc,70)) {
            if ((npc.id==='chepe'||npc.id==='mercader')&&player.inventory.maiz>0) {
                const rate=npc.id==='mercader'?12:10;
                const earned=player.inventory.maiz*rate;
                player.gold+=earned;player.xp+=player.inventory.maiz*5;
                showDialogue(`${npc.name}: Aquí tienes ${earned} de oro por ${player.inventory.maiz} mazorcas. ¡Vuelve pronto!`);
                player.inventory.maiz=0;checkLevelUp();
                if(!world.questsDone.q3&&player.gold>=50){
                    world.questsDone.q3=true;player.xp+=30;checkLevelUp();
                    showDialogue('¡Misión completada!\nGanaste 50 de oro. (+30 XP)');
                }
                updateUI();
            } else {
                showDialogue(`${npc.name}:\n${npc.dialogue}`);
            }
            return;
        }
    }
    showDialogue('No hay nada cerca.\n(Acércate a un NPC o la casa y presiona E)');
}

// ── SPAWN ENEMY ──────────────────────────────────────────────
function spawnEnemy() {
    if (world.enemies.length>=5) return;
    const sx=Math.random()<.5?-20:W+20;
    const speed=world.region==='chiquimula'?1.4+Math.random()*.6:1+Math.random()*.5;
    world.enemies.push({x:sx,y:80+Math.random()*180,w:24,h:32,speed,hp:2});
}

// ── UPDATE ────────────────────────────────────────────────────
function update() {
    if (gameState!=='playing'||world.transitioning) return;
    const dt = cfg.speed;
    world.time=(world.time+dt)%6000;
    player.frame++;
    world.clouds.forEach(c=>{c.x+=c.speed*dt;if(c.x>W+c.w)c.x=-c.w;});
    const nightI=Math.max(0,Math.sin(world.time*.001047)*.8);
    if (nightI>.3&&Math.random()<.006*dt) spawnEnemy();
    if (nightI<.1) world.enemies=[];
    ui.time.innerText=nightI>.3?'🌙 Noche':'☀ Día';
    if (world.time%120<dt&&player.energy<player.maxEnergy&&!player.dead) {
        player.energy=Math.min(player.maxEnergy,player.energy+1); updateUI();
    }
    if (player.hp<=0&&!player.dead) {
        player.dead=true;player.hp=0;
        showDialogue('¡Has caído!\nAcércate a la casa y presiona E para recuperarte.');
        updateUI(); return;
    }
    if (player.dead) { if(ePending){ePending=false;handleInteraction();} return; }

    // Grow crops
    regionData.objects.forEach(o=>{
        if(o.type==='soil'&&o.status==='seedling'){o.timer-=dt;if(o.timer<=0)o.status='ready';}
    });

    // SPACE: attack (one-shot per press)
    if (spacePending&&!player.attacking) {
        spacePending=false;
        player.attacking=true;player.atkFrame=0;
        audio.playHit();
        if(player.energy<2){showDialogue('Sin energía. Descansa en casa (E).');player.attacking=false;}
        else{player.energy=Math.max(0,player.energy-2);updateUI();}
    }
    if (player.attacking){
        player.atkFrame++;
        if (player.atkFrame===6) {
            const ab=getAttackBox();
            world.enemies=world.enemies.filter(e=>{
                if(collides(ab,e)){
                    e.hp--;
                    if(e.hp<=0){
                        player.xp+=30;player.killCount++;
                        if(!world.questsDone.q4&&player.killCount>=5){
                            world.questsDone.q4=true;player.xp+=80;checkLevelUp();
                            showDialogue('¡Misión completada!\nDerrotaste 5 enemigos. (+80 XP)');
                        }
                        checkLevelUp();updateUI();return false;
                    }
                }
                return true;
            });
        }
        if(player.atkFrame>16)player.attacking=false;
    }

    // E: interact
    if (ePending){ePending=false;handleInteraction();}

    // Movement
    let nx=player.x,ny=player.y;
    player.moving=false;
    const spd=3.5*dt;
    if(!player.attacking){
        if(keys['ArrowUp']  ||keys['KeyW']){ny-=spd;player.dir='up';   player.moving=true;}
        else if(keys['ArrowDown'] ||keys['KeyS']){ny+=spd;player.dir='down'; player.moving=true;}
        if(keys['ArrowLeft'] ||keys['KeyA']){nx-=spd;player.dir='left'; player.moving=true;}
        else if(keys['ArrowRight']||keys['KeyD']){nx+=spd;player.dir='right';player.moving=true;}
    }

    // Region exits
    if(player.x>W-20&&regionData.exitRight) transitionToRegion(regionData.exitRight);
    if(player.x<4   &&regionData.exitLeft)  transitionToRegion(regionData.exitLeft);

    // Collision
    let bx=false,by=false;
    regionData.objects.forEach(obj=>{
        if(obj.type!=='solid') return;
        if(collides({x:nx,y:player.y,w:player.w,h:player.h},obj)) bx=true;
        if(collides({x:player.x,y:ny,w:player.w,h:player.h},obj)) by=true;
    });
    player.x=bx?player.x:Math.max(0,Math.min(W-player.w,nx));
    player.y=by?player.y:Math.max(48,Math.min(H-player.h,ny));

    // Enemies
    world.enemies=world.enemies.filter(e=>{
        const dx=player.x-e.x,dy=player.y-e.y,dist=Math.sqrt(dx*dx+dy*dy);
        if(dist<220&&dist>0){e.x+=dx/dist*e.speed*dt;e.y+=dy/dist*e.speed*dt;}
        if(collides(player,e)){player.hp=Math.max(0,player.hp-.2*dt);updateUI();}
        return e.x>-50&&e.x<W+50&&e.y>0&&e.y<H+50;
    });
}

// ── DRAW ─────────────────────────────────────────────────────
function draw() {
    ctx.clearRect(0,0,W,H);
    const nightI=Math.max(0,Math.sin(world.time*.001047)*.8);
    const [sr,sg,sb]=regionData.sky;
    ctx.fillStyle=`rgb(${Math.floor(sr-nightI*120)},${Math.floor(sg-nightI*180)},${Math.floor(sb-nightI*220)})`;
    ctx.fillRect(0,0,W,H);

    // Chiquimula background mountains
    if(world.region==='chiquimula'){
        ctx.save();ctx.globalAlpha=.4;ctx.fillStyle='#6b4c20';
        ctx.beginPath();ctx.moveTo(0,150);ctx.lineTo(80,60);ctx.lineTo(160,100);ctx.lineTo(240,50);ctx.lineTo(350,90);ctx.lineTo(420,40);ctx.lineTo(500,80);ctx.lineTo(672,55);ctx.lineTo(672,150);ctx.fill();
        ctx.restore();
    }
    if(nightI>.3){ctx.fillStyle=`rgba(255,255,255,${(nightI-.3)*1.2})`;for(let s=0;s<40;s++)ctx.fillRect((s*173+11)%W,(s*97+7)%100,1,1);}

    const S=assets.santiago;
    for(let tx=0;tx<W;tx+=TILE)for(let ty=60;ty<H;ty+=TILE){
        const t=groundTile(tx/TILE,ty/TILE);
        if(t.complete&&t.naturalWidth)ctx.drawImage(t,tx,ty,TILE,TILE);
        else{ctx.fillStyle=world.region==='chiquimula'?'#8B6B3D':'#78583a';ctx.fillRect(tx,ty,TILE,TILE);}
    }
    if(world.region==='zacapa'){
        if(S.path.complete&&S.path.naturalWidth)for(let px=0;px<W;px+=TILE)ctx.drawImage(S.path,px,252,TILE,24);
        const rb=S.river_bank;
        if(rb.complete&&rb.naturalWidth)for(let rx=0;rx<W;rx+=TILE)if(rx<288||rx>=384)ctx.drawImage(rb,rx,292,TILE,16);
    }

    // Objects
    const d=(img,dx,dy,dw,dh,fb)=>{if(img&&img.complete&&img.naturalWidth)ctx.drawImage(img,dx,dy,dw,dh);else{ctx.fillStyle=fb;ctx.fillRect(dx,dy,dw,dh);}};
    regionData.objects.forEach(obj=>{
        const{x,y,w:ow,h:oh}={...obj,w:obj.w||obj.width,h:obj.h||obj.height};
        if(obj.type==='soil'){
            d(S.tilled_soil,x,y,48,48,'#5c3d1e');
            ctx.strokeStyle='#3b1f0a';ctx.lineWidth=2;ctx.strokeRect(x+3,y+3,42,42);
            if(obj.status==='seedling'){
                const img=obj.timer>150?S.corn_seedling:S.corn_mid;
                if(img&&img.complete&&img.naturalWidth)ctx.drawImage(img,x,y,48,48);
                else{ctx.fillStyle='#16a34a';ctx.fillRect(x+20,y+28,8,16);}
            }else if(obj.status==='ready'){
                d(S.corn,x,y,48,48,'#eab308');
                ctx.save();ctx.globalAlpha=.8+Math.sin(world.time*.1)*.2;ctx.fillStyle='#fbbf24';
                ctx.font='10px monospace';ctx.textAlign='center';ctx.fillText('✨',x+24,y-4);ctx.restore();
            }
        }else if(obj.type==='house'){
            d(S.house_wall,x,   y+48,48,48,'#a16207');
            d(S.house_door,x+48,y+48,48,48,'#92400e');
            d(S.house_roof,x,   y,   96,48,'#b91c1c');
            if(obj.id==='market'){
                ctx.fillStyle='#d97706';ctx.fillRect(x+6,y+52,84,38);
                ctx.fillStyle='#1c1917';ctx.font='6px monospace';ctx.textAlign='left';ctx.fillText('MERCADO',x+10,y+74);
            }
        }else if(obj.type==='bridge'){
            ctx.fillStyle='#92400e';ctx.fillRect(x,y,ow,oh);
            ctx.strokeStyle='#78350f';ctx.lineWidth=2;
            for(let bx=x+8;bx<x+ow-4;bx+=16){ctx.beginPath();ctx.moveTo(bx,y);ctx.lineTo(bx,y+oh);ctx.stroke();}
        }else if(obj.type==='solid'){
            if(obj.id==='river_l'||obj.id==='river_r'){
                for(let rx=x;rx<x+ow;rx+=TILE)d(S.water,rx,y,TILE,oh,'#1d4ed8');
            }else if(obj.id==='cliff_l'||obj.id==='cliff_r'){
                ctx.fillStyle='#5d4037';ctx.fillRect(x,y,ow,oh);
                ctx.fillStyle='#795548';ctx.fillRect(x+4,y+4,ow-8,12);
            }else if(obj.id==='well'){
                ctx.fillStyle='#5d4037';ctx.fillRect(x,y,ow,ow);
                ctx.strokeStyle='#3e2723';ctx.lineWidth=2;ctx.strokeRect(x,y,ow,ow);
                ctx.fillStyle='#37474f';ctx.beginPath();ctx.arc(x+ow/2,y+ow/2,8,0,Math.PI*2);ctx.fill();
            }
        }
    });

    // Decorations
    regionData.decorations.forEach(dec=>{
        if(dec.type==='shop'){
            if(S.house_roof.complete&&S.house_roof.naturalWidth)ctx.drawImage(S.house_roof,dec.x,dec.y-24,64,32);
            ctx.fillStyle='#d97706';ctx.fillRect(dec.x+6,dec.y+8,52,22);
            ctx.fillStyle='#1c1917';ctx.font='6px monospace';ctx.textAlign='left';ctx.fillText('TIENDA',dec.x+10,dec.y+22);
        }else{
            const img=S[dec.type];
            if(img&&img.complete&&img.naturalWidth)ctx.drawImage(img,dec.x,dec.y,48,48);
            else{ctx.fillStyle='#15803d';ctx.fillRect(dec.x+16,dec.y+8,16,36);}
        }
    });

    // NPCs
    regionData.npcs.forEach(npc=>{
        ctx.fillStyle=npc.color;ctx.fillRect(npc.x+8,npc.y+12,16,32);
        ctx.fillStyle='#fcd34d';ctx.fillRect(npc.x+10,npc.y+4,12,12);
        ctx.fillStyle='rgba(0,0,0,.5)';ctx.fillRect(npc.x-4,npc.y-12,npc.name.length*5+8,10);
        ctx.fillStyle='#fff';ctx.font='5px monospace';ctx.textAlign='left';ctx.fillText(npc.name,npc.x-2,npc.y-4);
    });

    // Enemies
    world.enemies.forEach(e=>{
        ctx.save();ctx.globalAlpha=.8;
        ctx.fillStyle=world.region==='chiquimula'?'#7c3aed':'#1e1b4b';
        ctx.beginPath();ctx.ellipse(e.x+12,e.y+16,11,15,0,0,Math.PI*2);ctx.fill();
        ctx.fillStyle='#ef4444';ctx.fillRect(e.x+5,e.y+9,3,3);ctx.fillRect(e.x+16,e.y+9,3,3);
        if(e.hp===2){ctx.fillStyle='rgba(255,0,0,.4)';ctx.fillRect(e.x,e.y-6,24,3);}
        ctx.fillStyle='rgba(255,0,0,.8)';ctx.fillRect(e.x,e.y-6,24*(e.hp===2?.5:0),3);
        ctx.restore();
    });

    // Player
    if(assets.ready){
        const frame=player.moving?Math.floor(player.frame/10)%3:0;
        const spr=assets.oscar[player.dir][frame];
        if(spr&&spr.complete&&spr.naturalWidth){
            if(player.dead){ctx.save();ctx.globalAlpha=.5;}
            ctx.drawImage(spr,player.x-8,player.y,48,48);
            if(player.dead)ctx.restore();
        }
    }else{
        ctx.fillStyle='#f97316';ctx.fillRect(player.x+4,player.y+12,24,32);
        ctx.fillStyle='#fcd34d';ctx.fillRect(player.x+8,player.y+4,16,12);
        const pct=Math.floor(assets.loaded/assets.total*100);
        ctx.fillStyle='rgba(0,0,0,.7)';ctx.fillRect(W/2-80,H/2-20,160,40);
        ctx.fillStyle='#fbbf24';ctx.font='8px monospace';ctx.textAlign='center';
        ctx.fillText(`Cargando... ${pct}%`,W/2,H/2+4);
    }

    // Attack flash
    if(player.attacking&&player.atkFrame<10){
        const ab=getAttackBox();
        ctx.save();ctx.globalAlpha=.35;ctx.fillStyle='#fbbf24';ctx.fillRect(ab.x,ab.y,ab.w,ab.h);ctx.restore();
    }

    // Interaction prompts
    if(!player.dead&&gameState==='playing'){
        const house=regionData.objects.find(o=>o.id==='house'||o.id==='market');
        if(house&&nearEnough(player,{...house,w:120,h:120},90))drawPrompt(house.x+48,house.y-10,'E: Descansar');
        regionData.npcs.forEach(npc=>{
            if(nearEnough(player,npc,70))drawPrompt(npc.x+16,npc.y-16,`E: ${npc.name}`);
        });
        regionData.objects.forEach(obj=>{
            if(obj.type==='soil'&&collides({...player,x:player.x-8,w:48,h:56},obj)){
                const label=obj.status==='empty'?'E: Sembrar':obj.status==='ready'?'E: Cosechar':'⏳ Creciendo';
                drawPrompt(obj.x+24,obj.y-8,label);
            }
        });
        // Region exit arrow
        if(regionData.exitRight){
            ctx.save();ctx.fillStyle='rgba(255,255,255,.4)';
            ctx.font='14px monospace';ctx.textAlign='center';ctx.fillText('▶',W-8,H/2);ctx.restore();
        }
        if(regionData.exitLeft){
            ctx.save();ctx.fillStyle='rgba(255,255,255,.4)';
            ctx.font='14px monospace';ctx.textAlign='center';ctx.fillText('◀',8,H/2);ctx.restore();
        }
    }
    if(player.dead){
        ctx.fillStyle='rgba(80,0,0,.5)';ctx.fillRect(0,0,W,H);
        ctx.fillStyle='#ef4444';ctx.font='12px monospace';ctx.textAlign='center';
        ctx.fillText('HAS CAÍDO',W/2,H/2-10);
        ctx.fillStyle='#fbbf24';ctx.font='7px monospace';
        ctx.fillText('Acércate a la casa y presiona E',W/2,H/2+14);
    }

    // Clouds
    ctx.save();ctx.globalAlpha=.22;ctx.fillStyle='#fff';
    world.clouds.forEach(c=>{ctx.beginPath();ctx.ellipse(c.x,c.y,c.w/2,c.h/2,0,0,Math.PI*2);ctx.fill();});
    ctx.restore();

    if(nightI>0){ctx.fillStyle=`rgba(10,15,40,${nightI*.8})`;ctx.fillRect(0,0,W,H);}

    // Loading overlay
    if(!assets.ready){
        ctx.fillStyle='rgba(0,0,0,.6)';ctx.fillRect(0,0,W,H);
        ctx.fillStyle='#fbbf24';ctx.font='10px monospace';ctx.textAlign='center';
        ctx.fillText(`Cargando assets... ${Math.floor(assets.loaded/assets.total*100)}%`,W/2,H/2);
    }
}

// ── MAIN LOOP ─────────────────────────────────────────────────
audio.init();
function loop(){ update(); draw(); requestAnimationFrame(loop); }
loop();

// ── START MENU JS ─────────────────────────────────────────────
const SM_DEFS=[
    {action:'nueva',   file:'btn_nueva_partida' },
    {action:'continuar',file:'btn_continuar'    },
    {action:'cargar',  file:'btn_cargar_partida'},
    {action:'config',  file:'btn_configuracion' },
    {action:'creditos',file:'btn_creditos'      },
    {action:'salir',   file:'btn_salir'         },
];
let smIdx=0;

function smSetIdx(i){
    document.querySelectorAll('.sm-btn img').forEach((img,j)=>{
        img.src=UI_PATH+`start_menu/buttons_${j===i?'selected':'normal'}/${SM_DEFS[j].file}.png`;
    });
    smIdx=i;
    document.getElementById('sm-diamond').style.top=(i*53+3)+'px';
}

async function smActivate(){
    const action=SM_DEFS[smIdx].action;
    if(action==='nueva'){
        await startNewGame(); enterGame();
        showDialogue('¡Partida Nueva!\nHabla con Marisol para empezar.\nUsa WASD para moverte, E para interactuar.');
    }else if(action==='continuar'||action==='cargar'){
        const ok=await loadGame();
        enterGame();
        if(!ok)showDialogue('No se encontró una partida guardada.\nSe creó una partida nueva.');
    }else if(action==='config'){
        enterGame();openPauseMenu();pmSetIdx(6);
    }else if(action==='creditos'){
        openCreditos();
    }else if(action==='salir'){
        showDialogue('Cierra la pestaña para salir.');enterGame();
    }
}

function enterGame(){ gameState='playing'; document.getElementById('start-menu').classList.add('hidden'); }

document.querySelectorAll('.sm-btn').forEach((btn,i)=>{
    btn.addEventListener('mouseenter',()=>{
        if(i!==smIdx)btn.querySelector('img').src=UI_PATH+`start_menu/buttons_hover/${SM_DEFS[i].file}.png`;
    });
    btn.addEventListener('mouseleave',()=>{
        btn.querySelector('img').src=UI_PATH+`start_menu/buttons_${i===smIdx?'selected':'normal'}/${SM_DEFS[i].file}.png`;
    });
    btn.addEventListener('click',()=>{smSetIdx(i);smActivate();});
});
smSetIdx(0);

// ── CRÉDITOS ─────────────────────────────────────────────────
function openCreditos(){document.getElementById('creditos').classList.add('active');}
function closeCreditos(){document.getElementById('creditos').classList.remove('active');}
window.closeCreditos=closeCreditos;

// ── PAUSE MENU JS ─────────────────────────────────────────────
const PM_DEFS=[
    {action:'reanudar',   file:'btn_reanudar'           },
    {action:'guardar',    file:'btn_guardar'            },
    {action:'inventario', file:'btn_inventario'         },
    {action:'misiones',   file:'btn_misiones'           },
    {action:'mapa',       file:'btn_mapa'               },
    {action:'habilidades',file:'btn_habilidades'        },
    {action:'config',     file:'btn_configuracion'      },
    {action:'salir_menu', file:'btn_salir_menu_principal'},
];
let pmIdx=0;
const PM_VIEWS={inventario:'pmv-inventario',misiones:'pmv-misiones',mapa:'pmv-mapa',habilidades:'pmv-habilidades',config:'pmv-config'};
const ALL_PM_VIEWS=['pmv-default','pmv-inventario','pmv-misiones','pmv-mapa','pmv-habilidades','pmv-config'];

function pmSetIdx(i){
    document.querySelectorAll('.pm-btn img').forEach((img,j)=>{
        img.src=UI_PATH+`pause_menu/buttons_${j===i?'selected':'normal'}/${PM_DEFS[j].file}.png`;
    });
    pmIdx=i;
    document.getElementById('pm-diamond').style.top=(48+i*44)+'px';
    // Switch right panel
    const viewId=PM_VIEWS[PM_DEFS[i].action]||'pmv-default';
    ALL_PM_VIEWS.forEach(id=>{const el=document.getElementById(id);if(el)el.style.display=id===viewId?'':'none';});
    // Refresh content
    const a=PM_DEFS[i].action;
    if(a==='inventario')  refreshPMInventory();
    if(a==='misiones')    refreshPMMisiones();
    if(a==='mapa')        refreshPMMapa();
    if(a==='habilidades') refreshPMHabilidades();
    if(a==='config')      refreshPMConfig();
}

function refreshPMInventory(){
    const el=document.getElementById('pm-inv-content');
    if(!el)return;
    const m=player.inventory.maiz;
    el.innerHTML=m>0
        ?`<div style="color:#4ade80">🌽 Maíz x${m}</div><div style="color:#6b7280;font-size:5.5px;margin-top:4px">Valor: ${m*10} oro (Don Chepe)<br>Valor: ${m*12} oro (Mercader)</div>`
        :'<div style="color:#6b7280">Inventario vacío.<br><br>Cosecha maíz en<br>los campos del norte.</div>';
}

function refreshPMMisiones(){
    const el=document.getElementById('pm-mis-content');if(!el)return;
    const qd=world.questsDone,m=player.inventory.maiz,k=player.killCount,g=player.gold;
    const row=(done,text)=>`<div style="color:${done?'#4ade80':'#e2e8f0'};${done?'text-decoration:line-through':''};margin-bottom:6px">${done?'✓':'◦'} ${text}</div>`;
    el.innerHTML=
        row(qd.q1,`Cosecha 3 mazorcas (${Math.min(m,3)}/3)`)+
        row(qd.q2,'Viaja a Chiquimula')+
        row(qd.q3,`Gana 50 de oro (${Math.min(g,50)}/50)`)+
        row(qd.q4,`Derrota 5 enemigos (${Math.min(k,5)}/5)`);
}

function refreshPMMapa(){
    const mc=document.getElementById('minimap');if(!mc)return;
    const mctx=mc.getContext('2d');const mw=mc.width,mh=mc.height;
    mctx.fillStyle='#1a0a00';mctx.fillRect(0,0,mw,mh);
    const scaleX=mw/W,scaleY=mh/H;
    // Terrain
    mctx.fillStyle=world.region==='chiquimula'?'#5d3d1e':'#78583a';mctx.fillRect(0,0,mw,mh);
    // Objects
    regionData.objects.forEach(o=>{
        const colors={solid:'#1d4ed8',house:'#b91c1c',soil:'#5c3d1e',bridge:'#92400e'};
        mctx.fillStyle=colors[o.type]||'#666';
        mctx.fillRect(o.x*scaleX,o.y*scaleY,(o.w||48)*scaleX,(o.h||48)*scaleY);
        if(o.type==='soil'&&o.status==='ready'){mctx.fillStyle='#eab308';mctx.fillRect(o.x*scaleX,o.y*scaleY,48*scaleX,48*scaleY);}
    });
    // NPCs
    mctx.fillStyle='#f472b6';
    regionData.npcs.forEach(n=>{mctx.fillRect(n.x*scaleX,n.y*scaleY,4,4);});
    // Enemies
    mctx.fillStyle='#ef4444';
    world.enemies.forEach(e=>{mctx.fillRect(e.x*scaleX,e.y*scaleY,3,3);});
    // Player
    mctx.fillStyle='#fbbf24';
    mctx.fillRect(player.x*scaleX-2,player.y*scaleY-2,6,6);
    // Region label
    mctx.fillStyle='rgba(0,0,0,.5)';mctx.fillRect(0,mh-14,mw,14);
    mctx.fillStyle='#e2e8f0';mctx.font='6px monospace';mctx.textAlign='left';
    mctx.fillText(regionData.name,4,mh-4);
}

function refreshPMHabilidades(){
    const el=document.getElementById('pm-hab-content');if(!el)return;
    const lvl=player.level;
    el.innerHTML=
        `<div style="color:#fbbf24;margin-bottom:6px">Nivel ${lvl}</div>`+
        `<div style="color:#e2e8f0">⚔ Ataque: ${5+lvl*2} dmg</div>`+
        `<div style="color:#e2e8f0">🛡 Defensa: ${lvl} pts</div>`+
        `<div style="color:#e2e8f0">🌱 Siembra: ${lvl>2?'Rápida':'Normal'}</div>`+
        (lvl>=3?`<div style="color:#4ade80;margin-top:6px">✓ Doble Cosecha</div>`:'<div style="color:#6b7280;margin-top:6px">◦ Doble Cosecha (Lv.3)</div>')+
        (lvl>=5?`<div style="color:#4ade80">✓ Golpe Crítico</div>`:'<div style="color:#6b7280">◦ Golpe Crítico (Lv.5)</div>');
}

function refreshPMConfig(){
    const m=document.getElementById('cfg-music'),s=document.getElementById('cfg-sfx'),sp=document.getElementById('cfg-speed');
    if(m){m.innerText=cfg.musicOn?'ON':'OFF';m.style.color=cfg.musicOn?'#4ade80':'#ef4444';}
    if(s){s.innerText=cfg.sfxOn?'ON':'OFF';s.style.color=cfg.sfxOn?'#4ade80':'#ef4444';}
    if(sp)sp.innerText=cfg.speed+'x';
}

function updatePauseStats(){
    document.getElementById('pm-level').innerText='Nivel '+player.level;
    document.getElementById('pm-hp').innerText=`HP: ${Math.floor(player.hp)}/${player.maxHp}`;
    document.getElementById('pm-eng').innerText=`ENG: ${Math.floor(player.energy)}/${player.maxEnergy}`;
    document.getElementById('pm-xp').innerText=`XP: ${player.xp}/${player.nextLevelXp}`;
    document.getElementById('pm-gold').innerText='Oro: '+player.gold;
    document.getElementById('pm-kills').innerText='Enemigos: '+player.killCount;
    document.getElementById('pm-hp-fill').style.width=`${player.hp/player.maxHp*100}%`;
    document.getElementById('pm-eng-fill').style.width=`${player.energy/player.maxEnergy*100}%`;
    document.getElementById('pm-xp-fill').style.width=`${Math.min(100,player.xp/player.nextLevelXp*100)}%`;
    const qd=world.questsDone,m=player.inventory.maiz,g=player.gold;
    const qEl=document.getElementById('pm-quests-default');
    if(qEl){
        const row=(done,text)=>`<div style="color:${done?'#4ade80':'#e2e8f0'};${done?'text-decoration:line-through':''};margin-bottom:4px">${done?'✓':'◦'} ${text}</div>`;
        qEl.innerHTML=
            row(qd.q1,'Cosecha 3 mazorcas')+
            row(qd.q2,'Viaja a Chiquimula')+
            row(qd.q3,'Gana 50 de oro')+
            row(qd.q4,'Derrota 5 enemigos');
    }
}

function pmActivate(){
    const action=PM_DEFS[pmIdx].action;
    if(action==='reanudar'){closePauseMenu();}
    else if(action==='guardar'){closePauseMenu();setTimeout(()=>saveGame(),200);}
    else if(action==='salir_menu'){
        closePauseMenu();gameState='start';
        document.getElementById('start-menu').classList.remove('hidden');
    }else if(action==='mapa'||action==='inventario'||action==='misiones'||action==='habilidades'||action==='config'){
        // Already showing view via pmSetIdx — just keep pause menu open
    }
}

function openPauseMenu(){
    gameState='paused';updatePauseStats();pmSetIdx(0);
    document.getElementById('pause-menu').classList.add('active');
}
function closePauseMenu(){
    gameState='playing';
    document.getElementById('pause-menu').classList.remove('active');
}

document.querySelectorAll('.pm-btn').forEach((btn,i)=>{
    btn.addEventListener('mouseenter',()=>pmSetIdx(i));
    btn.addEventListener('click',()=>{pmSetIdx(i);pmActivate();});
});
pmSetIdx(0);

// ── WINDOW EXPORTS ────────────────────────────────────────────
window.saveGame=saveGame;window.openPauseMenu=openPauseMenu;
window.closeDialogue=closeDialogue;window.toggleMusic=toggleMusic;
window.toggleSFX=toggleSFX;window.cycleSpeed=cycleSpeed;
</script>
</body>
</html>
