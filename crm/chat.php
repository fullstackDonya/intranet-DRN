<?php
require_once __DIR__ . '/includes/verify_subscriptions.php';

if (!function_exists('isAuthenticated') || !isAuthenticated()) { header('Location: login.php'); exit; }
$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) { echo 'Client non identifié'; exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Messagerie</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/chat.css">

  <style>
    .chat-layout{display:flex;gap:12px}
    .chat-list{width:280px;border:1px solid #ddd;border-radius:8px;overflow:auto;max-height:70vh}
    .chat-messages{flex:1;border:1px solid #ddd;border-radius:8px;display:flex;flex-direction:column;max-height:70vh}
    .chat-messages .msgs{flex:1;overflow:auto;padding:12px}
    .chat-messages form{display:flex;gap:8px;padding:8px;border-top:1px solid #eee}
  </style>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container mt-3">
  <h2>Messagerie client</h2>
  <div class="chat-layout">
    <div class="chat-list" id="convList"></div>
    <div class="chat-messages">
      <div class="msgs" id="msgs"></div>
      <form id="sendForm">
        <input type="text" id="msgInput" placeholder="Votre message..." style="flex:1" />
        <button type="submit">Envoyer</button>
      </form>
    </div>
  </div>
</div>
<script src="../assets/js/realtime.js"></script>
<script>
(function(){
  let currentConversationId = null;
  const convList = document.getElementById('convList');
  const msgs = document.getElementById('msgs');
  const form = document.getElementById('sendForm');
  const input = document.getElementById('msgInput');

  function escapeHtml(s){return (s||'').toString().replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]));}

  function loadConversations(){
    fetch('../api/messages.php?action=conversations').then(r=>r.json()).then(d=>{
      convList.innerHTML='';
      (d.conversations||[]).forEach(c=>{
        const el = document.createElement('div');
        el.className='item';
        el.textContent = 'Conversation #' + c.id + ' · ' + (c.type || '');
        el.style.padding='8px';
        el.style.cursor='pointer';
        el.onclick = () => { currentConversationId = c.id; loadMessages(c.id); };
        convList.appendChild(el);
      });
    });
  }

  function loadMessages(cid){
    fetch('../api/messages.php?action=messages&conversation_id='+encodeURIComponent(cid)).then(r=>r.json()).then(d=>{
      msgs.innerHTML='';
      (d.messages||[]).forEach(m=> addMessage(m));
      msgs.scrollTop = msgs.scrollHeight;
    });
  }

  function addMessage(m){
    const p = document.createElement('div');
    p.innerHTML = '<b>' + escapeHtml(m.sender_type) + '</b>: ' + escapeHtml(m.body) + ' <small>(' + escapeHtml(m.created_at) + ')</small>';
    msgs.appendChild(p);
  }

  form.addEventListener('submit', function(e){
    e.preventDefault();
    if (!currentConversationId) return;
    const body = input.value.trim(); if (!body) return;
    const fd = new URLSearchParams();
    fd.append('conversation_id', currentConversationId);
    fd.append('body', body);
    fetch('../api/messages.php?action=send_message', { method:'POST', body: fd })
      .then(r=>r.json()).then(()=>{ input.value=''; });
  });

  // Realtime
  if (window.WebitechRealtime){
    const rt = WebitechRealtime({ endpoint: '../api/realtime.php', topics: ['messages'] });
    rt.on('message.created', function(evt){
      const d = evt && evt.data || evt; // from SSE payload
      if (!d || d.type !== 'message.created') return;
      if (currentConversationId && d.data && d.data.conversation_id == currentConversationId){
        addMessage({ sender_type: d.data.sender_type || 'user', body: d.data.body || '', created_at: new Date().toISOString() });
        msgs.scrollTop = msgs.scrollHeight;
      }
    });
    rt.start();
  }

  // init
  loadConversations();
})();
</script>
</body>
</html>