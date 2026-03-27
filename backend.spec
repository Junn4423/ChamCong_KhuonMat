# -*- mode: python ; coding: utf-8 -*-
"""PyInstaller spec for the Flask API backend."""

import os
import sys
from PyInstaller.utils.hooks import collect_data_files, collect_submodules

block_cipher = None

# Collect data files
insightface_datas = collect_data_files('insightface')
onnxruntime_datas = collect_data_files('onnxruntime')

a = Analysis(
    ['run_backend.py'],
    pathex=['.'],
    binaries=[],
    datas=[
        ('backend', 'backend'),
        ('dist', 'frontend_dist'),
        ('Silent-Face-Anti-Spoofing/src', 'Silent-Face-Anti-Spoofing/src'),
        ('Silent-Face-Anti-Spoofing/resources', 'Silent-Face-Anti-Spoofing/resources'),
        ('static/fonts', 'static/fonts'),
    ] + insightface_datas + onnxruntime_datas,
    hiddenimports=[
        'flask', 'flask_sqlalchemy', 'flask_cors',
        'sqlalchemy', 'sqlalchemy.dialects.sqlite',
        'mysql.connector',
        'cv2', 'numpy', 'PIL',
        'insightface', 'onnxruntime',
        'torch', 'torchvision',
        'easydict',
        'backend', 'backend.app', 'backend.config',
        'backend.models', 'backend.services',
        'backend.models.database',
        'backend.models.face_recognition_module',
        'backend.models.insightface_module',
        'backend.models.erp_integration',
        'backend.services.attendance_service',
        'backend.services.tracking_service',
        'backend.services.import_employees',
    ] + collect_submodules('insightface') + [
        hi for hi in collect_submodules('onnxruntime')
        if not hi.startswith('onnxruntime.quantization')
        and not hi.startswith('onnxruntime.tools')
    ],
    hookspath=[],
    hooksconfig={},
    runtime_hooks=[],
    excludes=[
        'IPython', 'jupyter',
        # onnx.reference crashes PyInstaller subprocess (access violation)
        'onnx.reference', 'onnx.reference.ops', 'onnx.reference.ops_optimized',
        'onnx.backend',
        # Not needed at runtime
        'sympy', 'Cython', 'pyximport',
        'pip', 'setuptools._vendor',
        'torch.testing._internal', 'torch.utils.tensorboard',
        'torch.distributed', 'torch.utils.benchmark',
        'torchgen',
    ],
    win_no_prefer_redirects=False,
    win_private_assemblies=False,
    cipher=block_cipher,
    noarchive=False,
)

pyz = PYZ(a.pure, a.zipped_data, cipher=block_cipher)

exe = EXE(
    pyz,
    a.scripts,
    [],
    exclude_binaries=True,
    name='facecheck-api',
    debug=False,
    bootloader_ignore_signals=False,
    strip=False,
    upx=True,
    console=True,
    icon=None,
)

coll = COLLECT(
    exe,
    a.binaries,
    a.zipfiles,
    a.datas,
    strip=False,
    upx=True,
    upx_exclude=[],
    name='facecheck-api',
)
