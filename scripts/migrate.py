#!/usr/bin/env python3
"""Re-apply the Universal Reading rebrand after each upstream sync (idempotent).

Protected anchors (kept intact on every run):
- github.com/j0k3r/graby, j0k3r/php-readability, fossar/htmlawed, bdunogier/guzzle-site-authenticator
  (functional composer vendor overrides in compose.yaml comments)
- COPYING.md (MIT license, untouched)
- .github/ and scripts/ are excluded from the sweep entirely
"""
from pathlib import Path
import shutil

PAIRS = [
 ("WallabagMigration", "UniversalReadingMigration"),
 ("WallabagKernelTestCase", "UniversalReadingKernelTestCase"),
 ("WallabagTestCase", "UniversalReadingTestCase"),
 ("WallabagRestController", "UniversalReadingRestController"),
 ("WallabagV", "UniversalReadingV"),
 ("wallabag_craue_config_setting", "universalreading_craue_config_setting"),
 ("wallabag_entry", "universalreading_entry"),
 ("wallabag_user", "universalreading_user"),
 ("wallabag_oauth", "universalreading_oauth"),
 ("wallabag_config", "universalreading_config"),
 ("wallabag_v", "universalreading_v"),
 ("wallabag_test", "universalreading_test"),
 ("who_behind_wallabag", "who_behind_universalreading"),
 ("WALLABAG_", "UNIVERSALREADING_"),
 ("Wallabag", "Universal Reading"),
 ("WALLABAG", "UNIVERSALREADING"),
 ("wallabag", "universalreading"),
]
SKIP_EXT = {".png",".jpg",".jpeg",".ico",".webp",".woff",".woff2",".ttf",".eot",".otf",".gif",".pdf",".gz",".zip"}
KEEP = {"COPYING.md", "migrate.py"}
DELETE = ["CREDITS.md", "CODE_OF_CONDUCT.md", "SECURITY.md", "RELEASE_PROCESS.md", "codecov.yml",
          ".github/FUNDING.yml", ".github/CODEOWNERS", ".github/CONTRIBUTING.md",
          ".github/PULL_REQUEST_TEMPLATE.md", ".github/dependabot.yml", ".github/release.yml",
          ".github/ISSUE_TEMPLATE", ".github/images"]


def main():
    for d in DELETE:
        p = Path(d)
        if p.is_dir():
            shutil.rmtree(p, ignore_errors=True)
        elif p.exists():
            p.unlink()
    for p in Path(".").rglob("*"):
        if ".git" in p.parts or not p.is_file() or p.name in KEEP or p.suffix in SKIP_EXT:
            continue
        if str(p).startswith((".github/", "scripts/")):
            continue
        try:
            t = p.read_text()
        except Exception:
            continue
        nt = t
        for a, b in PAIRS:
            nt = nt.replace(a, b)
        if nt != t:
            p.write_text(nt)
    for p in list(Path(".").rglob("*wallabag*")):
        if ".git" in p.parts:
            continue
        target = str(p).replace("wallabag", "universalreading")
        if p.is_dir():
            shutil.move(str(p), target)
        else:
            p.rename(target)
    readme = Path("README.md")
    if readme.exists() and not readme.read_text().startswith("# Universal Reading"):
        print("README drifted - rewrite needed")
    print("migrate done")


if __name__ == "__main__":
    main()
