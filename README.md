Changelog — fixes made

1) index.php
- Added missing absensi form (#formAbsensi) with hidden inputs `lat`, `lon` and `radius`, plus `nama` and `nim` fields.
- Removed duplicated popup HTML and fixed broken PHP conditional.
- Corrected misplaced HTML closing tags.

2) proses.php
- Fixed incorrect include of `sudah_absen.php` (which did not exist). Now `popup_sudah_absen.php` is included and there is a safe fallback that sets a flash message and redirects.
- Tidied early-exit flow so an already-absen device receives the popup page.

How to test the "already absen" flow
1. Open the app and perform a successful or failed absen once.
2. Try submitting the form again from the same browser session — you should see the "Absensi Sudah Tercatat" popup (served by `popup_sudah_absen.php`).

If you want a test helper to clear the session quickly, I can add a simple debug button for development only. Let me know and I'll add it.

Note on styling changes:
- The main stylesheet `style.css` was previously compacted (single-line rules) to reduce size.
- At your request the file has now been reformatted with shorter, readable lines so it's easier to edit while keeping the same visuals.

If you prefer a minified single-line production copy and a prettified working copy together, I can add both.

Note on location accuracy and watching:
- The client now uses watchPosition() so the page receives a stream of fixes instead of a single possibly-cached value.
- We still force fresh reads (maximumAge: 0) and check `position.coords.accuracy`. The default threshold is 100 m — if accuracy is poor the page will keep watching for better fixes up to a timeout/attempt limit, then accept the best available fix.

If you'd like different behavior, I can:
- tighten/loosen the accuracy threshold (e.g. 30 m or 200 m),
- implement an automatic retry loop until accuracy improves (with a retry cap), or
- use watchPosition with a longer accumulation window to pick the most accurate fix.
