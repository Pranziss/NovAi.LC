// =====================
// Nova Learning Center
// Chatbot — Floating Bubble
// =====================

// --- CONFIG ---
// When running locally:        use http://localhost:5000
// When demoing with ngrok:     replace with your ngrok URL e.g. https://abcd1234.ngrok-free.app
const NOVA_API_URL = "http://127.0.0.1:5000/ask";

// --- STATE ---
let isOnline = false;
let isOpen = false;

// --- INIT ---
document.addEventListener("DOMContentLoaded", () => {
  injectChatbotHTML();
  checkNovaStatus();
});

// --- INJECT UI ---
function injectChatbotHTML() {
  const el = document.createElement("div");
  el.innerHTML = `
    <!-- Bubble Button -->
    <button id="nova-bubble" onclick="toggleChat()"
      class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110"
      style="background: linear-gradient(135deg, #2563eb, #4f46e5);"
      aria-label="Open Nova Assistant">
      <!-- Chat icon (open state) -->
      <svg id="nova-icon-chat" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
      </svg>
      <!-- Close icon (close state) -->
      <svg id="nova-icon-close" class="w-6 h-6 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
      <!-- Status dot -->
      <span id="nova-status-dot"
        class="absolute top-0 right-0 w-3.5 h-3.5 rounded-full border-2 border-white bg-gray-400">
      </span>
    </button>

    <!-- Chat Window -->
    <div id="nova-window"
      class="fixed bottom-24 right-6 z-50 w-80 sm:w-96 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 flex flex-col overflow-hidden transition-all duration-300 opacity-0 pointer-events-none translate-y-4"
      style="max-height: 520px;">

      <!-- Header -->
      <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-800"
        style="background: linear-gradient(135deg, #2563eb, #4f46e5);">
        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3M6.343 6.343l-.707-.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M12 21v-1M12 8a4 4 0 100 8 4 4 0 000-8z"/>
          </svg>
        </div>
        <div class="flex-1">
          <p class="text-white font-semibold text-sm font-jakarta">Nova Assistant</p>
          <p id="nova-status-text" class="text-blue-200 text-xs">Checking status...</p>
        </div>
        <button onclick="toggleChat()" class="text-white/70 hover:text-white transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Messages -->
      <div id="nova-messages" class="flex-1 overflow-y-auto p-4 space-y-3" style="min-height:300px; max-height:360px;">
        <!-- Populated by JS -->
      </div>

      <!-- Input -->
      <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center gap-2">
        <input id="nova-input" type="text"
          placeholder="Ask Nova something..."
          class="flex-1 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 outline-none focus:border-blue-400 transition-colors"
          onkeydown="if(event.key==='Enter') sendMessage()"
          disabled/>
        <button id="nova-send-btn" onclick="sendMessage()"
          class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 transition-all disabled:opacity-40"
          style="background: linear-gradient(135deg, #2563eb, #4f46e5);"
          disabled>
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
          </svg>
        </button>
      </div>
    </div>
  `;
  document.body.appendChild(el);
}

// --- TOGGLE WINDOW ---
function toggleChat() {
  isOpen = !isOpen;
  const win = document.getElementById("nova-window");
  const iconChat = document.getElementById("nova-icon-chat");
  const iconClose = document.getElementById("nova-icon-close");

  if (isOpen) {
    win.classList.remove("opacity-0", "pointer-events-none", "translate-y-4");
    win.classList.add("opacity-100", "translate-y-0");
    iconChat.classList.add("hidden");
    iconClose.classList.remove("hidden");
    if (document.getElementById("nova-messages").children.length === 0) {
      appendMessage("nova", isOnline
        ? "Hi! I'm Nova, your study assistant. Ask me anything about your quizzes or topics you're reviewing! 📚"
        : "Nova is currently offline. Start the local server and refresh to chat.");
    }
    document.getElementById("nova-input").focus();
  } else {
    win.classList.add("opacity-0", "pointer-events-none", "translate-y-4");
    win.classList.remove("opacity-100", "translate-y-0");
    iconChat.classList.remove("hidden");
    iconClose.classList.add("hidden");
  }
}

// --- CHECK STATUS ---
async function checkNovaStatus() {
  const dot = document.getElementById("nova-status-dot");
  const statusText = document.getElementById("nova-status-text");
  const input = document.getElementById("nova-input");
  const btn = document.getElementById("nova-send-btn");

  try {
    // Ping the server with a lightweight request
    const res = await fetch(NOVA_API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message: "__ping__" }),
      signal: AbortSignal.timeout(4000)
    });

    if (res.ok) {
      isOnline = true;
      dot.classList.replace("bg-gray-400", "bg-emerald-400");
      if (statusText) statusText.textContent = "Online • Ready to help";
      input.disabled = false;
      btn.disabled = false;
    } else {
      setOffline(dot, statusText);
    }
  } catch {
    setOffline(dot, statusText);
  }
}

function setOffline(dot, statusText) {
  isOnline = false;
  dot.classList.remove("bg-emerald-400");
  dot.classList.add("bg-gray-400");
  if (statusText) statusText.textContent = "Offline • Start local server";
}

// --- SEND MESSAGE ---
async function sendMessage() {
  const input = document.getElementById("nova-input");
  const msg = input.value.trim();
  if (!msg || !isOnline) return;

  input.value = "";
  appendMessage("user", msg);
  showTyping();

  try {
    const res = await fetch(NOVA_API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message: msg }),
      signal: AbortSignal.timeout(15000)
    });

    removeTyping();

    if (!res.ok) throw new Error("Bad response");
    const data = await res.json();
    appendMessage("nova", data.response || "I didn't catch that, could you try again?");
  } catch (err) {
    removeTyping();
    appendMessage("nova", "Couldn't reach Nova right now. Make sure the local server is running.");
    console.error("[Nova Chatbot Error]", err);
  }
}

// --- APPEND MESSAGE ---
function appendMessage(sender, text) {
  const container = document.getElementById("nova-messages");
  const isNova = sender === "nova";

  const wrapper = document.createElement("div");
  wrapper.className = `flex ${isNova ? "justify-start" : "justify-end"} items-end gap-2`;

  const bubble = document.createElement("div");
  bubble.className = isNova
    ? "max-w-[80%] bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm rounded-2xl rounded-bl-sm px-4 py-2.5 leading-relaxed"
    : "max-w-[80%] text-white text-sm rounded-2xl rounded-br-sm px-4 py-2.5 leading-relaxed";

  if (!isNova) bubble.style.background = "linear-gradient(135deg, #2563eb, #4f46e5)";
  bubble.textContent = text;

  wrapper.appendChild(bubble);
  container.appendChild(wrapper);
  container.scrollTop = container.scrollHeight;
}

// --- TYPING INDICATOR ---
function showTyping() {
  const container = document.getElementById("nova-messages");
  const el = document.createElement("div");
  el.id = "nova-typing";
  el.className = "flex justify-start items-end gap-2";
  el.innerHTML = `
    <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-bl-sm px-4 py-3 flex gap-1 items-center">
      <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0s"></span>
      <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.15s"></span>
      <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.3s"></span>
    </div>`;
  container.appendChild(el);
  container.scrollTop = container.scrollHeight;
}

function removeTyping() {
  const el = document.getElementById("nova-typing");
  if (el) el.remove();
}