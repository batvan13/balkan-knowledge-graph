# PHASE1_APPROVED_TABLES.md

## PURPOSE

This document defines the **approved Phase 1 table set** for BKG.

It exists to prevent schema drift, overengineering, old-project fragmentation, and unapproved abstractions.

This document must be read together with:

- `BKG_PHASE1_CURSOR_RULES.md`

If a table, column group, or structural idea is not aligned with this document, it must not be implemented without explicit approval.

---

# 1. PHASE 1 SCOPE

Phase 1 implementation scope is:

- accommodation-first foundation, with food-place and attraction domain expansions approved
- central entity model
- multilingual-ready
- admin-managed
- Laravel-native
- simple and maintainable

Phase 1 is **not**:
- full Balkan graph engine
- booking engine
- reviews platform
- owner claim system
- event platform
- public API platform
- monetization/token system

---

# 2. APPROVED TABLES

## 2.1 `users`
### Role
Base table for:
- authentication
- admins
- object owners

### Notes
- Use standard Laravel users table as the ownership base.
- Do not introduce separate `owners` table in Phase 1.
- Admin access must also rely on the same base user system unless explicitly changed later.

### Allowed responsibility
- login/authentication identity
- ownership relation to entities
- admin access base

### Forbidden drift
- separate owner profile system
- complex roles/permissions packages
- parallel authentication model

---

## 2.2 `entity_types`
### Role
Controlled dictionary for object types.

### Initial usage
Phase 1 implementation is accommodation-first, but the type layer exists to keep the central entity model expandable.

### Allowed responsibility
- holds canonical type codes/names
- supports filtering and type binding

### Notes
- Keep simple.
- No speculative hierarchy unless explicitly needed.

### Forbidden drift
- overdesigned taxonomy engine
- deep inheritance model
- type-specific logic stored here

---

## 2.3 `places`
### Role
Canonical geographical location table.

### Purpose
Every entity belongs to a place.
This table is the location backbone of the catalog.

### Allowed responsibility
- place hierarchy
- country-aware expansion
- geographic structure
- optional coordinates

### Notes
- This replaces old single-country/towns-only thinking.
- Must support future Balkan country expansion cleanly.
- Keep it practical, not theoretical.

### Forbidden drift
- stuffing place text directly into entities as the only place model
- creating separate country/city/village tables too early
- complex geo engine abstractions

---

## 2.4 `place_translations`
### Role
Multilingual layer for places.

### Purpose
Store per-locale versions of place names and slugs.

### Allowed responsibility
- translated place name
- locale-specific slug
- locale-specific public display text

### Notes
- Required because the system is multilingual by structure.
- No fixed per-language columns in `places`.

### Forbidden drift
- `name_bg`, `name_en`, `name_de` in `places`
- storing all place translation logic outside DB
- single global slug only

---

## 2.5 `entities`
### Role
Central object table.

### Purpose
All objects must start here.
This is the base record for every catalog object.

### Allowed responsibility
Common, language-independent object data only.

Examples:
- owner
- type
- place
- status
- website
- email
- phone
- timestamps

### Notes
- Do not place multilingual text here.
- Do not place accommodation-specific characteristics here.
- Do not use this as a dump table.

### Forbidden drift
- putting all fields into `entities`
- placing translated content directly in `entities`
- placing accommodation-specific fields here
- reintroducing separate main domain tables

---

## 2.6 `entity_translations`
### Role
Multilingual object content table.

### Purpose
Store per-locale object content and locale-specific slug.

### Allowed responsibility
Per language:
- object name
- object slug
- description
- address text
- SEO fields

### Notes
- This table is mandatory.
- It replaces old fixed-language-column design.
- It must support future language expansion without schema changes.

### Important
Slugs are locale-specific and belong here.

### Forbidden drift
- fixed language columns in `entities`
- separate slug-only language table as a shortcut
- runtime-only multilingual logic with no persisted translation rows

---

## 2.7 `accommodation_details`
### Role
Accommodation-specific structured data table.

### Purpose
Holds the characteristics that belong only to accommodation entities.

### Allowed responsibility
Examples:
- subtype/category within accommodation domain
- room/capacity/beds style structured characteristics
- accommodation-specific factual fields

### Notes
- This is the type-specific detail table for accommodation domain.
- It must be linked to `entities`.
- Keep it practical and scoped to real accommodation needs.

### Forbidden drift
- putting these fields in `entities`
- turning this table into a second main object table
- adding food-place or other domain fields here

---

## 2.8 `food_place_details`
### Role
Food-place-specific structured data table.

### Purpose
Lean, food-domain-specific extension for food-related entities.
1:1 extension of the `entities` record — one row per food-place entity.

### Allowed responsibility
- service mode flags (reservations, takeaway, delivery)
- meal period flags (breakfast, lunch, dinner)
- price range signal

### Approved columns
- `id`
- `entity_id` (unique FK to `entities.id`)
- `accepts_reservations`
- `takeaway_available`
- `delivery_available`
- `serves_breakfast`
- `serves_lunch`
- `serves_dinner`
- `price_range` (nullable string, not DB enum)
- timestamps

### Approved food-place entity type codes
These codes are seeded in `entity_types` via `EntityTypeSeeder`:
- `restaurant`
- `tavern`
- `bar`
- `pub`
- `cafe`
- `bistro`
- `fast_food`
- `pastry_shop`

### Notes
- Structured boolean flags only — no free-text fields.
- `price_range` is a nullable string. Application layer enforces allowed values: `budget`, `midrange`, `premium`, `luxury`.
- FK is restrictive by default (no cascade delete).

### Forbidden drift
- adding cuisine columns here
- adding menu_url, average_bill, seating capacity
- adding opening hours
- adding entertainment/live_music/smoking flags
- adding hotel linkage field
- turning this into a second `entities` table
- mixing food-place fields into `accommodation_details` or `entities`

---

## 2.9 `attraction_details`
### Role
Attraction-specific structured data table.

### Purpose
Lean, type-specific extension for attraction entities.
1:1 extension of the `entities` record — one row per attraction entity.

### Allowed responsibility
- nature / culture classification flags
- indoor / outdoor flags
- entry fee flags
- estimated visit duration
- accessibility and audience flags
- seasonal flag

### Approved columns
- `id`
- `entity_id` (unique FK to `entities.id`)
- `is_natural` (nullable boolean)
- `is_cultural` (nullable boolean)
- `is_indoor` (nullable boolean)
- `is_outdoor` (nullable boolean)
- `is_free` (nullable boolean)
- `has_entry_fee` (nullable boolean)
- `estimated_visit_minutes` (nullable unsigned small integer)
- `is_family_friendly` (nullable boolean)
- `is_accessible` (nullable boolean)
- `is_seasonal` (nullable boolean)
- timestamps

### Approved attraction entity type codes
These codes are seeded in `entity_types` via `EntityTypeSeeder`:
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

### Important exclusions
Old attraction dropdown categories from the legacy system were reviewed and rejected as direct schema truth.
The following were intentionally NOT adopted:
- historical period / culture labels (`ancient`, `roman`, `thracian`, `medieval`, `proto_bulgarian`, `revival`)
- garbage-bucket catch-all types (`other_historical`, `other_cultural`, `other_natural`, `other_modern`)
- route model types (`eco_trail`, `trail`, `bike_route`, `route`)
- activity / facility / infrastructure types (`golf_course`, `paintball_field`, `water_park`, `karting_track`, `lift_station`, `airport`, `sports_attraction`)
- wrong-domain crossovers (`camping`, `hut`, `shelter`)

### Notes
- Boolean flags are nullable — many attraction attributes are legitimately unknown.
- FK is restrictive by default (no cascade delete).

### Forbidden drift
- historical period / culture classification in schema
- opening hours logic
- ticket pricing structures
- legal heritage registry structures
- booking / reservation logic
- theme / audience / marketing classification
- route tables
- additional attraction-related tables beyond this one

---

## 2.10 `amenities`
### Role
Controlled amenity dictionary.

### Purpose
Canonical machine-readable list of amenities/features.

### Allowed responsibility
- amenity code/key
- amenity grouping if needed later
- shared amenity dictionary across entities

### Notes
- One unified amenities table only.
- Do not recreate old fragmented extras pattern.

### Forbidden drift
- `hotel_extras`
- `restaurant_extras`
- per-domain extras dictionaries
- duplicated amenity structures

---

## 2.11 `amenity_translations`
### Role
Multilingual amenity label table.

### Purpose
Store translated human-readable labels for amenities.

### Allowed responsibility
- amenity label per locale
- multilingual UI-ready amenity display

### Notes
- Keep codes machine-readable in `amenities`
- Keep translated labels here

### Forbidden drift
- storing labels only in one language
- storing all translations in the base amenities table
- translating amenities only on the frontend

---

## 2.12 `entity_amenities`
### Role
Pivot table between entities and amenities.

### Purpose
Connect catalog objects to their available amenities.

### Allowed responsibility
- many-to-many relation between entities and amenities

### Notes
- Keep this simple.
- Do not overcomplicate with speculative value systems unless explicitly needed later.

### Forbidden drift
- per-domain pivot tables
- denormalized amenity columns inside entities/details
- separate extras relation model per object type

---

## 2.13 `entity_media`
### Role
Unified gallery/media table for entities.

### Purpose
Store media items related to an object.

### Allowed responsibility
- images
- videos if approved in the same unified structure
- ordering/sorting
- object gallery records

### Notes
- One unified media table only.
- Do not split media by old domain/form-specific habits.

### Forbidden drift
- `image_galleries`
- `video_galleries`
- `menu_galleries`
- per-domain gallery tables

---

## 2.14 `entity_contacts`
### Role
Universal contact points table for any entity.

### Purpose
Store structured contact information per entity, independent of domain.

### Approved fields
- `id`
- `entity_id` (FK to `entities.id`)
- `type`
- `value`
- `is_primary`
- `created_at`
- `updated_at`

### Approved type values
- `phone`
- `mobile`
- `email`
- `viber`
- `whatsapp`

### Notes
- Universal — not domain-specific.
- `is_primary` identifies the canonical contact per type.
- No `sort_order` — not needed at Phase 1.
- FK is restrictive by default (no cascade delete).

### Forbidden drift
- contact labels (reception, manager, bookings)
- verified/moderated flags
- person or role names
- time-valid contacts (valid_from / valid_to)
- locale-specific contact text
- internal extensions or department codes
- domain-specific contact tables

---

## 2.15 `entity_links`
### Role
Universal external URLs table for any entity.

### Purpose
Store structured external presence links per entity, independent of domain.

### Approved fields
- `id`
- `entity_id` (FK to `entities.id`)
- `type`
- `url`
- `is_primary`
- `created_at`
- `updated_at`

### Approved type values
- `website`
- `facebook`
- `instagram`
- `tiktok`
- `youtube`
- `menu`
- `booking`

### Notes
- Universal — not domain-specific.
- `is_primary` identifies the canonical link per type.
- No `sort_order` — not needed at Phase 1.
- `map` is excluded — coordinates already exist on `entities` (`lat`/`lng`).
- FK is restrictive by default (no cascade delete).

### Forbidden drift
- SEO metadata fields
- follower or engagement metrics
- domain extraction columns
- platform account IDs
- click tracking or analytics
- embed metadata
- merging with `entity_sources`

---

# 2A. APPROVED NEXT UNIVERSAL PACKAGE

These tables are **approved** as the next Phase 1 implementation package.
They are **not yet implemented**.

## 2A.1 `entity_sources`
### Role
Universal provenance / source-of-truth layer for any entity.

### Purpose
Store where entity data came from, supporting methodological honesty about data origin.

### Approved fields
- `id`
- `entity_id` (FK to `entities.id`)
- `source_type`
- `source_url`
- `is_official`
- `first_seen_at`
- `last_seen_at`
- `created_at`
- `updated_at`

### Approved source_type values
- `official_website`
- `social_profile`
- `manual_entry`
- `third_party_listing`

### Notes
- Universal — not domain-specific.
- `is_official` is a lean binary provenance flag.
- `first_seen_at` / `last_seen_at` are provenance timestamps, not crawl-state metadata.
- Must NOT be merged with `entity_links`.

### Forbidden drift
- confidence scores
- crawl state flags
- parser version tracking
- raw payload storage
- per-field provenance
- source weighting or priority engine
- scraper job references

---

# 3. APPROVED LATER TABLES

These are allowed later in Phase 1.5 or later Phase 1 expansion,
but are not mandatory in the first base pass.

## 3.1 `entity_price_signals`
### Role
Observed or owner-provided price range records.

### Purpose
Store price signals in a structured way without pretending they are always authoritative truth.

### Notes
- Price logic is sensitive and must remain methodologically honest.
- Better to store observed/declared price signals than false certainty.

### Forbidden drift
- pretending prices are always exact and universally valid
- embedding all price logic inside entities/details without provenance

---

# 4. COLUMN DESIGN PRINCIPLES

This document does not yet lock final columns one by one,
but the following design principles are mandatory.

## Principle 1 — `entities` contains only language-independent fields
Examples of fields that belong in `entities`:
- owner relation
- type relation
- place relation
- status
- website
- email
- phone
- timestamps

Examples of fields that do **not** belong in `entities`:
- translated name
- translated description
- translated slug
- translated address text
- accommodation-specific characteristics

---

## Principle 2 — translations must be row-based, not column-based
Approved pattern:
- one row per locale per object

Forbidden pattern:
- one big table with `*_bg`, `*_en`, `*_de`, etc.

---

## Principle 3 — unified tables are preferred over fragmented copies
Preferred:
- one amenities table
- one media table
- one translations pattern

Forbidden:
- per-domain duplicates of the same concept

---

## Principle 4 — detail tables must stay type-specific
Each detail table must contain only data specific to its own domain.

- `accommodation_details` — accommodation domain only
- `food_place_details` — food-place domain only
- `attraction_details` — attraction domain only

Do not use detail tables as generic junk drawers.
Do not mix domain-specific fields across detail tables.

---

# 5. FORBIDDEN STRUCTURAL DRIFT

The following are explicitly forbidden in Phase 1 unless approved:

- separate main domain tables
- fixed-language columns
- separate owners system
- per-domain extras/media tables
- speculative graph engine tables
- speculative API token tables
- reviews tables
- claims tables
- event tables
- booking tables
- moderation workflow tables
- overbuilt hierarchy systems
- package-driven architecture decisions without approval

---

# 6. REQUIRED IMPLEMENTATION ORDER

Cursor must respect this order:

## Step 1
Approve exact table set

## Step 2
Approve exact columns for each approved table

## Step 3
Create migrations

## Step 4
Create models and relationships

## Step 5
Create admin CRUD

## Step 6
Create Laravel auth/admin access

## Step 7
Only then discuss later extensions

No implementation should skip this order.

---

# 7. FINAL PHASE-1 DATABASE POSITION

The approved Phase 1 database direction is:

**Currently implemented:**
- one central `entities` table
- one multilingual translation pattern
- one places backbone
- one accommodation detail table (`accommodation_details`)
- one food-place detail table (`food_place_details`)
- one attraction detail table (`attraction_details`)
- one unified amenities system
- one unified media system
- one universal contact points table (`entity_contacts`)
- one universal external links table (`entity_links`)
- Laravel users as ownership/auth base

**Approved next (not yet implemented):**
- one universal source provenance table (`entity_sources`)

This is the approved foundation.

Anything outside it must be explicitly approved before implementation.
