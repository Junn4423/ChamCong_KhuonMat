# -*- coding: utf-8 -*-
"""Runtime helpers for persistent application data paths."""

import os
import shutil
import sys
from pathlib import Path


APP_NAME = 'FaceCheck'


def _iter_ancestors(start: Path, max_depth: int = 8):
    current = start.resolve()
    seen = set()
    for _ in range(max_depth):
        key = str(current).lower()
        if key in seen:
            break
        seen.add(key)
        yield current
        parent = current.parent
        if parent == current:
            break
        current = parent


def _project_root() -> Path:
    return Path(__file__).resolve().parents[1]


def _find_repo_instance_dir():
    search_roots = [Path.cwd()]

    if getattr(sys, 'frozen', False):
        search_roots.append(Path(sys.executable).resolve().parent)
    else:
        search_roots.append(_project_root())

    for root in search_roots:
        for candidate in _iter_ancestors(root):
            if (candidate / 'package.json').is_file():
                return candidate / 'instance'
    return None


def resolve_data_dir() -> Path:
    env_dir = os.environ.get('FACECHECK_DATA_DIR')
    if env_dir:
        return Path(env_dir).expanduser().resolve()

    repo_instance_dir = _find_repo_instance_dir()
    if repo_instance_dir is not None:
        return repo_instance_dir

    local_appdata = os.environ.get('LOCALAPPDATA')
    if local_appdata:
        return Path(local_appdata) / APP_NAME / 'instance'

    return Path.home() / f'.{APP_NAME.lower()}' / 'instance'


def ensure_data_dir() -> Path:
    data_dir = resolve_data_dir()
    data_dir.mkdir(parents=True, exist_ok=True)
    return data_dir


def _legacy_db_candidates():
    project_db = _project_root() / 'instance' / 'attendance.db'
    yield project_db

    if getattr(sys, 'frozen', False):
        exe_dir = Path(sys.executable).resolve().parent
        yield exe_dir / '_internal' / 'var' / 'backend.app-instance' / 'attendance.db'

        for ancestor in _iter_ancestors(exe_dir):
            yield ancestor / 'instance' / 'attendance.db'


def ensure_db_path() -> Path:
    data_dir = ensure_data_dir()
    db_path = data_dir / 'attendance.db'

    if db_path.exists():
        return db_path

    for candidate in _legacy_db_candidates():
        try:
            if candidate.exists() and candidate.resolve() != db_path.resolve():
                shutil.copy2(candidate, db_path)
                break
        except OSError:
            continue

    return db_path


def sqlite_database_uri() -> str:
    return f"sqlite:///{ensure_db_path().as_posix()}"


def runtime_path_info():
    data_dir = ensure_data_dir()
    db_path = data_dir / 'attendance.db'
    repo_instance_dir = _find_repo_instance_dir()
    return {
        'frozen': getattr(sys, 'frozen', False),
        'cwd': os.getcwd(),
        'executable': sys.executable,
        'data_dir': str(data_dir),
        'db_path': str(db_path),
        'db_exists': db_path.exists(),
        'repo_instance_dir': str(repo_instance_dir) if repo_instance_dir else None,
    }
