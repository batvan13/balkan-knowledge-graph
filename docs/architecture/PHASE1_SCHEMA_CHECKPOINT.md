# BKG Phase 1 Schema Checkpoint

## Статус

Този документ описва текущото одобрено и имплементирано състояние на Phase 1 базовата schema архитектура за BKG (Balkan Knowledge Graph).

Phase 1 е проектирана като:

- accommodation-first старт, с одобрени food-place и attraction разширения
- с универсален централен `entities` модел
- с отделени translation слоеве
- с отделени type-specific слоеве
- без преждевременна абстракция
- без стария фрагментиран модел с отделни главни таблици по тип

---

## Архитектурни принципи

Одобрените принципи за тази фаза са:

- една централна таблица `entities`
- няма отделни главни таблици като `hotels`, `restaurants`, `attractions`
- multilingual съдържанието се държи в translation tables
- няма language-specific колони като `name_bg`, `name_en`
- няма speculative schema design
- няма package-driven schema decisions
- няма boolean explosion
- няма смесване на structural, content, media и amenity слоеве

---

## Имплементирани таблици

### 1. `entities`
Централна canonical таблица за обектите.

Одобрени колони:
- `id`
- `entity_type_id`
- `place_id`
- `user_id`
- `slug`
- `status`
- `lat`
- `lng`
- timestamps

Одобрени status стойности:
- `draft`
- `published`
- `archived`

Роля:
- structural identity
- type reference
- place reference
- owner/manager reference
- canonical slug
- publication state
- coordinates

---

### 2. `entity_types`
Lookup таблица за canonical entity type codes.

Одобрени колони:
- `id`
- `code`
- timestamps

Одобрен seed set:

Accommodation types:
- `hotel`
- `guesthouse`
- `apartment`
- `house`
- `villa`
- `hostel`
- `bungalow`
- `camping`
- `lodge`

Food-place types:
- `restaurant`
- `tavern`
- `bar`
- `pub`
- `cafe`
- `bistro`
- `fast_food`
- `pastry_shop`

Attraction types:
- `museum`
- `gallery`
- `monument`
- `monastery`
- `church`
- `chapel`
- `fortress`
- `castle`
- `palace`
- `tomb`
- `megalith`
- `waterfall`
- `cave`
- `beach`
- `park`
- `reservoir`
- `spring`
- `rock_formation`
- `heritage_tree`
- `observatory`
- `planetarium`
- `zoo`

Бележка: всички codes се съхраняват в един `EntityTypeSeeder`.

---

### 3. `places`
Структурна географска таблица.

Одобрени колони:
- `id`
- `parent_id`
- `type`
- `slug`
- timestamps

Одобрени `type` стойности:
- `country`
- `region`
- `city`
- `village`
- `resort`
- `island`
- `mountain`
- `area`

Роля:
- географска йерархия
- canonical place structure
- parent-child geography

---

### 4. `place_translations`
Multilingual naming layer за `places`.

Одобрени колони:
- `id`
- `place_id`
- `locale`
- `name`
- timestamps

Constraint:
- unique (`place_id`, `locale`)

---

### 5. `entity_translations`
Multilingual human-readable layer за `entities`.

Одобрени колони:
- `id`
- `entity_id`
- `locale`
- `name`
- `address`
- `description`
- timestamps

Constraint:
- unique (`entity_id`, `locale`)

Бележка:
- `address` тук е display/raw address text
- не е normalized address model

---

### 6. `accommodation_details`
One-to-one accommodation-specific extension за accommodation entities.

Одобрени колони:
- `id`
- `entity_id`
- `star_rating`
- `check_in_from`
- `check_in_to`
- `check_out_from`
- `check_out_to`
- timestamps

Constraint:
- unique (`entity_id`)

Бележка:
- това е type-specific таблица
- не е универсален слой за всички entity типове

---

### 7. `amenities`
Lookup таблица за canonical amenity codes.

Одобрени колони:
- `id`
- `code`
- timestamps

Одобрен seed set:
- `wifi`
- `parking`
- `restaurant`
- `bar`
- `pool`
- `spa`
- `fitness_center`
- `air_conditioning`
- `heating`
- `breakfast`
- `room_service`
- `airport_transfer`
- `pet_friendly`
- `family_friendly`
- `non_smoking_rooms`
- `facilities_for_disabled_guests`
- `private_bathroom`
- `balcony`
- `kitchen`
- `sea_view`
- `mountain_view`
- `beach_access`
- `garden`
- `terrace`

---

### 8. `amenity_translations`
Multilingual display layer за `amenities`.

Одобрени колони:
- `id`
- `amenity_id`
- `locale`
- `name`
- timestamps

Constraint:
- unique (`amenity_id`, `locale`)

---

### 9. `entity_amenities`
Pure pivot bridge между `entities` и `amenities`.

Одобрени колони:
- `id`
- `entity_id`
- `amenity_id`
- timestamps

Constraint:
- unique (`entity_id`, `amenity_id`)

Бележка:
- pure pivot
- без metadata полета
- без value/notes/source/priority/conditions

---

### 10. `entity_media`
Универсален media-reference слой за всички entity типове.

Одобрени колони:
- `id`
- `entity_id`
- `type`
- `path`
- `url`
- `is_cover`
- `sort_order`
- timestamps

Одобрени `type` стойности:
- `image`
- `video`

Бележки:
- `path` е за вътрешно съхранени media assets
- `url` е за външни media references, включително линкнати видеа
- таблицата е универсална за accommodation, restaurants, attractions, services и future entity types
- няма `source_type`, `provider`, `embed_code`, `caption`, `alt_text`, `thumbnail`, `status`

---

### 11. `food_place_details`
Lean type-specific extension за food-place entities.
1:1 extension на `entities` record за food домейна.

Одобрени колони:
- `id`
- `entity_id`
- `accepts_reservations`
- `takeaway_available`
- `delivery_available`
- `serves_breakfast`
- `serves_lunch`
- `serves_dinner`
- `price_range`
- timestamps

Constraint:
- unique (`entity_id`)

Бележки:
- boolean флагове с default `false`
- `price_range` е nullable string — не е DB enum
- FK поведение: restrictive (без cascade delete)
- без cuisine, menu_url, opening hours, seating, entertainment полета

---

### 12. `attraction_details`
Lean type-specific extension за attraction entities.
1:1 extension на `entities` record за attraction домейна.

Одобрени колони:
- `id`
- `entity_id`
- `is_natural`
- `is_cultural`
- `is_indoor`
- `is_outdoor`
- `is_free`
- `has_entry_fee`
- `estimated_visit_minutes`
- `is_family_friendly`
- `is_accessible`
- `is_seasonal`
- timestamps

Constraint:
- unique (`entity_id`)

Бележки:
- всички boolean флагове са nullable — много attraction атрибути са легитимно неизвестни
- `estimated_visit_minutes` е nullable unsignedSmallInteger
- FK поведение: restrictive (без cascade delete)
- без opening hours, ticket pricing, heritage registry, booking логика

---

### 13. `entity_contacts`
Универсален слой за контактни точки на всеки entity.

Одобрени колони:
- `id`
- `entity_id`
- `type`
- `value`
- `is_primary`
- timestamps

Одобрени `type` стойности:
- `phone`
- `mobile`
- `email`
- `viber`
- `whatsapp`

Бележки:
- универсална таблица — не е domain-specific
- без `sort_order`
- без contact labels, verified flags, person names, time-valid contacts
- FK поведение: restrictive (без cascade delete)

---

## Одобрен следващ пакет (не е имплементиран още)

Следните таблици са одобрени като следващ Phase 1 пакет.
Те **не са имплементирани** към момента на този checkpoint.

### Next-1. `entity_links`
Универсален слой за external URL присъствие на всеки entity.

Одобрени колони:
- `id`
- `entity_id`
- `type`
- `url`
- `is_primary`
- timestamps

Одобрени `type` стойности:
- `website`
- `facebook`
- `instagram`
- `tiktok`
- `youtube`
- `menu`
- `booking`

Бележки:
- универсална таблица — не е domain-specific
- без `sort_order`
- `map` е изключен — координатите вече са в `entities` (`lat`/`lng`)
- не се слива с `entity_sources`

---

### Next-2. `entity_sources`
Универсален provenance / source-of-truth слой за всеки entity.

Одобрени колони:
- `id`
- `entity_id`
- `source_type`
- `source_url`
- `is_official`
- `first_seen_at`
- `last_seen_at`
- timestamps

Одобрени `source_type` стойности:
- `official_website`
- `social_profile`
- `manual_entry`
- `third_party_listing`

Бележки:
- универсална таблица — не е domain-specific
- без confidence score, crawl state, parser version, raw payload, per-field provenance
- не се слива с `entity_links`

---

## FK backfill статус

Добавени и одобрени са deferred foreign keys към `entities`:

- `entities.entity_type_id -> entity_types.id`
- `entities.place_id -> places.id`

Поведение:
- restrictive default FK behavior
- без cascade delete

---

## Какво съзнателно НЕ е включено в този checkpoint

Не са включени в тази фаза (все още извън scope):

- `entity_price_signals`
- normalized address layer
- services-specific details
- moderation/workflow layers
- provider/platform-specific media logic
- SEO/meta layers
- localized slugs
- belongsToMany convenience relations
- admin/UI logic
- relation layer
- claim/ownership layer
- events domain
- facets/classification layer

Съзнателно отложено за food-place домейна:
- cuisine / кухня
- теми и концепции на заведенията
- nightlife / entertainment venue expansion
- opening hours / работно време
- hotel-food relation layer
- по-богата feature taxonomy

Съзнателно отложено / отхвърлено за attraction домейна:
- исторически периоди и култури като schema типове (roman, thracian, medieval, proto_bulgarian, revival)
- catch-all / garbage-bucket типове (other_historical, other_cultural, other_natural)
- route model типове (eco_trail, trail, bike_route, route)
- activity / facility / infrastructure типове (golf_course, water_park, karting_track, airport)
- opening hours / работно време
- ticket pricing структури
- legal heritage registry структури
- booking / reservation логика

---

## Current approved direction

Текущата schema е:

- достатъчно lean за старт
- достатъчно обща за future expansion
- без заключване в стария хотелски модел
- с ясно отделени structural, translation, amenity, media и type-specific слоеве
- покрива accommodation домейна (9 типа), food-place домейна (8 типа) и attraction домейна (22 типа)
- type-specific extension pattern е доказан с три имплементирани detail tables
- универсален contact layer е имплементиран (`entity_contacts`)

---

## Следваща посока

Одобреният следващ имплементационен пакет е:
- `entity_links`
- `entity_sources`

Вижте секция "Одобрен следващ пакет" по-горе за точните колони и одобрени стойности.

След имплементацията на този пакет, следващите архитектурни решения трябва да продължат по същия модел:
- table-by-table
- без overengineering
- без premature abstractions
- само при ясно одобрени boundaries и реална нужда
