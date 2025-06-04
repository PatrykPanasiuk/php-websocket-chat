# PHP WebSocket Chat

A lightweight, dependency-free WebSocket chat application built entirely in PHP.

It uses native sockets, custom WebSocket frame decoding, and asynchronous event handling via `stream_select()`.  
The frontend is written in pure HTML and JavaScript, enabling real-time communication across multiple clients.

---

## 🚀 Features

- No frameworks or libraries
- WebSocket server written in raw PHP
- Supports multiple clients simultaneously
- Real-time message broadcasting
- UTF-8 safe message decoding
- Messages visible to sender and all recipients
- Simple and minimal frontend

---

## ⚙️ Installation

Clone the repository and run both the WebSocket server and the PHP HTTP server:

```bash
git clone https://github.com/PatrykPanasiuk/php-websocket-chat.git
cd php-websocket-chat

# Start the WebSocket server (port 8080)
php websocket_server.php

# In another terminal: start the frontend (port 8000)
php -S localhost:8000
```

Then open your browser and go to:  
[http://localhost:8000](http://localhost:8000)

---

## 💬 Usage

1. When prompted, enter your nickname.
2. Type a message and press **Enter** to send.
3. Open multiple tabs or browsers to test real-time communication.
4. Messages are visible to all connected users — including the sender.

---

## 🧠 How It Works

- `websocket_server.php` implements a raw WebSocket server using `stream_socket_server` and `stream_select()`.
- WebSocket handshake and frame decoding are handled manually.
- Incoming messages are decoded and broadcasted to all connected clients.
- The frontend (`index.php`) uses native JavaScript to connect via WebSocket and render incoming messages in real time.

---

## 🛣️ Planned Features

- [ ] Message timestamps
- [ ] Message history on join
- [ ] Nickname validation and uniqueness
- [ ] Private messaging support
- [ ] Chat commands (e.g. `/nick`, `/clear`)
- [ ] Typing indicators

---

## 📸 Demo

*Coming soon* — GIF or video preview of live chat in action.

---

## 📄 License

This project is licensed under the MIT License.  
See the [LICENSE](LICENSE) file for more information.