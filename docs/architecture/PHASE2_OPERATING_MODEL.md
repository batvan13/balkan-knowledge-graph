# PHASE 2 OPERATING MODEL

## 1. Purpose and Scope

This document defines the operating rules governing how BKG accepts, evaluates, deduplicates, publishes, updates, and governs entity data in Phase 2.

It covers the policy layer for:
- ingestion channels and candidate lifecycle
- source trust and provenance requirements
- deduplication policy
- publish criteria
- conflict resolution
- data freshness and update model
- owner submission and owner claim model
- minimal administration governance

This document does **not** define implementation details. It does not describe schemas, migrations, services, queue architectures, scraper mechanics, or UI flows. Its purpose is to reduce operating-model ambiguity before those implementation decisions are made.

---

## 2. Core Operating Principles

1. **Source-backed over unattributed.** Every publishable fact must be traceable to one or more accepted sources. Unsourced data is not publishable.
2. **Candidate first, publish later.** No discovered or submitted entity becomes public without passing review criteria. Discovery is never direct publication.
3. **No uncontrolled publication from scrape results.** Scraped or crawled data is always a candidate. It requires normalization, deduplication check, and review before any public state.
4. **Provenance must remain traceable.** The origin of a published fact must remain attributable at all times. Overwriting provenance silently is not allowed.
5. **Deduplication precedes publication.** Any new candidate must pass a deduplication check before a new entity record may be created publicly.
6. **Owner input is important but not automatically authoritative.** Owner-submitted data carries strong weight for identity and contact fields. It does not automatically override accepted source data for classification or geographic fields without review.
7. **Unresolved critical conflicts block trusted publication.** An entity with an open conflict on a critical field (type, place, identity) may not advance to published status until the conflict is resolved.
8. **Stability over velocity.** A correct, well-sourced catalog of fewer entities is preferred over a large, low-confidence catalog.

---

## 3. Allowed Primary Data Channels

| Channel | Status |
|---|---|
| Official entity website | Allowed |
| Owner direct submission of a new entity | Allowed with review |
| Owner claim over an existing entity | Allowed with verification |
| Manual admin research and entry | Allowed |
| Scrape-discovered candidate from an explicitly trusted source | Allowed with review; not sufficient alone for publication |
| Third-party structured source (e.g. registry, directory) | Supporting only; allowed if source is explicitly trusted |
| Unverified third-party or anonymous tip | Not sufficient alone; supporting signal only |

No primary data channel bypasses publication governance.

---

## 4. Source Trust and Provenance Discipline

Trust is assigned at the source level, not the data-point level. The classification is:

- **Authoritative** — The entity itself (official website, official registry entry). Facts from authoritative sources are accepted unless contradicted by a higher-confidence authoritative source.
- **Strong** — Verified owner submission or verified owner claim. Elevated trust for identity, contact, and operational fields. Not automatically authoritative for place or type classification.
- **Supporting** — Trusted third-party directories, structured public datasets explicitly approved for use. Contributes to confidence but does not alone satisfy publish criteria.
- **Weak** — Unverified scrape results, unverified third-party content, or aggregator data without explicit trust designation. Useful for discovery and candidate generation only.
- **Insufficient alone** — Anonymous user tips, unverified form submissions without further corroboration.

Publishable data must be attributable to at least one Authoritative or Strong source, or to converging Supporting sources without contradicting signals.

Provenance records must be retained. Removing source attribution from a published entity is not permitted without a documented reason.

---

## 5. Candidate Ingestion Pipeline

All data enters through a staged operating model:

1. **Discovered** — Raw data or signal arrives from any allowed channel. No public entity exists or is modified at this stage.
2. **Normalized** — Data is cleaned, structured, and mapped to the entity model. Mandatory fields are identified. Incomplete candidates are held, not discarded.
3. **Filtered** — Candidates that clearly do not meet minimum entity criteria (e.g. no identifiable place, no classifiable type) are rejected at this stage.
4. **Dedupe-checked** — Candidate is evaluated against existing entities per Section 6. Candidates with duplicate concerns do not advance without admin resolution.
5. **Review-ready** — Candidate meets minimum criteria and has cleared the deduplication check. It is available for admin review and publication decision.
6. **Outcome** — One of:
   - **Published** — Meets all publish criteria; admin confirms.
   - **Rejected** — Does not meet criteria.
   - **Merged** — Determined to be a duplicate; merged into existing entity with provenance retained.
   - **Held** — Criteria partially met; missing data or unresolved conflict; may advance once resolved.

No candidate advances to published status without explicit admin confirmation.

---

## 6. Deduplication Policy

**Strongest duplicate signals:**
- Identical stable public identifier already attributed to an existing entity
- Matching canonical-language name + same place + same entity type family
- Identical official website URL already attributed to an existing entity

**Supporting duplicate signals:**
- Same place + same entity type + similar name across languages
- Shared contact detail (phone or email) already attributed to an existing entity
- Shared social profile URL already attributed to an existing entity

**Policy:**
- A match on any strongest signal requires admin review before a new entity may be created. The outcome is rejection or merge; automatic creation is not permitted.
- A combination of supporting signals raises sufficient duplicate suspicion to require review before proceeding.
- An isolated weak similarity does not block a candidate but should remain visible during review.
- Merge is permitted only when provenance from all contributing records is preserved.
- Two entities may remain distinct if they are demonstrably separate legal or physical entities that share incidental data points.

---

## 7. Publish Criteria

An entity may be published only when all of the following are satisfied:

- Has a stable public identifier fit for publication (non-placeholder)
- Has a valid entity type from the approved type set
- Has a valid place association
- Has at minimum a non-empty Bulgarian name translation
- Is attributable to at least one Authoritative or Strong source, or to converging Supporting sources without contradicting signals
- Has no unresolved critical duplicate concern (see Section 6)
- Has no unresolved critical source conflict on identity, type, or place fields (see Section 8)
- Has been confirmed by an admin with publish authority

Entities that do not meet all criteria remain in draft or held state. Partial data is acceptable in draft state; publication requires the full minimum set.

---

## 8. Conflict Resolution Model

**Source-vs-source conflict** (two accepted sources disagree on a field):
- For non-critical fields (e.g. description, address formatting): the higher-trust source wins; lower-trust source is retained as provenance.
- For critical fields (type, place, identity name): the conflict must be resolved by admin before publication or update is accepted.

**Owner-vs-existing-source conflict** (owner-submitted data contradicts an accepted source):
- Owner input is surfaced as a proposed update, not an automatic overwrite.
- If the owner contradicts an Authoritative source on type or place: requires admin resolution.
- If the owner contradicts a Supporting source on contact or operational fields: owner input is accepted with review.

**Stale-source-vs-new-source conflict** (newer data contradicts older accepted data):
- Newer source takes precedence only if its trust level is equal to or higher than the source it replaces.
- Downgrading a field value based on a Weak source alone is not permitted, even if newer.

**General precedence rule:** Trust level takes priority over recency, except when the newer source is Authoritative and the older source is not.

**Conflicts that can pass with admin review:** non-critical field disagreements, owner updates to contact and operational fields supported by at least one source.

**Conflicts that block publication or update:** unresolved disagreements on entity type, place association, or legal identity name when sources are of comparable trust level.

---

## 9. Data Freshness and Update Model

**Stable fields** — Entity type, place association, slug, original creation provenance. These change rarely and any change requires admin review regardless of source.

**Moderately changing fields** — Name translations, address, description, contact details, links. These may be updated when a source of equal or higher trust provides different data. Owner-submitted updates to these fields require review.

**Fast-changing fields** — Price signals, operational hours, observed availability data. These may be refreshed from trusted sources with reduced review burden, provided provenance is maintained.

**Automatic refresh:** Only fast-changing fields from a trusted source may refresh without explicit admin confirmation. All other field updates require review.

**Staleness:** An entity becomes operationally stale when its supporting sourcing for critical fields is no longer considered current. Stale status lowers the entity's confidence standing, increases its priority for re-evaluation, and reduces its evidential weight in conflict resolution. Staleness alone does not trigger unpublishing, but stale entities with open quality concerns may be held pending review.

---

## 10. Owner Submission and Owner Claim Model

**Owner submission** is the act of a person representing an entity submitting it to BKG as a new entry.
**Owner claim** is the act of a person asserting ownership or management authority over an entity already present in BKG.

These are distinct operations with distinct trust implications:

- Neither submission nor claim results in automatic publication or automatic privileged edit access.
- Submission creates a candidate that must pass the full governed candidate process — validation, deduplication, and publication review — without bypass.
- Claim requires verification before elevated control is granted. The method of verification is an operational decision not defined here.
- A verified owner gains elevated trust for identity and contact field updates. They do not gain unrestricted overwrite authority over fields sourced from Authoritative sources.
- Owner-submitted edits to critical fields (type, place) are treated as proposed updates requiring admin review.
- Owner-submitted edits to contact, link, and operational fields require review but are generally accepted once ownership is verified.
- An owner may not remove provenance records from their entity.

---

## 11. Minimal Administration Role Concept

The minimum role model for Phase 2 administration:

- **Editorial reviewer** — Reviews incoming candidates, verifies minimum criteria, flags duplicates, surfaces conflicts. Does not have final publish authority.
- **Publisher** — Has authority to confirm publication, approve merges, and resolve non-critical conflicts.
- **Quality authority** — Resolves critical conflicts (type, place, identity disputes), approves or rejects owner claims, may unpublish or hold entities for quality reasons.
- **System administrator** — Manages user accounts, source trust designations, and operating configuration. Not involved in editorial decisions.

A single person may hold multiple roles. The model does not require dedicated personnel per role at small operating scale.

---

## 12. Explicit Out of Scope

This document does not cover:

- Schema design or database migration planning
- Scraper or crawler implementation
- Queue, job, or background worker architecture
- Moderation UI or admin workflow implementation
- Full permissions matrix or RBAC implementation
- Search, filtering, or browse feature design
- SEO strategy or public URL strategy
- Analytics, reporting, or metrics systems
- Monetization, pricing, or commercial strategy
- Product packaging or public launch planning
- Phase 3 or future-phase feature planning
