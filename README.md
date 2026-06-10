# UNU Mega Menu

Трирівневе мега-меню для сайту кафедри (факультету) УНУ на **MODX Evolution**.
Меню будується безпосередньо з дерева ресурсів MODX — без Wayfinder, бо потрібна
різна розмітка для рівня-1 («панель») і рівня-2 («колонка-група»).

## Як це працює

| Рівень | Що це | CSS-клас |
|--------|-------|----------|
| 1 | Розділи у верхньому (синьому) рядку | `.nav__item` / `.nav__link` |
| 2 | Заголовки колонок панелі **або** прості посилання, якщо немає 3-го рівня | `.mega__group-head` / `.mega__link` |
| 3 | Підпункти колонки | `.mega__sublink` |

Верхній пункт **без дітей** виводиться як звичайне посилання (`mega.top.link`).
Верхній пункт **з дітьми** розкриває панель (`mega.top.panel`):

- якщо хоча б один нащадок має свої діти → панель із колонками-групами
  (`mega.group` для груп із підпунктами, `mega.group.solo` для груп без них);
- інакше → пласка панель посилань (`mega.link`).

Колонка з великою кількістю підпунктів (понад `&wideAfter`) автоматично займає
дві шпальти (клас `span-2`).

## Структура

```
assets/mega-menu/
  mega-menu.css            стилі мега-меню
  mega-menu.js             поведінка (відкриття панелей, пошук, моб. меню)

modx/snipets/mega-menu/
  UNUMegaMenu.php          сніпет — генерує <li> верхнього рівня

modx/chunks/
  header-mega-menu.tpl     шапка сайту з викликом меню та панеллю пошуку
  menu/
    mega.top.link.tpl      верхній пункт без панелі
    mega.top.panel.tpl     верхній пункт із панеллю
    mega.group.tpl         група рівня-2 з підпунктами
    mega.group.solo.tpl    група рівня-2 без підпунктів
    mega.link.tpl          посилання у пласкій панелі
    mega.sublink.tpl       підпункт рівня-3
```

> На бойовому сайті CSS/JS лежать за шляхами
> `assets/templates/html/css/mega-menu.css` та `.../js/mega-menu.js`
> (див. підключення у `header-mega-menu.tpl`). Файли в `assets/mega-menu/` —
> версія цих ресурсів під контролем git.

## Сніпет `UNUMegaMenu`

Виклик **некешований** (`[! … !]`), бо меню підсвічує активний розділ:

```
[!UNUMegaMenu?
    &startId=`[%site-start%]`
    &blurbField=`introtext`
    &wideAfter=`2`
    &level=`2`
    &excludeDocs=`[[clearDocsForMenu]]`
!]
```

Сніпет повертає лише `<li>…</li>` верхнього рівня; обгортку
`<ul class="nav" id="nav">` дає чанк `header-mega-menu`.

### Параметри

| Параметр | Призначення | За замовч. |
|----------|-------------|------------|
| `&startId` | id контейнера, чиї діти = пункти меню (0 — корінь) | `0` |
| `&level` | макс. глибина меню: 1, 2 або 3 | `3` |
| `&excludeDocs` | список id для виключення (через кому) | `''` |
| `&blurbField` | поле опису розділу для фіче-колонки панелі | `description` |
| `&wideAfter` | к-сть підпунктів, після якої колонка займає 2 шпальти | `8` |
| `&tplTopLink` | чанк: верхній пункт без панелі | `mega.top.link` |
| `&tplPanel` | чанк: верхній пункт із панеллю | `mega.top.panel` |
| `&tplGroup` | чанк: група рівня-2 з підпунктами | `mega.group` |
| `&tplGroupSolo` | чанк: група рівня-2 без підпунктів | `mega.group.solo` |
| `&tplLink` | чанк: посилання у пласкій панелі | `mega.link` |
| `&tplSublink` | чанк: підпункт рівня-3 | `mega.sublink` |

### Логіка вибору пунктів

У меню потрапляють ресурси, які `published = 1`, `deleted = 0`, `hidemenu = 0`,
відсортовані за `menuindex ASC, id ASC`. Підтримуються посилання-weblink
(`type = reference`): для них URL береться з поля `content`, а не генерується.
Напис пункту — `menutitle`, з фолбеком на `pagetitle`.

## Підключення

1. Створити сніпет **UNUMegaMenu** з вмісту `modx/snipets/mega-menu/UNUMegaMenu.php`.
2. Створити чанки з теки `modx/chunks/menu/` (імена чанків = імена файлів без `.tpl`).
3. Підключити шапку `header-mega-menu.tpl` у шаблоні сторінки.
4. Покласти `mega-menu.css` і `mega-menu.js` за шляхами, на які посилається шапка.

Меню залежить від плейсхолдерів сайту (`[%site-start%]`, `[%search_id%]`,
`[%search-text%]` тощо) та сніпета `clearDocsForMenu`, який повертає список
службових id для виключення.

---

# Архів новин

Стрічка новин/подій із пагінацією, фільтрами та бічним календарем-архівом.
Дані агрегуються з кількох джерел в одну хронологічну стрічку.

## Джерела даних

1. **`site_ocontent`** — імпортований контент підрозділів (шаблон `template=25`),
   відфільтрований за типом і поточною мовою (`evoBabel_curLang`);
2. **Локальні документи** — діти теки `news-dir` / `events-dir`
   (id беруться з `$_SESSION['perevod']`).

> Імпорт із зовнішніх сайтів через cURL/XML у `NewsAggregate` присутній, але
> наразі **вимкнений** (`$SKIP_EVO_SEARCH = TRUE`).

## Структура

```
assets/
  archive.css              стилі сторінки архіву та календаря
  archive-news.js          календар-архів (рендер, навігація по місяцях, кліки)

modx/snipets/news/
  NewsAggregate.php        стрічка + пагінація (HTML/JSON)
  NewsAggregateData.php    JSON-карта дат для календаря

modx/templates/
  NewsAggregateTemlate.tpl шаблон сторінки архіву (стрічка + бічний rail)

modx/chunks/news/
  news_section.tpl         блок останніх новин для головної
  item.first.v2.tpl        картка «головної» новини
```

> На бойовому сайті CSS/JS архіву лежать у `assets/templates/html/css/news/`.

## Сніпет `NewsAggregate`

Виводить стрічку та зберігає пагінатор у плейсхолдер (`&pagerPH`, типово
`news.pager`), який потім підставляється окремо:

```
[!NewsAggregate?
    &rowTpl=`news.feed.item`
    &type=`news`
    &limit=`20`
    &showImage=`true`
    &showIntro=`true`
!]
...
[+news.pager+]
```

### Параметри

| Параметр | Призначення | За замовч. |
|----------|-------------|------------|
| `&rowTpl` | чанк рядка стрічки (**обов'язково**) | — |
| `&type` | `news` \| `events` (**обов'язково**) | — |
| `&limit` | елементів на сторінку (0 — всі) | `20` |
| `&display` | синонім `limit` (стара сумісність) | — |
| `&outerTpl` | обгортка з `[+wrapper+]` та `[+pagination+]` | `''` |
| `&alt` | чанк, якщо нічого не знайдено | `''` |
| `&format` | `html` \| `json` | `html` |
| `&pageVar` | GET-параметр номера сторінки | `page` |
| `&dateVar` | GET-параметр дня `?date=YYYY-MM-DD` | `date` |
| `&srcVar` | GET-параметр джерела `?src=local` \| `?src=<depId>` | `src` |
| `&pagerPH` | плейсхолдер пагінатора | `news.pager` |
| `&nextLabel` / `&prevLabel` | написи навігації («назад» порожнє = сховати) | `далі »` / `''` |
| `&edge` | к-сть номерів сторінок з кожного краю | `3` |
| `&around` | к-сть сусідів навколо поточної сторінки | `1` |
| `&showImage` / `&showIntro` | передати в чанк рядка прапорці виводу | `false` |

**Сортування:** події (`events`) — за зростанням дати (майбутні вперед, минулі
відкидаються); новини (`news`) — за спаданням (свіжі вперед).

**Фільтри через GET:** `?date=YYYY-MM-DD` (один день), `?src=local` (лише
локальні документи) або `?src=<depId>` (лише обраний підрозділ). Обидва
зберігаються в посиланнях пагінатора.

## Сніпет `NewsAggregateData` (календар)

Збирає дати публікацій з тих самих джерел і повертає JSON для календаря.
Підключається до `archive-news.js` через глобальну змінну:

```html
<script>window.NEWS_ARCHIVE = [!NewsAggregateData? &type=`news`!];</script>
<script src="[(base_url)]assets/.../archive-news.js"></script>
```

Формат відповіді (**місяць 0-індексний**, як у JS `Date`):

```json
{
  "days":    { "2026-5": [1, 2, 3] },
  "years":   [ { "y": 2026, "n": 48 } ],
  "sources": [ { "id": "local", "name": "Новини сайту" }, { "id": 42, "name": "…" } ]
}
```

## Календар `archive-news.js`

Будує сітку місяця (тиждень із понеділка), підсвічує дні з новинами
(`.has-news`) і сьогодні (`.is-today`), дозволяє гортати місяці кнопками
`#calPrev` / `#calNext`. Клік по дню з новинами веде на сторінку архіву з
`?date=YYYY-MM-DD`; базовий URL береться з `data-url` контейнера `#cal`.

## Підключення

1. Створити сніпети **NewsAggregate** і **NewsAggregateData** з теки
   `modx/snipets/news/`.
2. Створити чанки рядків (`news.feed.item`, `item.first.v2` тощо) та шаблон
   `NewsAggregateTemlate`.
3. Покласти `archive.css` і `archive-news.js` за шляхами шаблону.
4. У шаблоні сторінки розмістити стрічку, `[+news.pager+]`, контейнер `#cal`
   та ініціалізацію `window.NEWS_ARCHIVE`.

Залежності: підрозділи з `template=25`, TV `dep-email/dep-site/dep-export/dep-filter`
(id 54–57), таблиці `site_ocontent` та `site_import`, плейсхолдери теки новин
у `$_SESSION['perevod']` (`news-dir`, `events-dir`).
