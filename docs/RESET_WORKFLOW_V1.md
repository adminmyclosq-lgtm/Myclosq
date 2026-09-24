# Myclosq — Day 0–30 Workflow Integration v1

## Scope

This change integrates the guided reset journey into the existing Laravel project without replacing the existing homepage, login, ecommerce, account or admin framework.

## Customer flow

1. The customer enters the reset through a delivered order or activation QR.
2. Day 0 captures baseline context, up to three priority areas, up to three triggers, five baseline signal scores, start date and reminder preferences.
3. Days 1–30 record one daily adherence row and one reset-card usage row per day.
4. Days 3, 7, 14 and 21 also store a milestone check-in and answers.
5. Day 30 stores the final five signal scores plus final review, feedback and continuation intent.
6. The service generates the GRI and GRS records and closes the cycle with `status=completed` while retaining the user's account.
7. The customer can view the current response brief and historical completed cycles under the same login.
8. A later cycle is created only from an approved re-entry request; the previous cycle remains intact.

## Interruptions and unusual events

The daily workflow identifies a missed current-day check-in after the configured grace period and records a `dropoff_events` row. The profile is then held in `dropoff` state until the user resumes.

A user-reported unusual event is recorded separately. High/severe/critical entries create a `safety_flags` record, set `safety_flag_active=true`, set `manual_review_required=true`, and pause automation. Admin resolution clears the active safety hold when no other open flag remains.

When a dropped-off user returns through a WhatsApp completion response, the return is recorded in `reactivation_events` and the current cycle continues; it is not silently replaced.

## Scoring

The implementation follows the programme rules already established for the project:

`GRI = (capsules taken × 2) + (Day 30 comfort × 4)` with a maximum of 100.

GRI band mapping:

- 80–100 → `80-100`
- 60–79 → `60-79`
- 40–59 → `40-59`
- below 40 → `0-39`

For GRS:

`Day 0 burden = bloating + gas/burping + heaviness + acidity`

`Day 30 burden = bloating + gas/burping + heaviness + acidity`

`GRS movement = Day 0 burden − Day 30 burden`

`Comfort delta = Day 30 comfort − Day 0 comfort`

## WhatsApp

The existing queue job and Meta service are reused. Day 1–29 use the `daily_checkin` template. Day 30 uses `day30_review` and supplies the existing `/reset/day/30` URL. A detected drop-off receives `reactivation_prompt`.

The Day 0 WhatsApp opt-in is synchronized into the existing customer profile WhatsApp opt-in fields so the current sender/queue can use the user's choice.

Meta template approval, phone number configuration, access token, queue worker and a valid WhatsApp contact/phone remain deployment responsibilities.

## Admin operations

- `/admin/reset-operations` — programme counts, today’s activity, milestones and recent profiles
- `/admin/reset-operations/profiles/{resetProfile}` — customer cycle detail, checkpoints, adherence, unusual events, safety and final review
- `/admin/reset-operations/re-entry` — approve/reject later-cycle requests
- `/admin/reset-operations/testimonials` — moderation workflow

## Files added / changed

### Added

- `app/Http/Controllers/Web/ResetJourneyController.php`
- `app/Http/Controllers/Web/Admin/ResetOperationsController.php`
- `app/Models/ResetReentryRequest.php`
- `app/Services/ResetJourneyService.php`
- `database/migrations/phase1c/20260919130000_add_reset_cycles_and_reentry_requests.php`
- `resources/views/reset/*.blade.php`
- `resources/views/admin/reset/show.blade.php`
- `resources/views/admin/reset/reentry.blade.php`
- `resources/views/admin/reset/testimonials.blade.php`

### Updated

- reset-related models and relationships
- web routes and login QR handoff
- existing My Brief/My Plan routes to the new journey
- daily workflow and Day 30 workflow services
- order delivery activation hook
- existing WhatsApp workflow
- account and course navigation
- admin reset reporting
- development seed for a demo Day 0 cycle

## Runtime validation status

Static PHP linting and frontend build validation are performed during packaging. Full Laravel DB-backed execution is dependent on the target MySQL environment; the current build host does not have the MySQL PDO driver available, so database migrations and live workflow transactions are not executed here.

The project contains `reference/Final Design Doc.V4.pdf`, but that PDF is malformed in the supplied archive and could not be reliably rendered/extracted. The implementation therefore uses the existing project schema/data dictionary plus the Day 0–30 rules already established for this project.
