# BULGARIA PLACES IMPORT STRATEGY

---

## 1. Purpose and scope

This document defines the import strategy for populating Bulgaria places in BKG from the legacy `towns.sql` source. It is a policy and architecture decision document. It does not contain implementation steps, scripts, or code.

All implementation planning for place import must conform to this document before proceeding.

---

## 2. Source assessment

### Why towns.sql is useful

- It provides broad raw coverage of Bulgarian settlements.
- It contains a clear, consistent `region` field usable as the foundation for the BKG region layer.
- It is an available, internally sourced dataset with known provenance.

### Why towns.sql is insufficient for direct import

- It contains no country node. Bulgaria as the root of the place hierarchy does not exist in the source and must be created independently.
- `municipality` is a legacy administrative construct. It is not part of the chosen minimal BKG place hierarchy and must not be introduced as an official layer to accommodate this source.
- The source provides no reliable classification of `city`, `village`, `resort`, or `area`. Settlement type cannot be inferred safely from the available fields.
- Coordinates are incomplete or absent for a significant number of rows and must be treated as untrustworthy unless explicitly verified.
- The source schema does not align 1:1 with the BKG places model.

---

## 3. Locked target hierarchy

The target BKG place hierarchy for this import effort is:

- **country**
- **region**
- **city / village / resort / area**

This hierarchy is fixed. It will not be modified or extended to accommodate the structure of the legacy source. The import process adapts to the hierarchy, not the reverse.

---

## 4. Phase 1 import scope

Phase 1 imports the following with high confidence:

**Bulgaria as country root**
A single `country` place record for Bulgaria is created as the root node of the hierarchy. This record does not come from `towns.sql` — it is constructed from known authoritative data.

**Unique region records**
The distinct values of the `region` field in `towns.sql` are imported as `region` place records, each linked as a child of the Bulgaria country root. Duplicates are deduplicated. Region slugs are generated from the Bulgarian region names.

Phase 1 produces a clean two-level structural skeleton: one country node and its direct region children. No settlements enter Phase 1.

---

## 5. Deferred scope / Phase 2 enrichment

The following is explicitly deferred until after Phase 1 is complete and a classification policy is defined:

- Import of individual settlement rows as typed place records.
- Distinction between `city`, `village`, `resort`, and `area` for any settlement.
- Resort and area identification, which requires domain context beyond what the source provides.
- Coordinate assignment and verification for settlement records.

Settlements from `towns.sql` must not be written into the BKG places table as final typed records until an explicit classification/enrichment step has been applied to each row. Raw `name` values from the source are transformation input, not ready-to-insert place records.

---

## 6. Field mapping rules

These rules apply to how legacy fields are interpreted during transformation planning. They are policy-level definitions, not implementation steps.

**`region`**
Maps to region-level place candidates under the Bulgaria country root. Distinct values yield distinct region records.

**`municipality`**
Retained only as legacy transformation context. It is preserved in traceability records to aid later settlement disambiguation. It does not map to any BKG place hierarchy layer and must not be inserted as a place record.

**`name`**
Treated as the raw source material for a future settlement record. The `name` value alone is insufficient to produce a typed, final BKG place row. It enters Phase 2 as input to the classification and enrichment step.

**`lat` / `long`**
Treated as optional and potentially incomplete. Where present and plausible, they may inform later place record creation. Where absent or null, they are not backfilled by assumption. Coordinates are not a blocking requirement for Phase 1.

---

## 7. Traceability rules

Every row from `towns.sql` that is consulted during transformation must be traceable back to its source. The following fields must be preserved in any staging or transformation record:

- Legacy row `id`
- Original `region` value
- Original `municipality` value
- Original settlement `name`
- Source identifier: `towns.sql / legacy import`

Traceability matters because:

- Settlement classification decisions in Phase 2 must be auditable against the original source row.
- Conflicts or duplicates discovered during Phase 2 must be resolvable without re-importing the source.
- Any place record created from this source must be linkable to its origin for correction or removal if needed.

---

## 8. Explicit non-goals and forbidden shortcuts

The following must not occur during any phase of this import:

- **No 1:1 direct import** from `towns.sql` rows into `places` table rows. The source requires transformation, not transcription.
- **No introduction of municipality as an official place layer.** Municipality must not appear as a `type` value in the BKG places model as a result of this import.
- **No blanket classification of all settlements as `city`.** This is a convenience shortcut that produces incorrect data requiring mass correction later.
- **No guess-based typing of resort or area.** Resort and area classification requires deliberate domain-level decisions, not automated inference from settlement names.
- **No assumption that source coordinates are complete or accurate.** Null and suspicious coordinates must not be silently treated as valid geodata.
- **No architecture drift to accommodate legacy convenience.** The BKG hierarchy and data model remain authoritative. The source adapts to the model.

---

## 9. Final recommendation

Use `towns.sql` as transformation input material, not as direct import material.

Execute Phase 1 first: create the Bulgaria country root and import the distinct region records. Review the imported region list for normalization consistency before final acceptance.

Before proceeding to settlement import, define a written settlement classification policy that specifies how each settlement row from the source will be typed as `city`, `village`, `resort`, or `area`. This policy is a prerequisite for Phase 2 — settlement import must not begin without it.

After the classification policy is approved, proceed to implementation planning for the Phase 2 settlement import.
