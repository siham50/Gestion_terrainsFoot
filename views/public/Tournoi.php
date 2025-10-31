<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tournoi 8 équipes — FootTime</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    .tournament-shell {
      margin-left: 240px; 
      padding: 36px 24px;
      min-height: calc(100vh - 60px);
    }

    .bracket-wrap {
      position: relative;
      width: 100%;
      padding: 24px 8px;
      min-height: 480px;
    }

    .bracket {
      position: relative;
      height: 520px;        
      width: 1100px;       
      margin: 0 auto;
    }

    .match {
      position: absolute; 
      width: 200px;
      background: var(--ft-panel);
      border: 1px solid var(--ft-border);
      border-radius: 12px;
      padding: 8px 12px;
      box-shadow: var(--ft-shadow);
      color: var(--ft-text);
      text-align: left;
      font-size: 14px;
    }

    .match .team {
      display:flex;
      justify-content:space-between;
      padding:8px 6px;
      border-radius:8px;
      background: rgba(255,255,255,0.02);
      margin:6px 0;
    }

    .match .team .name { flex:1; }
    .match .team .score {
      width:36px;
      text-align:center;
      font-weight:700;
      color:var(--ft-accent);
    }

    .match .team.winner { font-weight:700; color:var(--ft-accent); }

    .champion {
      border-color: var(--ft-accent);
      background: rgba(43,217,151,0.06);
      text-align:center;
      font-weight:700;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:8px;
    }

    .bracket-svg {
      position:absolute;
      left:0; top:0;
      width:100%; height:100%;
      pointer-events: none;
    }

    @media (max-width: 900px) {
      .tournament-shell { margin-left: 0; padding: 16px; }
      .bracket { width: 1000px; }
    }
  </style>
</head>
<body>
  <?php require '../../includes/Navbar.php'; ?>

  <main class="tournament-shell">
    <h1 class="ft-h1" style="text-align:center; margin-bottom:14px;">Tournoi Inter-Équipes — 8 équipes</h1>

    <div class="bracket-wrap">
      <div class="bracket" id="bracket">

        <svg class="bracket-svg" id="bracket-svg" xmlns="http://www.w3.org/2000/svg"></svg>

        <div class="match" data-id="q1" data-round="1" data-index="1">
          <div style="font-size:12px;color:var(--ft-text-dim);">QF 1</div>
          <div class="team"><div class="name">Team 1</div><div class="score">2</div></div>
          <div class="team winner"><div class="name">Team 2</div><div class="score">3</div></div>
        </div>

        <div class="match" data-id="q2" data-round="1" data-index="2">
          <div style="font-size:12px;color:var(--ft-text-dim);">QF 2</div>
          <div class="team winner"><div class="name">Team 3</div><div class="score">1</div></div>
          <div class="team"><div class="name">Team 4</div><div class="score">0</div></div>
        </div>

        <div class="match" data-id="q3" data-round="1" data-index="3">
          <div style="font-size:12px;color:var(--ft-text-dim);">QF 3</div>
          <div class="team"><div class="name">Team 5</div><div class="score">0</div></div>
          <div class="team winner"><div class="name">Team 6</div><div class="score">2</div></div>
        </div>

        <div class="match" data-id="q4" data-round="1" data-index="4">
          <div style="font-size:12px;color:var(--ft-text-dim);">QF 4</div>
          <div class="team"><div class="name">Team 7</div><div class="score">1</div></div>
          <div class="team winner"><div class="name">Team 8</div><div class="score">4</div></div>
        </div>

        <div class="match" data-id="s1" data-round="2" data-index="1">
          <div style="font-size:12px;color:var(--ft-text-dim);">SF 1</div>
          <div class="team"><div class="name">Winner QF1</div><div class="score">1</div></div>
          <div class="team winner"><div class="name">Winner QF2</div><div class="score">2</div></div>
        </div>

        <div class="match" data-id="s2" data-round="2" data-index="2">
          <div style="font-size:12px;color:var(--ft-text-dim);">SF 2</div>
          <div class="team winner"><div class="name">Winner QF3</div><div class="score">2</div></div>
          <div class="team"><div class="name">Winner QF4</div><div class="score">0</div></div>
        </div>

        <div class="match" data-id="f1" data-round="3" data-index="1">
          <div style="font-size:12px;color:var(--ft-text-dim);">Finale</div>
          <div class="team"><div class="name">Winner SF1</div><div class="score">1</div></div>
          <div class="team winner"><div class="name">Winner SF2</div><div class="score">3</div></div>
        </div>

        <div class="match champion" data-id="champ" data-round="4" data-index="1">
          🏆 Champion: Winner F
        </div>

      </div>
    </div>
  </main>

  <?php require '../../includes/Footer.php'; ?>

  <script>
    (function () {
      const bracket = document.getElementById('bracket');
      const svg = document.getElementById('bracket-svg');

      const colX = {1: 20, 2: 270, 3: 520, 4: 770};
      const matchWidth = 200;
      const matchHeight = 88;
      const vertGap = 50; 

      const matches = Array.from(bracket.querySelectorAll('.match'))
        .map(el => ({
          el,
          id: el.dataset.id,
          round: parseInt(el.dataset.round, 10),
          index: parseInt(el.dataset.index, 10)
        }));

      const rounds = {};
      matches.forEach(m => {
        if (!rounds[m.round]) rounds[m.round] = [];
        rounds[m.round].push(m);
      });

      Object.keys(rounds).forEach(r => {
        rounds[r].sort((a,b)=>a.index-b.index);
      });

      function layoutAndDraw() {
        while (svg.firstChild) svg.removeChild(svg.firstChild);

        const qCount = (rounds[1] || []).length || 4;
        const totalHeight = qCount * matchHeight + (qCount - 1) * vertGap + 120;
        bracket.style.height = totalHeight + 'px';
        svg.setAttribute('width', bracket.clientWidth);
        svg.setAttribute('height', bracket.clientHeight);

        const topStart = 40;
        (rounds[1] || []).forEach((m, i) => {
          const x = colX[1];
          const y = topStart + i * (matchHeight + vertGap);
          m.el.style.left = x + 'px';
          m.el.style.top = y + 'px';
          m.el.style.width = matchWidth + 'px';
        });

        if (rounds[2]) {
          rounds[2].forEach((m, i) => {
            const childA = rounds[1][i * 2];
            const childB = rounds[1][i * 2 + 1];
            const xa = parseFloat(childA.el.style.left) + matchWidth;
            const ya = parseFloat(childA.el.style.top) + matchHeight / 2;
            const xb = parseFloat(childB.el.style.left) + matchWidth;
            const yb = parseFloat(childB.el.style.top) + matchHeight / 2;
            const x = colX[2];
            const y = (ya + yb) / 2 - matchHeight / 2;
            m.el.style.left = x + 'px';
            m.el.style.top = y + 'px';
            m.el.style.width = matchWidth + 'px';
            drawConnector(childA, m);
            drawConnector(childB, m);
          });
        }

        if (rounds[3]) {
          rounds[3].forEach((m, i) => {
            const childA = rounds[2][0];
            const childB = rounds[2][1];
            const ya = parseFloat(childA.el.style.top) + matchHeight / 2;
            const yb = parseFloat(childB.el.style.top) + matchHeight / 2;
            const x = colX[3];
            const y = (ya + yb) / 2 - matchHeight / 2;
            m.el.style.left = x + 'px';
            m.el.style.top = y + 'px';
            m.el.style.width = matchWidth + 'px';
            drawConnector(childA, m);
            drawConnector(childB, m);
          });
        }

        if (rounds[4]) {
          rounds[4].forEach((m, i) => {
            const child = rounds[3][0];
            const yc = parseFloat(child.el.style.top) + matchHeight / 2;
            const x = colX[4];
            const y = yc - matchHeight / 2;
            m.el.style.left = x + 'px';
            m.el.style.top = y + 'px';
            m.el.style.width = matchWidth - 40 + 'px';
            drawConnector(child, m);
          });
        }

      }

      function drawConnector(source, target) {
        const sBox = source.el.getBoundingClientRect();
        const tBox = target.el.getBoundingClientRect();
        const containerBox = bracket.getBoundingClientRect();

        const sx = (sBox.left - containerBox.left) + sBox.width;
        const sy = (sBox.top - containerBox.top) + sBox.height / 2;
        const tx = (tBox.left - containerBox.left);
        const ty = (tBox.top - containerBox.top) + tBox.height / 2;

        const dx = Math.max(30, (tx - sx) * 0.4);
        const path = document.createElementNS('http://www.w3.org/2000/svg','path');
        const d = `M ${sx} ${sy} C ${sx + dx} ${sy} ${tx - dx} ${ty} ${tx} ${ty}`;
        path.setAttribute('d', d);
        path.setAttribute('stroke', 'rgba(43,217,151,0.9)');
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke-width', '3');
        path.setAttribute('stroke-linecap', 'round');
        svg.appendChild(path);
      }

      function refresh() {
        layoutAndDraw();
      }

      window.addEventListener('load', refresh);
      window.addEventListener('resize', function () {
        if (window._bracketResize) clearTimeout(window._bracketResize);
        window._bracketResize = setTimeout(refresh, 100);
      });
    })();
  </script>
</body>
</html>
