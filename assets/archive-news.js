/* ───────── Архів новин — календар (MODX-версія) ─────────
 * Дані надходять з MODX через window.NEWS_ARCHIVE:
 *   { days: {"2026-5":[1,2,3], ...},
 *     years: [{y:2026,n:48}, ...],
 *     sources: [{id:"local",name:"Новини сайту"},{id:215,name:"..."}] }
 * (місяць 0-індексний). Див. сніпет NewsAggregateData.
 * Клік по дню → ?date=YYYY-MM-DD, вибір джерела → ?src=local|<depId>.
 * Активні фільтри підсвічуються; календар відкривається на даті фільтра.
 */

const MONTHS = ["Січень","Лютий","Березень","Квітень","Травень","Червень",
    "Липень","Серпень","Вересень","Жовтень","Листопад","Грудень"];
const DOW = ["Пн","Вт","Ср","Чт","Пт","Сб","Нд"];

// Сьогодні (реальна дата клієнта)
const _now = new Date();
const TODAY = { y: _now.getFullYear(), m: _now.getMonth(), d: _now.getDate() };

// Дані з MODX (фолбек — порожньо)
const ARCHIVE = (window.NEWS_ARCHIVE && typeof window.NEWS_ARCHIVE === "object")
    ? window.NEWS_ARCHIVE
    : { days: {}, years: [], sources: [] };
const NEWS_DAYS = ARCHIVE.days || {};
const YEAR_COUNTS = ARCHIVE.years || [];
const SOURCES = ARCHIVE.sources || [];

// Базовий URL архіву для переходів (data-attr на #cal)
const ARCHIVE_URL = (document.getElementById("cal") &&
    document.getElementById("cal").dataset.url) || window.location.pathname;

// Поточні фільтри з URL
const URL_QS  = new URLSearchParams(window.location.search);
const CUR_SRC = URL_QS.get("src") || "";

let CUR_DATE = null;                       // ?date=YYYY-MM-DD
const _dm = (URL_QS.get("date") || "").match(/^(\d{4})-(\d{2})-(\d{2})$/);
if (_dm) {
    CUR_DATE = { y: +_dm[1], m: +_dm[2] - 1, d: +_dm[3] };   // m — 0-індексний
}

/* URL архіву з параметрами; активне джерело додається автоматично */
function archiveUrl(params){
    const qs = new URLSearchParams(params || {});
    if (CUR_SRC && !qs.has("src")) qs.set("src", CUR_SRC);
    const s = qs.toString();
    return s ? `${ARCHIVE_URL}?${s}` : ARCHIVE_URL;
}

/* ───────── source filter ───────── */
function renderSources(){
    const el = document.getElementById("calSources");
    if (!el || !SOURCES.length) return;

    const all = `
    <a class="cal__src${CUR_SRC === "" ? " is-on" : ""}" href="${ARCHIVE_URL}">
      Всі новини
    </a>`;

    const items = SOURCES.map(s => {
        const id = String(s.id);
        const on = (CUR_SRC === id) ? " is-on" : "";
        const qs = new URLSearchParams({ src: id });
        return `
    <a class="cal__src${on}" href="${ARCHIVE_URL}?${qs.toString()}">
      ${s.name}
    </a>`;
    }).join("");

    el.innerHTML = all + items;
}

/* ───────── year-jump strip ───────── */
function renderYears(){
    const el = document.getElementById("calYears");
    if (!el) return;
    el.innerHTML = YEAR_COUNTS.map(y => `
    <a class="cal__year" href="${archiveUrl({ year: y.y })}">
      <span>${y.y}</span>
      <span class="n">${y.n}</span>
    </a>`).join("");
}

/* ───────── calendar ───────── */
// стартуємо з дати фільтра, якщо вона є
let viewY = CUR_DATE ? CUR_DATE.y : TODAY.y;
let viewM = CUR_DATE ? CUR_DATE.m : TODAY.m;

function renderCalendar(){
    document.getElementById("calMonth").textContent = `${MONTHS[viewM]} ${viewY}`;

    const newsDays = NEWS_DAYS[`${viewY}-${viewM}`] || [];
    const first = new Date(viewY, viewM, 1);
    const startOffset = (first.getDay() + 6) % 7; // Пн-старт
    const daysInMonth = new Date(viewY, viewM + 1, 0).getDate();

    let cells = DOW.map(d => `<div class="cal__dow">${d}</div>`).join("");
    for (let i = 0; i < startOffset; i++){
        cells += `<div class="cal__cell is-empty"></div>`;
    }
    for (let d = 1; d <= daysInMonth; d++){
        const isToday  = (viewY === TODAY.y && viewM === TODAY.m && d === TODAY.d);
        const isActive = (CUR_DATE && viewY === CUR_DATE.y && viewM === CUR_DATE.m && d === CUR_DATE.d);
        const hasNews = newsDays.includes(d);
        const cls = ["cal__cell"];
        if (hasNews) cls.push("has-news");
        if (isToday) cls.push("is-today");
        if (isActive) cls.push("is-active");
        cells += `<div class="${cls.join(" ")}"${hasNews ? ` data-day="${d}"` : ""}>${d}</div>`;
    }
    document.getElementById("calGrid").innerHTML = cells;
}

function shiftMonth(delta){
    viewM += delta;
    if (viewM < 0){ viewM = 11; viewY--; }
    if (viewM > 11){ viewM = 0; viewY++; }
    renderCalendar();
}

/* ───────── init ───────── */
renderSources();
renderYears();
renderCalendar();

document.getElementById("calPrev").addEventListener("click", () => shiftMonth(-1));
document.getElementById("calNext").addEventListener("click", () => shiftMonth(1));

document.getElementById("calGrid").addEventListener("click", (e) => {
    const cell = e.target.closest(".has-news");
    if (!cell) return;
    // повторний клік по активному дню — зняти фільтр дати
    if (cell.classList.contains("is-active")) {
        window.location.href = archiveUrl({});
        return;
    }
    const mm = String(viewM + 1).padStart(2, "0");
    const dd = String(cell.dataset.day).padStart(2, "0");
    window.location.href = archiveUrl({ date: `${viewY}-${mm}-${dd}` });
});