# -*- coding: utf-8 -*-
"""Entry point for running the Flask backend (used by PyInstaller and Electron)."""

import os
import sys
import argparse

os.environ.setdefault('KMP_DUPLICATE_LIB_OK', 'TRUE')
os.environ.setdefault('OMP_NUM_THREADS', '1')
os.environ.setdefault('MKL_NUM_THREADS', '1')

def main():
    parser = argparse.ArgumentParser(description='FaceCheck API Server')
    parser.add_argument('--port', type=int, default=int(os.environ.get('FLASK_PORT', 5000)))
    parser.add_argument('--host', default='127.0.0.1')
    args = parser.parse_args()

    from backend.app import create_app
    app = create_app()
    print(f'Starting FaceCheck API on {args.host}:{args.port}')
    app.run(host=args.host, port=args.port, debug=False)

if __name__ == '__main__':
    main()
