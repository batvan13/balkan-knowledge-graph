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

- accommodation-first
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
- This is the main type-specific detail table for Phase 1.
- It must be linked to `entities`.
- Keep it practical and scoped to real accommodation needs.

### Forbidden drift
- putting these fields in `entities`
- turning this table into a second main object table
- adding fields for restaurants/attractions/services in Phase 1

---

## 2.8 `amenities`
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

## 2.9 `amenity_translations`
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

## 2.10 `entity_amenities`
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

## 2.11 `entity_media`
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

## 3.2 `entity_sources`
### Role
Source/provenance tracking table.

### Purpose
Store where certain data came from:
- owner-submitted
- official website
- manual research
- other approved source types

### Notes
- Strongly useful for future dataset evolution
- Can wait slightly, but should remain planned

### Forbidden drift
- fake certainty with no source trail
- untracked imported factual data

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
`accommodation_details` must contain only accommodation-specific data.

Do not use detail tables as generic junk drawers.

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

- one central `entities` table
- one multilingual translation pattern
- one places backbone
- one accommodation detail table
- one unified amenities system
- one unified media system
- Laravel users as ownership/auth base
- simple structure now, expandable later

This is the approved foundation.

Anything outside it must be explicitly approved before implementation.
