#!/usr/bin/env python3

from __future__ import annotations

import json
import re
from pathlib import Path

TEXT_NODE_PATTERN = re.compile(r">([^<\n]*[A-Za-z][^<\n]*)<")
ATTRIBUTE_PATTERN = re.compile(
    r'(title|placeholder|aria-label|alt|label|helper|buttonTitle|step3ButtonText|confirmTitle|description|subtitle|heading)="([^"]*[A-Za-z][^"]*)"'
)
BOUND_ATTRIBUTE_PATTERN = re.compile(
    r'(:title|x-bind:title|:aria-label|x-bind:aria-label)="([^"]+)"'
)
QUOTED_STRING_PATTERN = re.compile(r"""(['"])([^'"]*[A-Za-z][^'"]*)\1""")

CODE_MARKERS = (
    "{{",
    "}}",
    "{!!",
    "!!}",
    "->",
    "::",
    "&&",
    "||",
    "wire:",
    "x-",
    "@if",
    "@foreach",
    "@php",
    "$checkbox",
    "$title",
    "$helper",
    "$wire",
    "${",
    "=>",
    "bg-",
    "dark:",
    "text-",
    "setTimeout(",
    "innerHTML",
)


def normalize(value: str) -> str:
    return re.sub(r"\s+", " ", value).strip()


def should_keep(value: str) -> bool:
    normalized = normalize(value)
    if len(normalized) < 2:
        return False
    if not re.search(r"[A-Za-z]", normalized):
        return False
    if normalized.startswith(("$", "#", ".", "/", "'", '"', "`")):
        return False
    if normalized.endswith(("])>", "}}", "->", "::")):
        return False
    if any(marker in normalized for marker in ("&middot;", "&rarr;", " mb", " MB", "timeString", "selectedIndex")):
        return False
    if re.search(r"^[^A-Za-z]*\$", normalized):
        return False
    return not any(marker in normalized for marker in CODE_MARKERS)


def extract_strings(root: Path) -> list[str]:
    strings: list[str] = []

    for path in sorted(root.rglob("*.blade.php")):
        content = path.read_text(errors="ignore")

        for match in TEXT_NODE_PATTERN.finditer(content):
            value = normalize(match.group(1))
            if should_keep(value):
                strings.append(value)

        for _, value in ATTRIBUTE_PATTERN.findall(content):
            value = normalize(value)
            if should_keep(value):
                strings.append(value)

        for _, expression in BOUND_ATTRIBUTE_PATTERN.findall(content):
            for _, value in QUOTED_STRING_PATTERN.findall(expression):
                value = normalize(value)
                if should_keep(value):
                    strings.append(value)

    return sorted(dict.fromkeys(strings))


def load_json(path: Path) -> dict[str, str]:
    if not path.exists():
        return {}
    return json.loads(path.read_text())


def main() -> None:
    repo_root = Path(__file__).resolve().parents[1]
    view_root = repo_root / "resources" / "views"
    en_path = repo_root / "lang" / "en.json"
    zh_path = repo_root / "lang" / "zh-cn.json"

    extracted = extract_strings(view_root)
    english = load_json(en_path)
    chinese = load_json(zh_path)

    for value in extracted:
        english.setdefault(value, value)
        chinese.setdefault(value, value)

    en_path.write_text(json.dumps(dict(sorted(english.items())), ensure_ascii=False, indent=4) + "\n")
    zh_path.write_text(json.dumps(dict(sorted(chinese.items())), ensure_ascii=False, indent=4) + "\n")

    print(f"Extracted {len(extracted)} strings.")


if __name__ == "__main__":
    main()
