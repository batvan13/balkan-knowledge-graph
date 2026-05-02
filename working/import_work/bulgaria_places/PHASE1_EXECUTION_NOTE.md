# PHASE 1 EXECUTION NOTE

---

## Execution status

Phase 1 structural import completed successfully on 2026-05-02.

---

## Records created

- 1 `country` place record: Bulgaria (slug=`bulgaria`, name(bg)=`България`, id=2)
- 27 `region` place records, each with parent_id pointing to Bulgaria root
- 27 `place_translations` records, locale=`bg`, one per place created

Total places inserted: 28. Total place_translations inserted: 28.

---

## Approved region set — confirmed inserted

| BG Name | Slug |
|---|---|
| Благоевград | blagoevgrad |
| Бургас | burgas |
| Варна | varna |
| Велико Търново | veliko-tarnovo |
| Видин | vidin |
| Враца | vratsa |
| Габрово | gabrovo |
| Добрич | dobrich |
| Кърджали | kardzhali |
| Кюстендил | kyustendil |
| Ловеч | lovech |
| Монтана | montana |
| Пазарджик | pazardzhik |
| Перник | pernik |
| Плевен | pleven |
| Пловдив | plovdiv |
| Разград | razgrad |
| Русе | ruse |
| Силистра | silistra |
| Сливен | sliven |
| Смолян | smolyan |
| София | sofiya |
| Стара Загора | stara-zagora |
| Търговище | targovishte |
| Хасково | haskovo |
| Шумен | shumen |
| Ямбол | yambol |

---

## Post-execution validation — passed

- ✓ Bulgaria root present with BG translation
- ✓ 27 region records under Bulgaria root (27 / 27 expected)
- ✓ No non-region records created under Bulgaria root
- ✓ Rerun confirmed idempotent (0 created, 27 reused, exit 0)

---

## Notes

- `towns.sql` was not modified at any point.
- No settlements, municipalities, or coordinates were imported.
- Command is available for future reruns: `php artisan bkg:import-bulgaria-phase1`
- Use `--dry-run` flag to preview without writing.
