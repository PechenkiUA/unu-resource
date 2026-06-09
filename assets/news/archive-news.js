/* ───────── Архів новин — календар (MODX-версія) ─────────
 * Дані надходять з MODX через window.NEWS_ARCHIVE:
 *   { days: {"2026-5":[1,2,3], ...}, years: [{y:2026,n:48}, ...] }
 * (місяць 0-індексний). Див. сніпет NewsArchiveData.
 * Клік по дню переходить на сторінку архіву з ?date=YYYY-MM-DD —
 * обробіть параметр своїм сніпетом (pdoResources &where).
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
    : { days: {}, years: [] };
const NEWS_DAYS = ARCHIVE.days || {};
const YEAR_COUNTS = ARCHIVE.years || [];

// Базовий URL архіву для переходів по даті/року (data-attr на #cal)
const ARCHIVE_URL = (document.getElementById("cal") &&
    document.getElementById("cal").dataset.url) || window.location.pathname;

/* ───────── year-jump strip ───────── */
function renderYears(){
    const el = document.getElementById("calYears");
    if (!el) return;
    el.innerHTML = YEAR_COUNTS.map(y => `
    <a class="cal__year" href="${ARCHIVE_URL}?year=${y.y}">
      <span>${y.y}</span>
      <span class="n">${y.n}</span>
    </a>`).join("");
}

/* ───────── calendar ───────── */
let viewY = TODAY.y, viewM = TODAY.m;

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
        const isToday = (viewY === TODAY.y && viewM === TODAY.m && d === TODAY.d);
        const hasNews = newsDays.includes(d);
        const cls = ["cal__cell"];
        if (hasNews) cls.push("has-news");
        if (isToday) cls.push("is-today");
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
renderYears();
renderCalendar();

document.getElementById("calPrev").addEventListener("click", () => shiftMonth(-1));
document.getElementById("calNext").addEventListener("click", () => shiftMonth(1));

document.getElementById("calGrid").addEventListener("click", (e) => {
    const cell = e.target.closest(".has-news");
    if (!cell) return;
    const mm = String(viewM + 1).padStart(2, "0");
    const dd = String(cell.dataset.day).padStart(2, "0");
    window.location.href = `${ARCHIVE_URL}?date=${viewY}-${mm}-${dd}`;
});
