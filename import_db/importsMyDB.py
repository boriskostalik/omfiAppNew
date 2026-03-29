import sys
sys.stdout.reconfigure(encoding='utf-8')

from pathlib import Path
from collections import OrderedDict

BASE = Path(__file__).parent

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

# institute replaced by institute_id (FK)
KEEP_AUTH_COLS = [
    'id', 'surname', 'von', 'firstname', 'email', 'url',
    'institute_id', 'created_at', 'updated_at'
]

KEEP_PUB_COLS = [
    'id', 'issue_id', 'actualyear', 'title', 'title_eng', 'bibtex_id', 'type',
    'isbn', 'firstpage', 'lastpage', 'abstract', 'doi'
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


def sql_str(value):
    """Wrap a Python string as a SQL quoted string, or return NULL."""
    if value is None:
        return 'NULL'
    escaped = value.replace("'", "\\'")
    return f"'{escaped}'"


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

    # Build institutes mapping: name -> id
    institutes = OrderedDict()
    next_inst_id = 1
    for vals in rows:
        if len(vals) != len(AUTH_COLS):
            continue
        name = raw(vals[auth_idx['institute']])
        if name and name.strip():
            name = name.strip()
            if name not in institutes:
                institutes[name] = next_inst_id
                next_inst_id += 1

    # Write importinstitutes.sql
    with (BASE / 'importinstitutes.sql').open('w', encoding='utf-8') as f:
        f.write('SET NAMES utf8mb4;\n')
        f.write('SET CHARACTER SET utf8mb4;\n')
        for name, iid in institutes.items():
            f.write(f"INSERT INTO institutes (id, name, created_at, updated_at) "
                    f"VALUES ({iid}, {sql_str(name)}, NOW(), NOW());\n")

    # Write importauthors_clean.sql with institute_id instead of institute string
    good = bad = 0
    with (BASE / 'importauthors_clean.sql').open('w', encoding='utf-8') as f:
        f.write('SET NAMES utf8mb4;\n')
        f.write('SET CHARACTER SET utf8mb4;\n')
        cols = ', '.join(KEEP_AUTH_COLS)
        for vals in rows:
            if len(vals) != len(AUTH_COLS):
                bad += 1
                continue
            name = raw(vals[auth_idx['institute']])
            institute_id = institutes.get(name.strip(), None) if name and name.strip() else None
            inst_sql = str(institute_id) if institute_id else 'NULL'

            selected = []
            for c in KEEP_AUTH_COLS:
                if c == 'institute_id':
                    selected.append(inst_sql)
                else:
                    selected.append(vals[auth_idx[c]])
            f.write(f"INSERT INTO authors ({cols}) VALUES ({', '.join(selected)});\n")
            good += 1

    return good, bad, len(institutes)


def fix_publications():
    rows = load_insert_rows(BASE / 'importpublications.sql', 'INSERT INTO publications')
    pub_idx = {c: i for i, c in enumerate(PUB_COLS)}
    issues = OrderedDict()
    parsed = []
    next_issue_id = 1
    good = bad = 0

    for vals in rows:
        if len(vals) != len(PUB_COLS):
            bad += 1
            continue
        row = {c: vals[pub_idx[c]] for c in PUB_COLS}
        year   = raw(row['year'])
        volume = raw(row['volume'])
        number = raw(row['number'])
        year_int   = int(year)   if year   and str(year).lstrip('-').isdigit()   else None
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
    print('Fixing authors + extracting institutes...')
    authors_good, authors_bad, inst_count = fix_authors()
    print(f'  institutes: {inst_count}')
    print(f'  authors: {authors_good} ok, {authors_bad} skipped')

    print('Fixing publications + extracting issues...')
    pubs_good, pubs_bad, issues = fix_publications()
    print(f'  issues: {issues}')
    print(f'  publications: {pubs_good} ok, {pubs_bad} skipped')

    print()
    print('Import order:')
    print('  1. importauthors_clean.sql')
    print('  2. importinstitutes.sql')
    print('  3. importissues.sql')
    print('  4. importpublications_clean.sql')
    print('  5. importpublication_authors.sql  (unchanged)')


if __name__ == '__main__':
    main()
