=== GlotCore Translation Memory ===
Contributors: meloniq
Tags: glotpress, translation, memory, suggestions
Tested up to: 6.9
Stable tag: 1.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Suggests translations based on a translation memory of previously translated strings in GlotPress.

== Description ==

Ideas for implementation:
1. Query the `originals` table for the exact match of the original string.
 - If found, query the `translations` table for translations of the original string from other projects.

2. Separate database table to store simplified original strings (`originals` table, `singular` column)
 - Column need to have fulltext index for searching.
 - When a new original string is added, it is simplified and stored in the `singular` column.
 - When a original string is deleted, the corresponding entry in the new table is also deleted.
 - Do a fulltext search on the `singular` column to find similar original strings (e.g. max 100 results).
 - Found similar strings filter in PHP using a string similarity algorithm (e.g., Levenshtein distance) to find the closest matches (10 with > 80% match).
 - Query the `translations` table for translations of the similar original strings from other projects.

== Changelog ==

= 1.0 =
* Initial release.
