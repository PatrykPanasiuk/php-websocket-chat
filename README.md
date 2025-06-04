# PHP WebSocket Chat

This is a lightweight, dependency-free WebSocket chat application built entirely in PHP.  
It uses native sockets, custom WebSocket frame decoding, and asynchronous event handling via `stream_select()`.  
Includes a simple HTML frontend with real-time message broadcasting.

## Features
- No frameworks or libraries
- WebSocket server in raw PHP
- Supports multiple clients simultaneously
- UTF-8 safe message handling
- Messages visible to sender and all recipients