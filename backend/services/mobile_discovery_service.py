# -*- coding: utf-8 -*-
"""UDP discovery responder for mobile auto-configuration."""

from __future__ import annotations

import json
import socket
import threading

from backend.services.mobile_pairing_service import (
    build_discovery_offer,
    get_mobile_pairing_settings,
    resolve_local_ip_for_peer,
)


class MobileUdpDiscoveryService:
    def __init__(self, app):
        self._app = app
        self._thread = None
        self._stop_event = threading.Event()
        self._socket = None
        self._bound_port = None

    def start(self):
        if self._thread and self._thread.is_alive():
            return
        self._stop_event.clear()
        self._thread = threading.Thread(target=self._run, daemon=True, name='mobile-udp-discovery')
        self._thread.start()

    def stop(self):
        self._stop_event.set()
        self._close_socket()
        if self._thread and self._thread.is_alive():
            self._thread.join(timeout=1.5)
        self._thread = None

    def _close_socket(self):
        sock = self._socket
        self._socket = None
        self._bound_port = None
        if sock is None:
            return
        try:
            sock.close()
        except OSError:
            pass

    def _ensure_socket(self, port):
        if self._socket is not None and self._bound_port == port:
            return True

        self._close_socket()

        try:
            sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
            sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
            sock.bind(('0.0.0.0', int(port)))
            sock.settimeout(1.0)
        except OSError as exc:
            print(f'[MobileDiscovery] bind failed on UDP {port}: {exc}')
            return False

        self._socket = sock
        self._bound_port = int(port)
        print(f'[MobileDiscovery] UDP responder listening on 0.0.0.0:{port}')
        return True

    def _run(self):
        while not self._stop_event.is_set():
            with self._app.app_context():
                settings = get_mobile_pairing_settings(include_sensitive=False)

            if not settings.get('enabled', True):
                self._close_socket()
                self._stop_event.wait(1.0)
                continue

            listen_port = int(settings.get('discovery_port') or 45876)
            if not self._ensure_socket(listen_port):
                self._stop_event.wait(1.0)
                continue

            try:
                data, addr = self._socket.recvfrom(4096)
            except socket.timeout:
                continue
            except OSError:
                if self._stop_event.is_set():
                    break
                continue

            if not data:
                continue

            try:
                payload = json.loads(data.decode('utf-8', errors='ignore'))
            except json.JSONDecodeError:
                continue

            packet_type = str(payload.get('type') or '').strip().lower()
            if packet_type not in {'facecheck.mobile.discover', 'facecheck_mobile_discover'}:
                continue

            with self._app.app_context():
                latest_settings = get_mobile_pairing_settings(include_sensitive=False)

            if not latest_settings.get('allow_udp_discovery', True):
                continue

            peer_ip = addr[0] if isinstance(addr, tuple) and addr else ''
            local_ip = resolve_local_ip_for_peer(peer_ip)
            response_payload = build_discovery_offer(latest_settings, local_ip)

            try:
                response_bytes = json.dumps(response_payload, ensure_ascii=True).encode('utf-8')
                self._socket.sendto(response_bytes, addr)
            except OSError:
                continue

        self._close_socket()
