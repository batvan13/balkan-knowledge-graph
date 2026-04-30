# BKG Phase 1 — STRICT ARCHITECTURE RULES FOR CURSOR

## PURPOSE

This document defines the hard boundaries for Phase 1 of BKG.

Cursor must treat this document as an implementation guardrail.

Phase 1 is **not** a full Balkan knowledge graph.
Phase 1 is **not** a booking platform.
Phase 1 is **not** a marketplace.
Phase 1 is **not** a public API product.

Phase 1 is:

**an accommodation-first Balkan catalog with a central entity model, multilingual-ready structure, and a clean database foundation that can later evolve into a dataset.**

Everything outside this scope is forbidden unless explicitly approved.

---

# 1. NON-NEGOTIABLE PROJECT RULES

## Rule 1 — Central object model is mandatory
The project must use **one central table for objects**:

- `entities`

Do not create separate main object tables such as:
- `hotels`
- `restaurants`
- `attractions`
- `services`

That old fragmented pattern is forbidden in BKG Phase 1.

---

## Rule 2 — Common data stays in `entities`
Only common object fields belong in `entities`.

Examples of common fields:
- owner
- type
- place
- status
- website
- email
- phone

Do not place type-specific characteristics in `entities`.

---

## Rule 3 — Type-specific fields go into detail tables
Accommodation-specific data must go into:

- `accommodation_details`

Do not mix accommodation-specific fields into `entities`.

Phase 1 supports only the accommodation domain in implementation.

---

## Rule 4 — Reuse unified tables
Use unified tables for shared concepts.

Allowed unified model:
- one central entities table
- one places table
- one amenities table
- one media table
- one translations pattern

Do not recreate fragmented old-style tables like:
- `hotel_extras`
- `hotel_images`
- `restaurant_extras`
- `service_images`
- `video_galleries`
- `menu_galleries`

Those patterns are forbidden unless explicitly approved later.

---

## Rule 5 — Accommodation-first implementation only
Phase 1 implementation scope is accommodation only.

Architecture may remain extendable,
but implementation must not expand into:
- restaurants
- attractions
- services
- events
- reviews
- claims
- bookings
- offers
- marketplace logic

No silent scope expansion is allowed.

---

# 2. MULTILINGUAL RULES

## Rule 6 — No fixed language columns
Do not use columns like:
- `name_bg`
- `name_en`
- `description_bg`
- `description_en`

This is forbidden.

Multilingual data must not be modeled with per-language columns.

---

## Rule 7 — Translation tables are required
Multilingual content must be stored in translation tables.

Approved translation tables:
- `entity_translations`
- `place_translations`
- `amenity_translations`

No alternative multilingual shortcut is allowed.

---

## Rule 8 — Slugs are locale-specific
Slug must be stored per language inside translation tables.

Do not use a single global slug as the final multilingual model.

---

## Rule 9 — Architecture must support future multilingual SEO
The schema must support per-language:
- name
- slug
- description
- SEO fields

Even if some SEO behavior is implemented later, the structure must support it from the start.

---

# 3. OWNERSHIP AND AUTH RULES

## Rule 10 — Use Laravel `users`
Object ownership must be tied to the standard Laravel `users` table.

Use relation such as:
- `owner_id` in `entities`

Do not introduce a separate owners system in Phase 1.

---

## Rule 11 — No Spatie, no permission packages, no auth complexity
Use standard Laravel auth only.

Forbidden unless explicitly approved:
- Spatie Permission
- admin packages
- roles/permissions packages
- advanced access-control systems

Keep auth simple.

---

# 4. ADMIN AND IMPLEMENTATION RULES

## Rule 12 — Admin UI must follow the approved database model
Admin forms and CRUD must follow the approved schema.

Do not invent UI structures that force a different database architecture.

---

## Rule 13 — Reuse proven workflow, not old structural mistakes
Old project experience may be reused only as:
- CRUD workflow
- form grouping
- practical admin UX
- data-entry logic

Do not copy old schema fragmentation.

Reuse operational lessons.
Do not reuse structural debt.

---

## Rule 14 — No overengineering
The following are forbidden in Phase 1 unless explicitly requested:
- graph engines
- polymorphic universal node systems
- microservices
- CQRS
- DDD-heavy layering
- repository pattern without need
- service layers without need
- speculative abstractions

Use plain Laravel application structure.

---

## Rule 15 — No package additions without explicit approval
Do not install packages unless directly approved.

Preference order:
1. Laravel native solution
2. simple custom implementation
3. package only if clearly necessary

---

## Rule 16 — No future systems in advance
Do not implement speculative systems for:
- owner claims
- moderation workflows
- public API monetization
- AI pipelines
- automatic translation systems
- scraping infrastructure
- graph relation engines
- event systems
- review systems

If not explicitly requested now, do not build it now.

---

# 5. APPROVED PHASE-1 TABLE SET

Only the following base tables are approved for current planning.

## Required now

### 1. `users`
Purpose:
- system users
- object owners
- admin authentication base

### 2. `entity_types`
Purpose:
- controlled list of object types

### 3. `places`
Purpose:
- geographical location structure

### 4. `place_translations`
Purpose:
- multilingual place names and slugs

### 5. `entities`
Purpose:
- central object record for all objects

### 6. `entity_translations`
Purpose:
- multilingual object content and slugs

### 7. `accommodation_details`
Purpose:
- accommodation-specific structured characteristics

### 8. `amenities`
Purpose:
- controlled amenity dictionary

### 9. `amenity_translations`
Purpose:
- multilingual amenity labels

### 10. `entity_amenities`
Purpose:
- pivot table between entities and amenities

### 11. `entity_media`
Purpose:
- object gallery and media items

## Allowed later, but not mandatory for first base pass

### 12. `entity_price_signals`
Purpose:
- observed or owner-provided price range signals

### 13. `entity_sources`
Purpose:
- source tracking / provenance for imported or observed data

No extra tables may be added without approval.

---

# 6. FORBIDDEN TABLE PATTERNS

The following patterns are forbidden in Phase 1:

- separate main tables per domain (`hotels`, `restaurants`, etc.)
- separate extras tables per domain
- separate media tables per domain
- separate language columns per field
- separate owner system outside Laravel users
- speculative relation engines
- speculative API token systems
- speculative translation infrastructure
- speculative claim/review/event systems

If Cursor proposes such structures, they must be rejected.

---

# 7. APPROVED DEVELOPMENT ORDER

Cursor must respect this exact order:

## Step 1
Confirm the approved table set only.

## Step 2
Define exact columns for each approved table.

## Step 3
Create migrations only after schema approval.

## Step 4
Create Eloquent models and relations.

## Step 5
Create admin CRUD according to the approved schema.

## Step 6
Create Laravel auth/admin access using standard Laravel tools.

## Step 7
Only after that, discuss further expansion.

Do not skip steps.
Do not jump to implementation outside the current approved step.

---

# 8. REQUIRED CURSOR BEHAVIOR

Cursor must:
- stay inside approved scope
- ask before expanding scope
- avoid architectural invention
- avoid premature generalization
- avoid unrequested refactors
- prefer explicit simple Laravel code
- preserve clarity over cleverness

Cursor must not:
- redesign the project
- silently expand scope
- introduce abstractions “for the future”
- install packages without approval
- split the system into unnecessary layers
- create extra tables because they “might be useful later”

---

# 9. FINAL PHASE-1 DEFINITION

BKG Phase 1 must be treated as:

**a practical accommodation-first Balkan catalog, built on a central entity model, multilingual-ready schema, unified shared tables, and simple Laravel architecture, with a clean path for future dataset evolution.**

This is the boundary.

Anything outside this boundary is out of scope unless explicitly approved.
