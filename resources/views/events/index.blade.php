@extends('layouts.app')

@section('content')
{{-- ── Server → JS data ───────────────────────────────────────── --}}
<script>
const _EVENTS   = @json($events);
const _IS_AUTH  = @json(auth()->check());
const _AUTH_ID  = @json(auth()->id());
const _IS_ADMIN = @json(auth()->check() && auth()->user()->hasRole('admin'));
const _CSRF     = document.querySelector('meta[name="csrf-token"]').content;
</script>

{{-- ── Mount points ────────────────────────────────────────────── --}}
<div id="em-header"></div>
<div id="em-filters"></div>
<div id="em-af"></div>
<div id="em-cal"></div>

{{-- ── Floating tooltip ────────────────────────────────────────── --}}
<div id="em-tooltip"></div>

{{-- ── Modal ───────────────────────────────────────────────────── --}}
<div id="em-backdrop" class="modal-backdrop hidden" onclick="if(event.target===this)closeModal()">
  <div class="modal-box">
    <div class="modal-header">
      <div id="m-title" class="modal-title">Create Event</div>
      <div id="m-desc"  class="modal-desc">Add a new event</div>
    </div>

    <div class="modal-body">
      <div class="form-group">
        <label class="label" for="m-f-title">Title</label>
        <input id="m-f-title" class="input" type="text" placeholder="Event title">
      </div>
      <div class="form-group">
        <label class="label" for="m-f-desc">Description</label>
        <textarea id="m-f-desc" class="textarea" rows="3" placeholder="Event description"></textarea>
      </div>
      <div class="form-group">
        <label class="label" for="m-f-loc">Location</label>
        <input id="m-f-loc" class="input" type="text" placeholder="Location">
      </div>
      <div class="form-row form-row-2">
        <div class="form-group">
          <label class="label" for="m-f-date">Date</label>
          <input id="m-f-date" class="input" type="date">
        </div>
        <div class="form-group">
          <label class="label" for="m-f-time">Start Time</label>
          <input id="m-f-time" class="input" type="time">
        </div>
      </div>
      <div class="form-row form-row-2">
        <div class="form-group">
          <label class="label" for="m-f-status">Status</label>
          <select id="m-f-status" class="select">
            <option value="active">Active</option>
            <option value="cancelled">Cancelled</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="form-group">
          <label class="label" for="m-f-color">Color</label>
          <select id="m-f-color" class="select">
            <option value="blue">Blue</option>
            <option value="green">Green</option>
            <option value="purple">Purple</option>
            <option value="orange">Orange</option>
            <option value="pink">Pink</option>
            <option value="red">Red</option>
          </select>
        </div>
      </div>
      <div id="m-errors" class="err-box" style="display:none"><ul id="m-err-list"></ul></div>
    </div>

    <div class="modal-footer">
      <button id="m-btn-del"  class="btn btn-destructive" style="display:none" onclick="handleDelete()">Delete</button>
      <button                  class="btn btn-outline"                           onclick="closeModal()">Cancel</button>
      <button id="m-btn-save" class="btn btn-default"                            onclick="handleSave()">Create</button>
    </div>
  </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════════════════════
const COLORS = [
  {v:'blue',   label:'Blue',   hex:'#3b82f6'},
  {v:'green',  label:'Green',  hex:'#22c55e'},
  {v:'purple', label:'Purple', hex:'#a855f7'},
  {v:'orange', label:'Orange', hex:'#f97316'},
  {v:'pink',   label:'Pink',   hex:'#ec4899'},
  {v:'red',    label:'Red',    hex:'#ef4444'},
];
const STATUSES = ['active','cancelled','completed'];

const SVG = {
  chevL:    `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>`,
  chevR:    `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>`,
  plus:     `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M12 5v14"/></svg>`,
  cal:      `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>`,
  clock:    `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,
  grid:     `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>`,
  list:     `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>`,
  search:   `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>`,
  filter:   `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>`,
  x:        `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18m0-12 12 12"/></svg>`,
  pin:      `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>`,
};

// ═══════════════════════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════════════════════
let events = _EVENTS.map(norm);
let view          = 'month';
let curDate       = new Date();
let search        = '';
let fColors       = [];
let fCats         = [];
let dragId        = null;
let mMode         = null;   // 'create' | 'edit' | 'view'
let mEventId      = null;

// ═══════════════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════════════
const $  = id => document.getElementById(id);
const esc = s => String(s ?? '')
  .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

function norm(e) {
  return {
    id:         e.id,
    title:      e.title       || '',
    desc:       e.description || '',
    loc:        e.location    || '',
    date:       e.date,           // "YYYY-MM-DD"
    startTime:  e.start_time  || '',
    startHour:  e.start_time  ? parseInt(e.start_time) : null,
    status:     e.status,
    color:      e.color || 'blue',
    userId:     e.user_id,
    userName:   (e.user && e.user.name) || 'Unknown',
  };
}
function colorHex(v) { return (COLORS.find(c=>c.v===v)||COLORS[0]).hex; }
function isSameDay(a,b) {
  return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();
}
function d2s(d) {
  return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}
function parseD(s) { const [y,m,d]=s.split('-').map(Number); return new Date(y,m-1,d); }
function fmtDate(s) { return parseD(s).toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'}); }
function fmtHour(h) { if(h==null)return ''; const ap=h<12?'AM':'PM'; return `${h%12||12}:00 ${ap}`; }
function weekStart(d) { const r=new Date(d); r.setDate(d.getDate()-d.getDay()); r.setHours(0,0,0,0); return r; }
function canEdit(ev) { return _IS_AUTH && (_AUTH_ID==ev.userId||_IS_ADMIN); }
function statusBadge(s) {
  const cls = {active:'badge-active',cancelled:'badge-cancelled',completed:'badge-completed'}[s]||'badge-secondary';
  return `<span class="badge ${cls}">${esc(s)}</span>`;
}
function filtered() {
  return events.filter(e=>{
    if(search){const q=search.toLowerCase();if(!e.title.toLowerCase().includes(q)&&!e.desc.toLowerCase().includes(q)&&!e.loc.toLowerCase().includes(q))return false;}
    if(fColors.length && !fColors.includes(e.color))  return false;
    if(fCats.length   && !fCats.includes(e.status))   return false;
    return true;
  });
}

// ═══════════════════════════════════════════════════════════════
// RENDER: HEADER
// ═══════════════════════════════════════════════════════════════
function renderHeader() {
  let title = view==='month' ? curDate.toLocaleDateString('en-US',{month:'long',year:'numeric'})
    : view==='week'  ? `Week of ${weekStart(curDate).toLocaleDateString('en-US',{month:'short',day:'numeric'})}`
    : view==='day'   ? curDate.toLocaleDateString('en-US',{weekday:'long',month:'long',day:'numeric',year:'numeric'})
    : 'All Events';

  const tabs = [
    {k:'month',icon:SVG.cal,  l:'Month'},
    {k:'week', icon:SVG.grid, l:'Week'},
    {k:'day',  icon:SVG.clock,l:'Day'},
    {k:'list', icon:SVG.list, l:'List'},
  ];

  $('em-header').innerHTML = `
    <div class="cal-header">
      <div class="cal-left">
        <div class="cal-title">${esc(title)}</div>
        <div class="cal-nav">
          <button class="btn btn-outline btn-icon btn-sm" onclick="nav(-1)">${SVG.chevL}</button>
          <button class="btn btn-outline btn-sm"          onclick="goToday()">Today</button>
          <button class="btn btn-outline btn-icon btn-sm" onclick="nav(1)">${SVG.chevR}</button>
        </div>
      </div>
      <div class="cal-right">
        <div class="view-tabs hide-sm">
          ${tabs.map(t=>`<button class="view-tab${view===t.k?' active':''}" onclick="setView('${t.k}')">${t.icon} ${t.l}</button>`).join('')}
        </div>
        <select class="select show-sm" style="width:auto" onchange="setView(this.value)">
          ${tabs.map(t=>`<option value="${t.k}"${view===t.k?' selected':''}>${t.l} View</option>`).join('')}
        </select>
        ${_IS_AUTH?`<button class="btn btn-default btn-sm" onclick="openCreate()">${SVG.plus} New Event</button>`:''}
      </div>
    </div>`;
}

// ═══════════════════════════════════════════════════════════════
// RENDER: FILTER BAR
// ═══════════════════════════════════════════════════════════════
function renderFilters() {
  const cc = fColors.length, sc = fCats.length;
  $('em-filters').innerHTML = `
    <div class="filter-bar">
      <div class="input-wrap" style="flex:1;min-width:12rem">
        <span class="input-icon input-icon-l">${SVG.search}</span>
        <input id="em-search" class="input input-pl" type="text" placeholder="Search events…" value="${esc(search)}">
        <span id="em-search-x" class="input-icon input-icon-r" style="display:${search?'flex':'none'}" onclick="clearSearch()">${SVG.x}</span>
      </div>

      <div class="dropdown" id="dd-c">
        <button class="btn btn-outline btn-sm" onclick="toggleDD('dd-c',event)">
          ${SVG.filter} Colors${cc?` <span class="badge badge-secondary">${cc}</span>`:''}
        </button>
        <div class="dropdown-menu">
          <div class="dropdown-lbl">Filter by Color</div>
          <div class="dropdown-sep"></div>
          ${COLORS.map(c=>`
            <div class="dropdown-item" onclick="toggleFC('${c.v}',event)">
              <div class="dd-check${fColors.includes(c.v)?' on':''}"></div>
              <span class="swatch sw-${c.v}"></span>${c.label}
            </div>`).join('')}
        </div>
      </div>

      <div class="dropdown" id="dd-s">
        <button class="btn btn-outline btn-sm" onclick="toggleDD('dd-s',event)">
          ${SVG.filter} Status${sc?` <span class="badge badge-secondary">${sc}</span>`:''}
        </button>
        <div class="dropdown-menu">
          <div class="dropdown-lbl">Filter by Status</div>
          <div class="dropdown-sep"></div>
          ${STATUSES.map(s=>`
            <div class="dropdown-item" onclick="toggleFS('${s}',event)">
              <div class="dd-check${fCats.includes(s)?' on':''}"></div>
              ${s.charAt(0).toUpperCase()+s.slice(1)}
            </div>`).join('')}
        </div>
      </div>

      ${cc||sc?`<button class="btn btn-ghost btn-sm" onclick="clearFilters()">${SVG.x} Clear</button>`:''}
    </div>`;

  // Re-attach search listener (element was replaced)
  const inp = $('em-search');
  inp.addEventListener('input', function(){ search=this.value; $('em-search-x').style.display=search?'flex':'none'; renderAF(); renderCal(); });
}

// ═══════════════════════════════════════════════════════════════
// RENDER: ACTIVE FILTERS
// ═══════════════════════════════════════════════════════════════
function renderAF() {
  if(!fColors.length && !fCats.length){ $('em-af').innerHTML=''; return; }
  const cb = fColors.map(v=>{const c=COLORS.find(x=>x.v===v); return `<span class="af-badge"><span class="swatch sw-${v}"></span>${c.label}<span class="af-rm" onclick="toggleFC('${v}',event)">${SVG.x}</span></span>`;});
  const sb = fCats.map(s=>`<span class="af-badge">${s.charAt(0).toUpperCase()+s.slice(1)}<span class="af-rm" onclick="toggleFS('${s}',event)">${SVG.x}</span></span>`);
  $('em-af').innerHTML = `<div class="active-filters"><span style="color:var(--muted-fg)">Active filters:</span>${[...cb,...sb].join('')}</div>`;
}

// ═══════════════════════════════════════════════════════════════
// RENDER: EVENT CHIP (month)
// ═══════════════════════════════════════════════════════════════
function chip(ev) {
  const dr = canEdit(ev)?'draggable="true"':'';
  return `<div class="ev-chip ev-${ev.color}" data-eid="${ev.id}" ${dr}
    onclick="openView(${ev.id})"
    ondragstart="dStart(${ev.id},event)" ondragend="dEnd(event)">${esc(ev.title)}</div>`;
}

// ═══════════════════════════════════════════════════════════════
// RENDER: EVENT BLOCK (week / day)
// ═══════════════════════════════════════════════════════════════
function block(ev) {
  const dr  = canEdit(ev)?'draggable="true"':'';
  const sub = ev.loc ? esc(ev.loc) : (ev.startHour!=null ? fmtHour(ev.startHour) : '');
  return `<div class="ev-block ev-${ev.color}" data-eid="${ev.id}" ${dr}
    onclick="openView(${ev.id})"
    ondragstart="dStart(${ev.id},event)" ondragend="dEnd(event)">
    <div class="ev-block-t">${esc(ev.title)}</div>
    ${sub?`<div class="ev-block-s">${sub}</div>`:''}
  </div>`;
}

// ═══════════════════════════════════════════════════════════════
// RENDER: MONTH
// ═══════════════════════════════════════════════════════════════
function renderMonth(evs) {
  const y=curDate.getFullYear(), m=curDate.getMonth();
  const first=new Date(y,m,1), start=new Date(first);
  start.setDate(1-first.getDay());
  const today=new Date();

  const hdrs = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].map(d=>`<div class="month-hdr">${d}</div>`).join('');
  let cells='';
  for(let i=0;i<42;i++){
    const day=new Date(start); day.setDate(start.getDate()+i);
    const ds=d2s(day), inM=day.getMonth()===m, isT=isSameDay(day,today);
    const de=evs.filter(e=>e.date===ds);
    cells+=`<div class="month-cell${inM?'':' other-month'}" ondragover="event.preventDefault()" ondrop="drop('${ds}',null,event)">
      <div class="month-day-n${isT?' today':''}">${day.getDate()}</div>
      <div class="month-evs">${de.slice(0,3).map(chip).join('')}${de.length>3?`<div class="more-evs">+${de.length-3} more</div>`:''}</div>
    </div>`;
  }
  return `<div class="month-grid">${hdrs}${cells}</div>`;
}

// ═══════════════════════════════════════════════════════════════
// RENDER: WEEK
// ═══════════════════════════════════════════════════════════════
function renderWeek(evs) {
  const ws=weekStart(curDate), today=new Date();
  const days=Array.from({length:7},(_,i)=>{const d=new Date(ws);d.setDate(ws.getDate()+i);return d;});

  const hCols=days.map(d=>{
    const isT=isSameDay(d,today);
    return `<div class="tg-hcell${isT?' today-hd':''}">${d.toLocaleDateString('en-US',{weekday:'short'})}<small>${d.toLocaleDateString('en-US',{month:'short',day:'numeric'})}</small></div>`;
  });

  let rows='';
  for(let h=0;h<24;h++){
    const cols=days.map(day=>{
      const ds=d2s(day), he=evs.filter(e=>e.date===ds&&e.startHour===h);
      return `<div class="tg-cell" ondragover="event.preventDefault()" ondrop="drop('${ds}',${h},event)">${he.map(block).join('')}</div>`;
    });
    rows+=`<div class="tg-row" style="grid-template-columns:3rem repeat(7,1fr)">
      <div class="tg-time">${String(h).padStart(2,'0')}:00</div>${cols.join('')}
    </div>`;
  }
  return `<div class="time-grid">
    <div class="tg-head" style="grid-template-columns:3rem repeat(7,1fr)">
      <div class="tg-hcell"></div>${hCols.join('')}
    </div>${rows}
  </div>`;
}

// ═══════════════════════════════════════════════════════════════
// RENDER: DAY
// ═══════════════════════════════════════════════════════════════
function renderDay(evs) {
  const ds=d2s(curDate);
  let rows='';
  for(let h=0;h<24;h++){
    const he=evs.filter(e=>e.date===ds&&e.startHour===h);
    rows+=`<div class="day-row" ondragover="event.preventDefault()" ondrop="drop('${ds}',${h},event)">
      <div class="tg-time" style="padding:.375rem .5rem;border-bottom:1px solid var(--border)">${String(h).padStart(2,'0')}:00</div>
      <div class="day-cell">${he.map(block).join('')}</div>
    </div>`;
  }
  return `<div class="time-grid">${rows}</div>`;
}

// ═══════════════════════════════════════════════════════════════
// RENDER: LIST
// ═══════════════════════════════════════════════════════════════
function renderList(evs) {
  if(!evs.length) return `<div class="empty-state"><p>No events found.</p></div>`;
  return [...evs].sort((a,b)=>a.date.localeCompare(b.date)||a.startHour-b.startHour).map(ev=>`
    <div class="list-card" onclick="openView(${ev.id})">
      <div class="list-dot dot-${ev.color}"></div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;flex-wrap:wrap">
          <div class="list-title">${esc(ev.title)}</div>${statusBadge(ev.status)}
        </div>
        ${ev.desc?`<div class="list-desc">${esc(ev.desc)}</div>`:''}
        <div class="list-meta">
          ${SVG.cal} ${fmtDate(ev.date)}
          ${ev.startHour!=null?`${SVG.clock} ${fmtHour(ev.startHour)}`:''}
          ${ev.loc?`${SVG.pin} ${esc(ev.loc)}`:''}
          <span>by ${esc(ev.userName)}</span>
        </div>
      </div>
    </div>`).join('');
}

// ═══════════════════════════════════════════════════════════════
// MAIN RENDER
// ═══════════════════════════════════════════════════════════════
function renderCal() {
  const evs=filtered();
  const el=$('em-cal');
  if     (view==='month') el.innerHTML=renderMonth(evs);
  else if(view==='week')  el.innerHTML=renderWeek(evs);
  else if(view==='day')   el.innerHTML=renderDay(evs);
  else                    el.innerHTML=renderList(evs);
}

function render() { renderHeader(); renderFilters(); renderAF(); renderCal(); }

// ═══════════════════════════════════════════════════════════════
// NAVIGATION / VIEW
// ═══════════════════════════════════════════════════════════════
function nav(dir) {
  const d=new Date(curDate);
  if     (view==='month') d.setMonth(d.getMonth()+dir);
  else if(view==='week')  d.setDate(d.getDate()+dir*7);
  else if(view==='day')   d.setDate(d.getDate()+dir);
  curDate=d; render();
}
function goToday() { curDate=new Date(); render(); }
function setView(v){ view=v; render(); }
function clearSearch() { search=''; const inp=$('em-search'); if(inp)inp.value=''; $('em-search-x').style.display='none'; renderAF(); renderCal(); }

// ═══════════════════════════════════════════════════════════════
// FILTERS
// ═══════════════════════════════════════════════════════════════
function toggleFC(v,e){e&&e.stopPropagation(); fColors=fColors.includes(v)?fColors.filter(x=>x!==v):[...fColors,v]; renderFilters(); renderAF(); renderCal();}
function toggleFS(s,e){e&&e.stopPropagation(); fCats=fCats.includes(s)?fCats.filter(x=>x!==s):[...fCats,s]; renderFilters(); renderAF(); renderCal();}
function clearFilters(){ fColors=[]; fCats=[]; renderFilters(); renderAF(); renderCal(); }

// ═══════════════════════════════════════════════════════════════
// DROPDOWNS
// ═══════════════════════════════════════════════════════════════
function toggleDD(id,e){
  e&&e.stopPropagation();
  document.querySelectorAll('.dropdown.open').forEach(d=>{if(d.id!==id)d.classList.remove('open');});
  document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click',()=>{
  document.querySelectorAll('.dropdown.open').forEach(d=>d.classList.remove('open'));
});

// ═══════════════════════════════════════════════════════════════
// TOOLTIP
// ═══════════════════════════════════════════════════════════════
const ttEl=$('em-tooltip');
document.addEventListener('mouseover',e=>{
  const chip=e.target.closest('[data-eid]'); if(!chip)return;
  const ev=events.find(x=>x.id==chip.dataset.eid); if(!ev)return;
  showTT(ev,chip);
});
document.addEventListener('mouseout',e=>{
  if(e.target.closest('[data-eid]') && (!e.relatedTarget||!e.relatedTarget.closest('[data-eid]'))) hideTT();
});
function showTT(ev,anchor){
  ttEl.innerHTML=`
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:.5rem;margin-bottom:.375rem">
      <strong style="font-size:.875rem;line-height:1.3">${esc(ev.title)}</strong>
      <span class="swatch" style="background:${colorHex(ev.color)};flex-shrink:0;margin-top:2px"></span>
    </div>
    ${ev.desc?`<p style="font-size:.75rem;color:var(--muted-fg);margin:0 0 .375rem;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">${esc(ev.desc)}</p>`:''}
    <div style="font-size:.75rem;color:var(--muted-fg);display:flex;align-items:center;gap:.375rem;flex-wrap:wrap">
      ${SVG.cal} ${fmtDate(ev.date)}
      ${ev.startHour!=null?`${SVG.clock} ${fmtHour(ev.startHour)}`:''}
    </div>
    ${ev.loc?`<div style="font-size:.75rem;color:var(--muted-fg);margin-top:.25rem;display:flex;align-items:center;gap:.375rem">${SVG.pin} ${esc(ev.loc)}</div>`:''}
    <div style="margin-top:.5rem">${statusBadge(ev.status)}</div>`;

  const rect=anchor.getBoundingClientRect(), sy=window.scrollY||pageYOffset;
  ttEl.style.cssText=`display:block;left:${rect.left}px;top:${rect.bottom+sy+6}px`;
  requestAnimationFrame(()=>{
    const tr=ttEl.getBoundingClientRect();
    if(tr.right>window.innerWidth-8)  ttEl.style.left=Math.max(8,window.innerWidth-tr.width-8)+'px';
    if(tr.bottom>window.innerHeight-8) ttEl.style.top=(rect.top+sy-tr.height-6)+'px';
  });
}
function hideTT(){ttEl.style.display='none';}

// ═══════════════════════════════════════════════════════════════
// DRAG & DROP
// ═══════════════════════════════════════════════════════════════
function dStart(id,e){dragId=id;e.dataTransfer.effectAllowed='move';e.target.classList.add('dragging');}
function dEnd(e){dragId=null;e.target.classList.remove('dragging');}
function drop(ds,hour,e){
  e.preventDefault();if(!dragId)return;
  const ev=events.find(x=>x.id===dragId);if(!ev||!canEdit(ev))return;
  const h=hour!==null&&hour!==undefined?hour:(ev.startHour??9);
  apiUpdate(ev.id,{title:ev.title,description:ev.desc,location:ev.loc,event_date:ds,start_time:`${String(h).padStart(2,'0')}:00`,status:ev.status,color:ev.color})
    .then(r=>{applyUpd(norm(r));renderCal();}).catch(()=>{});
}

// ═══════════════════════════════════════════════════════════════
// MODAL
// ═══════════════════════════════════════════════════════════════
function openCreate(dateHint){
  if(!_IS_AUTH)return;
  mMode='create';mEventId=null;
  $('m-title').textContent='Create Event';
  $('m-desc').textContent='Add a new event to your calendar';
  $('m-f-title').value='',$('m-f-desc').value='',$('m-f-loc').value='';
  $('m-f-date').value=dateHint||d2s(curDate);
  $('m-f-time').value='09:00';
  $('m-f-status').value='active';
  $('m-f-color').value='blue';
  $('m-btn-save').textContent='Create';
  $('m-btn-del').style.display='none';
  setDisabled(false);clearErrs();showModal();
}
function openView(id){
  const ev=events.find(e=>e.id===id);if(!ev)return;
  const ed=canEdit(ev);
  mMode=ed?'edit':'view';mEventId=id;
  $('m-title').textContent=ed?'Edit Event':ev.title;
  $('m-desc').textContent=ed?'Edit event details':`by ${ev.userName}`;
  $('m-f-title').value=ev.title;$('m-f-desc').value=ev.desc;$('m-f-loc').value=ev.loc;
  $('m-f-date').value=ev.date;
  $('m-f-time').value=ev.startTime?ev.startTime.slice(0,5):'';
  $('m-f-status').value=ev.status;$('m-f-color').value=ev.color;
  $('m-btn-save').textContent='Save';
  $('m-btn-save').style.display=ed?'':'none';
  $('m-btn-del').style.display=ed?'':'none';
  setDisabled(!ed);clearErrs();showModal();
}
function setDisabled(v){['m-f-title','m-f-desc','m-f-loc','m-f-date','m-f-time','m-f-status','m-f-color'].forEach(id=>$(id).disabled=v);}
function showModal() {$('em-backdrop').classList.remove('hidden');}
function closeModal(){$('em-backdrop').classList.add('hidden');hideTT();}
function clearErrs(){$('m-errors').style.display='none';$('m-err-list').innerHTML='';}
function showErrs(msgs){$('m-errors').style.display='block';$('m-err-list').innerHTML=msgs.map(m=>`<li>${esc(m)}</li>`).join('');}

async function handleSave(){
  clearErrs();
  const data={
    title:$('m-f-title').value.trim(),description:$('m-f-desc').value.trim(),
    location:$('m-f-loc').value.trim(),event_date:$('m-f-date').value,
    start_time:$('m-f-time').value||null,status:$('m-f-status').value,color:$('m-f-color').value,
  };
  const btn=$('m-btn-save');btn.disabled=true;
  try{
    if(mMode==='create'){const r=await apiCreate(data);events.push(norm(r));}
    else{const r=await apiUpdate(mEventId,data);applyUpd(norm(r));}
    closeModal();renderCal();
  }catch(err){
    const msgs=extractErrs(err);showErrs(msgs.length?msgs:['Something went wrong.']);
  }finally{btn.disabled=false;}
}
async function handleDelete(){
  if(!confirm('Delete this event?'))return;
  try{await apiDel(mEventId);events=events.filter(e=>e.id!==mEventId);closeModal();renderCal();}
  catch{alert('Could not delete the event.');}
}

// ═══════════════════════════════════════════════════════════════
// API
// ═══════════════════════════════════════════════════════════════
const H={'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':_CSRF};
async function apiCreate(d){const r=await fetch('/events',{method:'POST',headers:H,body:JSON.stringify(d)});if(!r.ok)throw await r.json().catch(()=>({}));return r.json();}
async function apiUpdate(id,d){const r=await fetch(`/events/${id}`,{method:'PUT',headers:H,body:JSON.stringify(d)});if(!r.ok)throw await r.json().catch(()=>({}));return r.json();}
async function apiDel(id){const r=await fetch(`/events/${id}`,{method:'DELETE',headers:H});if(!r.ok)throw new Error('fail');}
function applyUpd(u){const i=events.findIndex(e=>e.id===u.id);if(i!==-1)events[i]=u;}
function extractErrs(err){if(!err)return[];if(err.errors)return Object.values(err.errors).flat();if(err.message)return[err.message];return[];}

// ═══════════════════════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════════════════════
render();
</script>
@endsection
