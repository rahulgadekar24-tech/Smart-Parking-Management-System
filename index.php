<?php
session_start();
include("includes/header.php");
?>

<style>
  /* ── HERO WRAPPER ── */
  .hero {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1.5rem 4rem;
    position: relative;
    overflow: hidden;
  }

  /* Grid background */
  .hero::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image:
      linear-gradient(rgba(26,86,219,0.04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(26,86,219,0.04) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
    z-index: 0;
  }

  /* Radial glow blobs */
  .hero::after {
    content: '';
    position: fixed;
    top: -30%;
    right: -20%;
    width: 700px;
    height: 700px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(59,111,239,0.08) 0%, transparent 65%);
    pointer-events: none;
    z-index: 0;
  }

  .blob-bottom {
    position: fixed;
    bottom: -20%;
    left: -15%;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(201,150,60,0.07) 0%, transparent 65%);
    pointer-events: none;
    z-index: 0;
  }

  /* ── BADGE ── */
  .badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--accent-light);
    color: var(--accent);
    font-family: 'Outfit', sans-serif;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 100px;
    border: 1px solid rgba(26,86,219,0.15);
    margin-bottom: 1.75rem;
    position: relative;
    z-index: 1;
    animation: slideDown 0.6s ease both;
  }

  .badge-dot {
    width: 6px;
    height: 6px;
    background: var(--accent);
    border-radius: 50%;
    animation: pulse 2s ease infinite;
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.75); }
  }

  /* ── HEADLINE ── */
  h1.headline {
    font-family: 'Outfit', sans-serif;
    font-size: clamp(2.6rem, 6vw, 4.2rem);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -0.02em;
    color: var(--ink);
    text-align: center;
    max-width: 640px;
    position: relative;
    z-index: 1;
    animation: slideDown 0.6s 0.1s ease both;
  }

  h1.headline .accent-word {
    color: var(--accent);
    position: relative;
  }

  h1.headline .accent-word::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -2px;
    width: 100%;
    height: 3px;
    background: var(--accent);
    border-radius: 2px;
    transform: scaleX(0);
    transform-origin: left;
    animation: underlineGrow 0.5s 0.8s ease forwards;
  }

  @keyframes underlineGrow {
    to { transform: scaleX(1); }
  }

  /* ── SUBHEADING ── */
  .subhead {
    margin-top: 1.1rem;
    font-size: 1.05rem;
    font-weight: 300;
    color: var(--ink-soft);
    text-align: center;
    letter-spacing: 0.01em;
    max-width: 420px;
    line-height: 1.6;
    position: relative;
    z-index: 1;
    animation: slideDown 0.6s 0.2s ease both;
  }

  /* ── STAT STRIP ── */
  .stats {
    display: flex;
    gap: 2.5rem;
    margin: 2.25rem 0;
    position: relative;
    z-index: 1;
    animation: slideDown 0.6s 0.3s ease both;
  }

  .stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
  }

  .stat-num {
    font-family: 'Outfit', sans-serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: -0.02em;
  }

  .stat-label {
    font-size: 0.7rem;
    font-weight: 400;
    color: var(--ink-mute);
    text-transform: uppercase;
    letter-spacing: 0.07em;
  }

  .stat-divider {
    width: 1px;
    height: 36px;
    background: var(--border-strong);
    align-self: center;
  }

  /* ── CARDS ── */
  .card-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    width: 100%;
    max-width: 640px;
    position: relative;
    z-index: 1;
    animation: slideUp 0.7s 0.35s ease both;
  }

  .card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2rem 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 0;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    position: relative;
    overflow: hidden;
  }

  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04);
    border-color: var(--border-strong);
  }

  /* Subtle top-accent bar */
  .card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: var(--radius) var(--radius) 0 0;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
  }

  .card:hover::before {
    transform: scaleX(1);
  }

  .card.user::before { background: var(--accent); }
  .card.admin::before { background: var(--gold); }

  /* Card icon */
  .card-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.1rem;
    flex-shrink: 0;
  }

  .card.user .card-icon {
    background: var(--accent-light);
  }

  .card.admin .card-icon {
    background: var(--gold-light);
  }

  .card-icon svg {
    width: 22px;
    height: 22px;
  }

  .card.user .card-icon svg { color: var(--accent); }
  .card.admin .card-icon svg { color: var(--gold); }

  /* Card role label */
  .card-role {
    font-family: 'Outfit', sans-serif;
    font-size: 0.65rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-bottom: 0.35rem;
  }

  .card.user .card-role { color: var(--accent); }
  .card.admin .card-role { color: var(--gold); }

  /* Card title */
  .card h2 {
    font-family: 'Outfit', sans-serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: -0.01em;
    margin-bottom: 0.45rem;
  }

  /* Card description */
  .card p {
    font-size: 0.85rem;
    color: var(--ink-mute);
    line-height: 1.55;
    margin-bottom: 1.5rem;
  }

  /* Card actions */
  .card-actions {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    margin-top: auto;
  }

  /* Buttons */
  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 0.65rem 1rem;
    border-radius: var(--radius-sm);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    white-space: nowrap;
  }

  .btn-primary {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
  }

  .btn-primary:hover {
    background: #1446c0;
    border-color: #1446c0;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(26,86,219,0.28);
  }

  .btn-secondary {
    background: transparent;
    color: var(--accent);
    border-color: rgba(26,86,219,0.2);
  }

  .btn-secondary:hover {
    background: var(--accent-light);
    border-color: rgba(26,86,219,0.35);
    transform: translateY(-1px);
  }

  .btn-gold {
    background: var(--gold);
    color: #fff;
    border-color: var(--gold);
  }

  .btn-gold:hover {
    background: #b5852f;
    border-color: #b5852f;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(201,150,60,0.3);
  }

  /* Arrow inside buttons */
  .btn-arrow {
    display: inline-block;
    transition: transform 0.2s ease;
  }

  .btn:hover .btn-arrow {
    transform: translateX(3px);
  }

  /* ── FOOTER NOTE ── */
  .footer-note {
    margin-top: 2.5rem;
    font-size: 0.75rem;
    color: var(--ink-mute);
    display: flex;
    align-items: center;
    gap: 6px;
    position: relative;
    z-index: 1;
    animation: slideUp 0.6s 0.5s ease both;
  }

  .footer-note svg {
    width: 13px;
    height: 13px;
    opacity: 0.5;
  }

  /* ── ANIMATIONS ── */
  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-18px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  @keyframes slideUp {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ── RESPONSIVE ── */
  @media (max-width: 540px) {
    .card-container {
      grid-template-columns: 1fr;
      max-width: 360px;
    }

    .stats {
      gap: 1.5rem;
    }

    h1.headline {
      font-size: 2.2rem;
    }
  }
</style>

<div class="hero">
  <div class="blob-bottom"></div>

  <!-- Badge -->
  <div class="badge">
    <span class="badge-dot"></span>
    Smart Parking · v2.0
  </div>

  <!-- Headline -->
  <h1 class="headline">
    Park <span class="accent-word">smarter</span>,<br>not harder.
  </h1>

  <p class="subhead">
    Real-time slot availability, instant booking, and seamless access — all in one platform.
  </p>

  <!-- Stats strip -->
  <div class="stats">
    <div class="stat">
      <span class="stat-num">500+</span>
      <span class="stat-label">Slots</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat">
      <span class="stat-num">24/7</span>
      <span class="stat-label">Available</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat">
      <span class="stat-num">2 min</span>
      <span class="stat-label">Avg. booking</span>
    </div>
  </div>

  <!-- Cards -->
  <div class="card-container">

    <!-- User Card -->
    <div class="card user">
      <div class="card-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="8" r="4"/>
          <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
        </svg>
      </div>
      <span class="card-role">Driver</span>
      <h2>Park with ease</h2>
      <p>Find available slots, book in seconds, and manage all your parking from one place.</p>
      <div class="card-actions">
        <a href="login.php" class="btn btn-primary">
          Sign in <span class="btn-arrow">→</span>
        </a>
        <a href="register.php" class="btn btn-secondary">
          Create account
        </a>
      </div>
    </div>

    <!-- Admin Card -->
    <div class="card admin">
      <div class="card-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="3" width="20" height="14" rx="2"/>
          <path d="M8 21h8M12 17v4"/>
          <path d="M7 8h.01M11 8h6M7 11h.01M11 11h3"/>
        </svg>
      </div>
      <span class="card-role">Administrator</span>
      <h2>Manage the lot</h2>
      <p>Monitor occupancy, configure slots, generate reports, and oversee the full system.</p>
      <div class="card-actions">
        <a href="admin/admin_login.php" class="btn btn-gold">
          Admin panel <span class="btn-arrow">→</span>
        </a>
      </div>
    </div>

  </div>

  <!-- Footer note -->
  <p class="footer-note">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
      <rect x="3" y="11" width="18" height="11" rx="2"/>
      <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    </svg>
    Secured with encrypted authentication &amp; session management
  </p>

</div>

<?php include("includes/footer.php"); ?>