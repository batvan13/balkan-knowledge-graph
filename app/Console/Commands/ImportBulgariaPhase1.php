<?php

namespace App\Console\Commands;

use App\Models\Place;
use App\Models\PlaceTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Phase 1 structural import: Bulgaria country root + 27 approved regions.
 *
 * Governing documents:
 *   docs/architecture/BULGARIA_PLACES_IMPORT_STRATEGY.md
 *   working/import_work/bulgaria_places/PHASE1_STRUCTURAL_IMPORT_PLAN.md
 *   working/import_work/bulgaria_places/REGION_LIST_REVIEWED.md
 *
 * Execution is idempotent. Reruns produce no changes where records already exist.
 * Use --dry-run to preview without writing.
 */
class ImportBulgariaPhase1 extends Command
{
    protected $signature = 'bkg:import-bulgaria-phase1
                            {--dry-run : Preview planned changes without writing to the database}';

    protected $description = 'Phase 1 structural import: Bulgaria country root + 27 approved regions (idempotent).';

    /**
     * Approved normalized region list.
     *
     * Source of truth: working/import_work/bulgaria_places/REGION_LIST_REVIEWED.md — Section 5.
     * This array must not be modified without first updating that artifact.
     */
    private const APPROVED_REGIONS = [
        'Благоевград',
        'Бургас',
        'Варна',
        'Велико Търново',
        'Видин',
        'Враца',
        'Габрово',
        'Добрич',
        'Кърджали',
        'Кюстендил',
        'Ловеч',
        'Монтана',
        'Пазарджик',
        'Перник',
        'Плевен',
        'Пловдив',
        'Разград',
        'Русе',
        'Силистра',
        'Сливен',
        'Смолян',
        'София',
        'Стара Загора',
        'Търговище',
        'Хасково',
        'Шумен',
        'Ямбол',
    ];

    private const BULGARIA_SLUG    = 'bulgaria';
    private const BULGARIA_BG_NAME = 'България';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('[DRY RUN] No database writes will occur.');
        }

        // ── Step 1: Bulgaria country root ────────────────────────────────────
        $this->line('');
        $this->line('Step 1 — Bulgaria country root');

        $bulgaria = $this->resolveOrCreateBulgariaRoot($dryRun);

        if ($bulgaria === false) {
            return self::FAILURE;
        }

        // ── Step 2: Approved regions ──────────────────────────────────────────
        $this->line('');
        $this->line('Step 2 — Approved regions (' . count(self::APPROVED_REGIONS) . ')');

        $created  = 0;
        $reused   = 0;

        foreach (self::APPROVED_REGIONS as $regionName) {
            $result = $this->resolveOrCreateRegion($regionName, $bulgaria, $dryRun);

            if ($result === false) {
                return self::FAILURE;
            }

            $result === 'created' ? $created++ : $reused++;
        }

        // ── Step 3: Summary and post-execution validation ─────────────────────
        $this->line('');
        $this->line('──────────────────────────────────────');

        if ($dryRun) {
            $this->info(sprintf(
                '[DRY RUN] Would create: %d. Would reuse: %d.',
                $created, $reused
            ));
        } else {
            $this->info(sprintf(
                'Phase 1 complete. Created: %d. Reused: %d.',
                $created, $reused
            ));

            if (! $this->validateFinalState($bulgaria)) {
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Resolve the Bulgaria country root.
     * Returns the Place on success, false on unrecoverable collision.
     *
     * @return Place|false|null  null only in dry-run where root does not yet exist
     */
    private function resolveOrCreateBulgariaRoot(bool $dryRun): Place|false|null
    {
        $existing = Place::where('slug', self::BULGARIA_SLUG)->first();

        if ($existing) {
            if ($existing->type !== 'country') {
                $this->error(sprintf(
                    'ABORT: A place with slug "%s" exists but has type "%s" (expected "country"). Manual review required.',
                    self::BULGARIA_SLUG,
                    $existing->type
                ));
                return false;
            }

            $this->line(sprintf('  ✓ Bulgaria root already exists (id=%d). Reusing.', $existing->id));
            return $existing;
        }

        if ($dryRun) {
            $this->line(sprintf(
                '  [dry-run] Would create country place: slug="%s", name(bg)="%s"',
                self::BULGARIA_SLUG,
                self::BULGARIA_BG_NAME
            ));
            return null;
        }

        return DB::transaction(function () {
            $place            = new Place();
            $place->type      = 'country';
            $place->slug      = self::BULGARIA_SLUG;
            $place->parent_id = null;
            $place->save();

            $t           = new PlaceTranslation();
            $t->place_id = $place->id;
            $t->locale   = 'bg';
            $t->name     = self::BULGARIA_BG_NAME;
            $t->save();

            $this->info(sprintf('  + Created Bulgaria country root (id=%d).', $place->id));
            return $place;
        });
    }

    /**
     * Resolve or create one approved region under the Bulgaria root.
     * Returns 'created', 'reused', or false on unrecoverable collision.
     *
     * @param  Place|null  $bulgaria  null only in dry-run; slug check still runs
     */
    private function resolveOrCreateRegion(string $regionName, Place|null $bulgaria, bool $dryRun): string|false
    {
        $slug = $this->generateSlug($regionName);

        if ($slug === '') {
            $this->error(sprintf(
                'ABORT: Slug generation produced an empty string for "%s". Halting.',
                $regionName
            ));
            return false;
        }

        $existing = Place::where('slug', $slug)->first();

        if ($existing) {
            if ($existing->type !== 'region') {
                $this->error(sprintf(
                    'COLLISION: Slug "%s" exists with type "%s" (expected "region"). Manual review required. Halting.',
                    $slug,
                    $existing->type
                ));
                return false;
            }

            if (! $dryRun && $bulgaria && $existing->parent_id !== $bulgaria->id) {
                $this->error(sprintf(
                    'COLLISION: Region slug "%s" exists but parent_id=%d does not match Bulgaria root id=%d. Manual review required. Halting.',
                    $slug,
                    $existing->parent_id,
                    $bulgaria->id
                ));
                return false;
            }

            $this->line(sprintf('  ✓ %s (slug=%s) already exists. Reusing.', $regionName, $slug));
            return 'reused';
        }

        if ($dryRun) {
            $this->line(sprintf('  [dry-run] Would create region: "%s" (slug=%s)', $regionName, $slug));
            return 'created';
        }

        DB::transaction(function () use ($regionName, $slug, $bulgaria) {
            $place            = new Place();
            $place->type      = 'region';
            $place->slug      = $slug;
            $place->parent_id = $bulgaria->id;
            $place->save();

            $t           = new PlaceTranslation();
            $t->place_id = $place->id;
            $t->locale   = 'bg';
            $t->name     = $regionName;
            $t->save();
        });

        $this->info(sprintf('  + Created region: "%s" (slug=%s)', $regionName, $slug));
        return 'created';
    }

    /**
     * Post-execution validation. Returns true if all checks pass.
     */
    private function validateFinalState(Place $bulgaria): bool
    {
        $this->line('');
        $this->line('Post-execution validation:');

        $pass = true;

        // Region count
        $regionCount = Place::where('parent_id', $bulgaria->id)
            ->where('type', 'region')
            ->count();
        $expected = count(self::APPROVED_REGIONS);

        if ($regionCount === $expected) {
            $this->info(sprintf('  ✓ Region count under Bulgaria: %d / %d expected.', $regionCount, $expected));
        } else {
            $this->warn(sprintf('  ⚠ Region count: %d (expected %d). Review required.', $regionCount, $expected));
            $pass = false;
        }

        // No settlements created under Bulgaria
        $unexpectedChildren = Place::where('parent_id', $bulgaria->id)
            ->whereNotIn('type', ['region'])
            ->exists();

        if (! $unexpectedChildren) {
            $this->info('  ✓ No non-region records found directly under Bulgaria root.');
        } else {
            $this->warn('  ⚠ Unexpected non-region children detected under Bulgaria root. Review required.');
            $pass = false;
        }

        // BG translation present on Bulgaria root
        $hasBgName = $bulgaria->translations()->where('locale', 'bg')->exists();
        if ($hasBgName) {
            $this->info('  ✓ Bulgaria root has a BG translation.');
        } else {
            $this->warn('  ⚠ Bulgaria root is missing its BG translation. Review required.');
            $pass = false;
        }

        return $pass;
    }

    /**
     * Generate a URL slug from a Bulgarian name using the project's
     * established Cyrillic transliteration rules (same map as Entity::generateSlugFromBgName).
     */
    private function generateSlug(string $bgName): string
    {
        $map = [
            'а' => 'a',   'б' => 'b',   'в' => 'v',   'г' => 'g',   'д' => 'd',
            'е' => 'e',   'ж' => 'zh',  'з' => 'z',   'и' => 'i',   'й' => 'y',
            'к' => 'k',   'л' => 'l',   'м' => 'm',   'н' => 'n',   'о' => 'o',
            'п' => 'p',   'р' => 'r',   'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'ts',  'ч' => 'ch',  'ш' => 'sh',
            'щ' => 'sht', 'ъ' => 'a',   'ь' => '',    'ю' => 'yu',  'я' => 'ya',
            'А' => 'A',   'Б' => 'B',   'В' => 'V',   'Г' => 'G',   'Д' => 'D',
            'Е' => 'E',   'Ж' => 'Zh',  'З' => 'Z',   'И' => 'I',   'Й' => 'Y',
            'К' => 'K',   'Л' => 'L',   'М' => 'M',   'Н' => 'N',   'О' => 'O',
            'П' => 'P',   'Р' => 'R',   'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'Ts',  'Ч' => 'Ch',  'Ш' => 'Sh',
            'Щ' => 'Sht', 'Ъ' => 'A',   'Ь' => '',    'Ю' => 'Yu',  'Я' => 'Ya',
        ];

        return Str::slug(strtr($bgName, $map));
    }
}
