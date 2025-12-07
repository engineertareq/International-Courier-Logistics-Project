<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Front-end Coming Soon</title>

  <!-- Google Font (optional) -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">

  <style>
    :root{
      --bg1: #0f172a;
      --bg2: #081229;
      --card: rgba(255,255,255,0.04);
      --accent: #6ee7b7;
      --muted: rgba(255,255,255,0.65);
      --glass: rgba(255,255,255,0.03);
      font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }
    html,body{
      height:100%;
      margin:0;
      background: linear-gradient(135deg,var(--bg1) 0%, var(--bg2) 100%);
      color: white;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    .wrap{
      min-height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:48px 20px;
      box-sizing:border-box;
    }

    .card{
      width:100%;
      max-width:920px;
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border-radius:18px;
      box-shadow: 0 10px 30px rgba(4,6,15,0.6);
      padding:36px;
      display:grid;
      grid-template-columns: 1fr 380px;
      gap:28px;
      align-items:center;
      border: 1px solid rgba(255,255,255,0.04);
    }

    /* left column */
    .brand{
      display:flex;
      gap:16px;
      align-items:center;
      margin-bottom:6px;
    }
    .logo{
      width:64px;
      height:64px;
      border-radius:12px;
      background: linear-gradient(135deg,#1e293b,#0ea5a9);
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:800;
      color:white;
      font-size:20px;
      box-shadow: 0 6px 18px rgba(14,165,169,0.14);
    }
    h1{
      margin:0 0 10px 0;
      font-size:28px;
      line-height:1.05;
      letter-spacing:-0.2px;
    }
    p.lead{
      margin:0 0 18px 0;
      color:var(--muted);
      max-width:56ch;
    }

    .status{
      display:flex;
      gap:12px;
      align-items:center;
    }

    .spinner{
      width:36px;
      height:36px;
      border-radius:50%;
      display:inline-grid;
      place-items:center;
      background: conic-gradient(var(--accent), transparent 50%);
      filter: blur(6px) contrast(120%);
      position:relative;
    }
    .spinner::after{
      content:"";
      position:absolute;
      width:22px;
      height:22px;
      border-radius:50%;
      background:linear-gradient(180deg,#052b2b, #0b3b3b);
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
    }
    @keyframes spin {
      from{ transform: rotate(0deg); }
      to{ transform: rotate(360deg); }
    }
    .spinner-anim{
      animation: spin 2.4s linear infinite;
      width:36px;
      height:36px;
      position:relative;
    }

    /* right column */
    .panel{
      background: var(--glass);
      border-radius:12px;
      padding:18px;
      display:flex;
      flex-direction:column;
      gap:12px;
      align-items:stretch;
      border: 1px solid rgba(255,255,255,0.03);
    }
    .panel small{ color:var(--muted); }
    .email-row{
      display:flex;
      gap:8px;
    }
    input[type="email"]{
      flex:1;
      padding:10px 12px;
      border-radius:10px;
      border: none;
      outline: none;
      background: rgba(255,255,255,0.02);
      color: white;
      font-size:14px;
    }
    button.btn{
      padding:10px 14px;
      border-radius:10px;
      border:none;
      cursor:pointer;
      background: linear-gradient(90deg,#06b6d4,#06d6a0);
      color:#042027;
      font-weight:700;
      letter-spacing:0.2px;
    }
    .meta{
      display:flex;
      justify-content:space-between;
      gap:10px;
      font-size:13px;
      color:var(--muted);
    }

    /* rocket graphic */
    .illus{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:12px;
    }
    .rocket{
      width:220px;
      height:160px;
    }

    footer.small{
      margin-top:18px;
      font-size:13px;
      color:var(--muted);
    }

    /* responsive */
    @media (max-width:880px){
      .card{
        grid-template-columns: 1fr;
        padding:22px;
      }
      .rocket{ width:100%; height:120px; }
    }
  </style>
</head>
<body>
  <main class="wrap" role="main">
    <section class="card" aria-labelledby="title">
      <div>
        <div class="brand" aria-hidden="true">
          <div class="logo" title="Logo">You</div>
          <div>
            <div style="font-size:14px;color:var(--muted)">Courier & Logistics</div>
            <div style="font-size:12px;color:rgba(255,255,255,0.18)">Under Maintenance</div>
          </div>
        </div>

        <h1 id="title">Front-end not ready yet.</h1>
        <p class="lead">We're putting the finishing touches on the front-end. Please come back later — we'll be live soon. Thanks for your patience!</p>

        <div class="status" aria-live="polite">
          <div class="spinner spinner-anim" aria-hidden="true"></div>
          <div>
            <div style="font-weight:700">Building the experience</div>
            <div style="color:var(--muted);font-size:13px">Currently working on the user interface and responsive layout.</div>
          </div>
        </div>

        <footer class="small" aria-hidden="true">
          <div style="margin-top:14px">Need updates? Leave your email — we'll notify you when it's ready.</div>
        </footer>
      </div>

      <aside class="panel" aria-labelledby="updates">
        <div id="updates" style="font-weight:700">Get notified</div>
        <small>Enter your email. We'll only use it to send one notification when the front-end is ready.</small>

        <form id="notifyForm" class="email-row" onsubmit="event.preventDefault();subscribe();">
          <label for="email" class="sr-only" style="position:absolute;left:-9999px;">Email</label>
          <input id="email" type="email" placeholder="me@tareq.com" aria-label="Email for notification" />
          <button class="btn" type="submit" aria-label="Notify me">Notify Us</button>
        </form>

        <div class="meta">
          <div>ETA: <strong id="etaText">Soon</strong></div>
          <div id="timeNow">Local time: --</div>
        </div>

        <div style="margin-top:6px;font-size:13px;color:var(--muted)">Or follow us on social media for progress updates.</div>

        <div style="display:flex;gap:8px;margin-top:8px">
          <a href="#" aria-label="Facebook" style="text-decoration:none;padding:8px;border-radius:8px;background:rgba(255,255,255,0.02);font-weight:700">f</a>
          <a href="#" aria-label="Twitter/X" style="text-decoration:none;padding:8px;border-radius:8px;background:rgba(255,255,255,0.02);font-weight:700">x</a>
          <a href="#" aria-label="Instagram" style="text-decoration:none;padding:8px;border-radius:8px;background:rgba(255,255,255,0.02);font-weight:700">📷</a>
        </div>

        <div style="margin-top:10px">
          <svg class="rocket" viewBox="0 0 600 400" role="img" aria-hidden="false" focusable="false">
            <defs>
              <linearGradient id="g1" x1="0" x2="1"><stop offset="0" stop-color="#06b6d4"/><stop offset="1" stop-color="#06d6a0"/></linearGradient>
            </defs>
            <g transform="translate(60,20) scale(0.9)">
              <ellipse cx="210" cy="260" rx="160" ry="20" fill="rgba(0,0,0,0.12)"/>
              <path d="M210 20c-30 12-60 38-80 70-18 30-28 68-24 98 28 6 48-8 64-20 20-14 40-14 60 0 24 16 40 22 66 14 1-32-6-66-24-98-18-30-46-54-122-64z" fill="url(#g1)">
                <animateTransform attributeName="transform" type="translate" values="0 0;0 -6;0 0" dur="3s" repeatCount="indefinite"/>
              </path>
              <circle cx="210" cy="120" r="14" fill="#001219"/>
              <g transform="translate(154,238)">
                <rect width="112" height="34" rx="16" fill="#ffb86b">
                  <animate attributeName="y" values="0;6;0" dur="1.6s" repeatCount="indefinite"/>
                </rect>
              </g>
            </g>
          </svg>
        </div>
      </aside>
    </section>
  </main>

  <script>
    // Small helpers: email feedback & local time display
    function subscribe(){
      const email = document.getElementById('email').value.trim();
      if(!email){
        alert('Thanks — we will notify you when the site is ready.');
        return;
      }
      // Placeholder behavior: you can replace this with an API call.
      alert('Thanks! We saved: ' + email + '. We will Back Soon');
      document.getElementById('email').value = '';
    }

    // Show local time
    function updateTime(){
      const now = new Date();
      const time = now.toLocaleString();
      document.getElementById('timeNow').textContent = 'Local time: ' + time;
    }
    updateTime();
    setInterval(updateTime, 1000 * 30);

    // Optional: set ETA (you can set a real date/time)
    (function setEta(){
      // If you want a static date, replace with: new Date('2026-01-15T10:00:00')
      const etaText = document.getElementById('etaText');
      etaText.textContent = 'coming soon';
    })();
  </script>
</body>
</html>
