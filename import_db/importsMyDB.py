from pathlib import Path
from collections import OrderedDict

BASE = Path('/mnt/data')

PUB_COLS = [
    'id', 'entered_by', 'year', 'actualyear', 'title', 'title_eng', 'mesc',
    'bibtex_id', 'pub_type', 'type', 'survey', 'mark', 'series', 'volume',
    'publisher', 'location', 'issn', 'isbn', 'firstpage', 'lastpage', 'journal',
    'booktitle', 'number', 'institution', 'address', 'chapter', 'edition',
    'howpublished', 'month', 'organization', 'school', 'note', 'keywords',
    'abstract', 'url', 'doi', 'crossref', 'namekey', 'userfields', 'specialchars',
    'cleanjournal', 'cleantitle'
]

AUTH_COLS = [
    'id', 'surname', 'von', 'firstname', 'email', 'url',
    'institute', 'specialchars', 'cleanname', 'created_at', 'updated_at'
]

KEEP_AUTH_COLS = [
    'id', 'surname', 'von', 'firstname', 'email', 'url',
    'institute', 'cleanname', 'created_at', 'updated_at'
]

KEEP_PUB_COLS = [
    'id', 'issue_id', 'actualyear', 'title', 'title_eng', 'bibtex_id', 'type',
    'issn', 'isbn', 'firstpage', 'lastpage', 'journal', 'keywords', 'abstract', 'doi'
]

def split_sql_values(s: str):
    vals, cur = [], []
    in_str = False
    escape = False
    for ch in s:
        if in_str:
            cur.append(ch)
            if escape:
                escape = False
            elif ch == '\\':
                escape = True
            elif ch == "'":
                in_str = False
        else:
            if ch == "'":
                in_str = True
                cur.append(ch)
            elif ch == ',':
                vals.append(''.join(cur).strip())
                cur = []
            else:
                cur.append(ch)
    if cur:
        vals.append(''.join(cur).strip())
    return vals

def extract_insert_statements(text: str, prefix: str):
    stmts = []
    pos = 0
    while True:
        i = text.find(prefix, pos)
        if i == -1:
            break
        j = i
        in_str = False
        escape = False
        while j < len(text):
            ch = text[j]
            if in_str:
                if escape:
                    escape = False
                elif ch == '\\':
                    escape = True
                elif ch == "'":
                    in_str = False
            else:
                if ch == "'":
                    in_str = True
                elif ch == ';':
                    j += 1
                    break
            j += 1
        stmts.append(text[i:j])
        pos = j
    return stmts

def extract_values_part(stmt: str):
    marker = 'VALUES ('
    start = stmt.find(marker)
    if start == -1:
        return None
    start += len(marker)
    end = stmt.rfind(');')
    if end == -1:
        return None
    return stmt[start:end].strip()


def raw(token: str):
    token = token.strip()
    if token.upper() == 'NULL':
        return None
    if token.startswith("'") and token.endswith("'"):
        return token[1:-1]
    return token

def load_insert_rows(path: Path, prefix: str):
    text = path.read_text(encoding='utf-8')
    rows = []
    for stmt in extract_insert_statements(text, prefix):
        values = extract_values_part(stmt)
        if values is not None:
            rows.append(split_sql_values(values))
    return rows

def fix_authors():
    rows = load_insert_rows(BASE / 'importauthors_fixed.sql', 'INSERT INTO authors')
    auth_idx = {c: i for i, c in enumerate(AUTH_COLS)}
    out = BASE / 'importauthors_clean.sql'
    good = 0
    bad = 0
    with out.open('w', encoding='utf-8') as f:
        f.write('SET NAMES utf8mb4;\n')
        f.write('SET CHARACTER SET utf8mb4;\n')
        cols = ', '.join(KEEP_AUTH_COLS)
        for vals in rows:
            if len(vals) != len(AUTH_COLS):
                bad += 1
                continue
            selected = [vals[auth_idx[c]] for c in KEEP_AUTH_COLS]
            f.write(f"INSERT INTO authors ({cols}) VALUES ({', '.join(selected)});\n")
            good += 1
    return good, bad

def fix_publications():
    rows = load_insert_rows(BASE / 'importpublications.sql', 'INSERT INTO publications')
    pub_idx = {c: i for i, c in enumerate(PUB_COLS)}
    issues = OrderedDict()
    parsed = []
    next_issue_id = 1
    good = 0
    bad = 0
    for vals in rows:
        if len(vals) != len(PUB_COLS):
            bad += 1
            continue
        row = {c: vals[pub_idx[c]] for c in PUB_COLS}
        year = raw(row['year'])
        volume = raw(row['volume'])
        number = raw(row['number'])
        year_int = int(year) if year and str(year).lstrip('-').isdigit() else None
        number_int = int(number) if number and str(number).lstrip('-').isdigit() else 0
        volume_norm = volume.strip() if volume and volume.strip() else None
        issue_id = 'NULL'
        if year_int is not None:
            key = (year_int, volume_norm, number_int)
            if key not in issues:
                issues[key] = next_issue_id
                next_issue_id += 1
            issue_id = issues[key]
        parsed.append((row, issue_id))
        good += 1

    with (BASE / 'importissues.sql').open('w', encoding='utf-8') as f:
        f.write('SET NAMES utf8mb4;\n')
        f.write('SET CHARACTER SET utf8mb4;\n')
        for (year, volume, number), iid in issues.items():
            vol_sql = f"'{volume}'" if volume else 'NULL'
            f.write(
                'INSERT INTO issues (id, year, volume, number, created_at, updated_at) '
                f'VALUES ({iid}, {year}, {vol_sql}, {number}, NOW(), NOW());\n'
            )

    with (BASE / 'importpublications_clean.sql').open('w', encoding='utf-8') as f:
        f.write('SET NAMES utf8mb4;\n')
        f.write('SET CHARACTER SET utf8mb4;\n')
        cols = ', '.join(f'`{c}`' for c in KEEP_PUB_COLS)
        for row, issue_id in parsed:
            out_vals = [str(issue_id) if c == 'issue_id' else row[c] for c in KEEP_PUB_COLS]
            f.write(f"INSERT INTO publications ({cols}) VALUES ({', '.join(out_vals)});\n")
    return good, bad, len(issues)


def main():
    authors_good, authors_bad = fix_authors()
    pubs_good, pubs_bad, issues = fix_publications()
    print(f'authors: {authors_good} ok, {authors_bad} skipped')
    print(f'issues: {issues}')
    print(f'publications: {pubs_good} ok, {pubs_bad} skipped')

if __name__ == '__main__':
    main()
