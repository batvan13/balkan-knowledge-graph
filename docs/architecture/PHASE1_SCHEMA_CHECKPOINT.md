# BKG Phase 1 Schema Checkpoint

## Статус

Този документ описва текущото одобрено и имплементирано състояние на Phase 1 базовата schema архитектура за BKG (Balkan Knowledge Graph).

Phase 1 е проектирана като:

- accommodation-first старт
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
- `hotel`
- `guesthouse`
- `apartment`
- `house`
- `villa`
- `hostel`
- `bungalow`
- `camping`
- `lodge`

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

## FK backfill статус

Добавени и одобрени са deferred foreign keys към `entities`:

- `entities.entity_type_id -> entity_types.id`
- `entities.place_id -> places.id`

Поведение:
- restrictive default FK behavior
- без cascade delete

---

## Какво съзнателно НЕ е включено в този checkpoint

Не са включени в тази фаза:

- `entity_price_signals`
- `entity_sources`
- contact-specific layer
- normalized address layer
- restaurant-specific details
- attraction-specific details
- services-specific details
- moderation/workflow layers
- provider/platform-specific media logic
- SEO/meta layers
- localized slugs
- belongsToMany convenience relations
- admin/UI logic

---

## Current approved direction

Текущата schema е:

- достатъчно lean за старт
- достатъчно обща за future expansion
- без заключване в стария хотелски модел
- с ясно отделени structural, translation, amenity, media и type-specific слоеве

---

## Следваща посока

След този checkpoint следващите архитектурни решения трябва да продължат по същия модел:

- table-by-table
- без overengineering
- без premature abstractions
- само при ясно одобрени boundaries и реална нужда
