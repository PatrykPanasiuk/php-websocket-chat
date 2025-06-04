<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PHP WebSocket Chat</title>
    <style>
        body { font-family: sans-serif; margin: 2em; }
        #chat {
            width: 100%;
            height: 300px;
            border: 1px solid #ccc;
            padding: 10px;
            overflow-y: auto;
            margin-bottom: 10px;
            background: #f8f8f8;
        }
        #msg {
            width: 100%;
            padding: 8px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<h2>PHP WebSocket Chat</h2>

<div id="chat"></div>
<input id="msg" type="text" placeholder="Type your message and press Enter">

<script>
    const chat = document.getElementById("chat");
    const input = document.getElementById("msg");
    let nickname = "";

    const ws = new WebSocket("ws://localhost:8080");

    ws.onopen = () => {
        nickname = prompt("Enter your nickname:");
        if (nickname) {
            ws.send(nickname);
        }
    };

    ws.onmessage = (e) => {
        const msg = document.createElement("div");
        msg.textContent = e.data;
        chat.appendChild(msg);
        chat.scrollTop = chat.scrollHeight;
    };

    input.addEventListener("keydown", e => {
        if (e.key === "Enter" && input.value.trim() !== "") {
            ws.send(input.value);
            input.value = "";
        }
    });
</script>

</body>
</html>