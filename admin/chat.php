<?php
require_once '../init.php';

if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] != 'admin') {
    header('Location: admin-403.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản lý Chat | Admin</title>
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/font-awesome.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <style>
        .chat-container {
            display: flex;
            height: calc(100vh - 100px);
            margin: 20px;
            gap: 20px;
        }
        .chat-list {
            width: 300px;
            border: 1px solid #ddd;
            overflow-y: auto;
        }
        .chat-messages {
            flex: 1;
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .chat-input {
            padding: 20px;
            border-top: 1px solid #ddd;
        }
        .user-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .user-item:hover {
            background: #f5f5f5;
        }
        .user-item.active {
            background: #e9ecef;
        }
        .message {
            margin-bottom: 10px;
            max-width: 70%;
        }
        .message.incoming {
            margin-right: auto;
        }
        .message.outgoing {
            margin-left: auto;
            background: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-list">
            <h4 class="p-3 mb-0 border-bottom">Danh sách người dùng</h4>
            <div id="userList"></div>
        </div>
        <div class="chat-messages">
            <div class="messages-container" id="messagesContainer"></div>
            <div class="chat-input">
                <div class="input-group">
                    <input type="text" id="messageInput" class="form-control" placeholder="Nhập tin nhắn...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" onclick="sendMessage()">Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    let currentUserId = null;
    let lastMessageId = 0;

    function loadUsers() {
        $.ajax({
            url: 'get_chat_users.php',
            type: 'GET',
            success: function(response) {
                const users = JSON.parse(response);
                const userList = document.getElementById('userList');
                userList.innerHTML = users.map(user => `
                    <div class="user-item ${currentUserId == user.account_id ? 'active' : ''}" 
                         onclick="selectUser(${user.account_id})">
                        ${user.username}
                    </div>
                `).join('');
            }
        });
    }

    function selectUser(userId) {
        currentUserId = userId;
        lastMessageId = 0;
        loadMessages();
        loadUsers(); // Cập nhật trạng thái active
    }

    function loadMessages() {
        if (!currentUserId) return;
        
        $.ajax({
            url: 'get_admin_messages.php',
            type: 'GET',
            data: { 
                user_id: currentUserId,
                last_id: lastMessageId
            },
            success: function(response) {
                const messages = JSON.parse(response);
                const container = document.getElementById('messagesContainer');
                
                messages.forEach(message => {
                    if (message.message_id > lastMessageId) {
                        const isOutgoing = message.sender_id == <?php echo $_SESSION['account_id']; ?>;
                        container.innerHTML += `
                            <div class="message ${isOutgoing ? 'outgoing' : 'incoming'}">
                                <div class="message-content">${message.message}</div>
                            </div>
                        `;
                        lastMessageId = message.message_id;
                    }
                });
                
                container.scrollTop = container.scrollHeight;
            }
        });
    }

    function sendMessage() {
        if (!currentUserId) {
            alert('Vui lòng chọn người dùng để chat');
            return;
        }

        const input = document.getElementById('messageInput');
        const message = input.value.trim();
        if (message) {
            $.ajax({
                url: 'send_admin_message.php',
                type: 'POST',
                data: {
                    receiver_id: currentUserId,
                    message: message
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        input.value = '';
                        loadMessages();
                    } else {
                        alert(data.message);
                    }
                }
            });
        }
    }

    // Load users khi trang được tải
    loadUsers();
    // Kiểm tra tin nhắn mới mỗi 5 giây
    setInterval(loadMessages, 5000);
    </script>
</body>
</html> 