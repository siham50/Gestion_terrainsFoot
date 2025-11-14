<?php
// views/public/Tournoi.php
$GLOBALS['contentDisplayed'] = true;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer l'ID du tournoi
$idTournoi = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idTournoi <= 0) {
    header('Location: MesTournois.php');
    exit;
}

// Charger les données depuis le contrôleur
require_once __DIR__ . '/../../controllers/TournoiController.php';
require_once __DIR__ . '/../../classes/Database.php';

$controller = new TournoiController();
$tournoi = $controller->viewTournoi($idTournoi);

if (!$tournoi) {
    header('Location: MesTournois.php?error=Tournoi introuvable');
    exit;
}

// Vérifier les permissions pour modifier les scores
$canEdit = false;
if (isset($_SESSION['user_id'])) {
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    $isOrganisateur = $tournoi['idUtilisateur'] == $_SESSION['user_id'];
    $canEdit = $isAdmin || $isOrganisateur;
}

// Organiser les matchs par rounds
$bracket = $tournoi['bracket'] ?? [];
$stats = $tournoi['stats'] ?? [];

// Déterminer le nombre de rounds
$nombreRounds = count($bracket);
$roundNames = [
    1 => 'Huitièmes de finale',
    2 => 'Quarts de finale',
    3 => 'Demi-finales',
    4 => 'Finale'
];

// Pour 8 équipes
if (count($tournoi['equipes'] ?? []) === 8) {
    $roundNames = [
        1 => 'Quarts de finale',
        2 => 'Demi-finales',
        3 => 'Finale'
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?php echo htmlspecialchars($tournoi['format'] ?? 'Tournoi'); ?> — FootTime</title>
    <link rel="stylesheet" href="../../assets/css/Style.css">
    <style>
        .tournament-shell {
            margin-left: 240px; 
            padding: 36px 24px;
            min-height: calc(100vh - 60px);
        }

        .tournament-header {
            margin-bottom: 32px;
        }

        .tournament-header h1 {
            margin-bottom: 8px;
        }

        .tournament-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .stat-card {
            padding: 16px;
            background: var(--ft-panel);
            border: 1px solid var(--ft-border);
            border-radius: 12px;
            min-width: 150px;
        }

        .stat-card .label {
            font-size: 12px;
            color: var(--ft-text-dim);
            margin-bottom: 4px;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: 700;
            color: var(--ft-accent);
        }

        .bracket-wrap {
            position: relative;
            width: 100%;
            padding: 24px 8px;
            min-height: 480px;
            overflow-x: auto;
        }

        .bracket {
            position: relative;
            min-height: 520px;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .match {
            position: absolute; 
            width: 220px;
            background: var(--ft-panel);
            border: 1px solid var(--ft-border);
            border-radius: 12px;
            padding: 12px;
            box-shadow: var(--ft-shadow);
            color: var(--ft-text);
            text-align: left;
            font-size: 14px;
            z-index: 10;
        }

        .match.has-winner {
            border-color: var(--ft-accent);
            background: rgba(43, 217, 151, 0.05);
        }

        .match .round-label {
            font-size: 11px;
            color: var(--ft-text-dim);
            margin-bottom: 8px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .match .team {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-radius: 8px;
            background: rgba(255,255,255,0.02);
            margin: 6px 0;
            min-height: 36px;
        }

        .match .team.winner {
            font-weight: 700;
            color: var(--ft-accent);
            background: rgba(43, 217, 151, 0.1);
        }

        .match .team .name {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .match .team .score {
            width: 40px;
            text-align: center;
            font-weight: 700;
            color: var(--ft-accent);
            font-size: 16px;
        }

        .match .score-input {
            display: flex;
            gap: 4px;
            margin-top: 8px;
            align-items: center;
        }

        .match .score-input input {
            width: 50px;
            padding: 4px 8px;
            border: 1px solid var(--ft-border);
            border-radius: 4px;
            background: var(--ft-panel);
            color: var(--ft-text);
            text-align: center;
        }

        .match .score-input button {
            padding: 4px 12px;
            background: var(--ft-accent);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .match .score-input button:hover {
            opacity: 0.9;
        }

        .champion {
            border-color: var(--ft-accent);
            background: rgba(43,217,151,0.1);
            text-align: center;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 18px;
        }

        .bracket-svg {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .bracket-svg path {
            stroke: rgba(43, 217, 151, 0.6);
            stroke-width: 3;
            fill: none;
            stroke-linecap: round;
        }

        @media (max-width: 900px) {
            .tournament-shell {
                margin-left: 0;
                padding: 16px;
            }
            .bracket {
                width: 100%;
            }
        }

        .no-bracket {
            text-align: center;
            padding: 48px 24px;
            color: var(--ft-text-dim);
        }
    </style>
</head>
<body>
    <?php require '../../includes/Navbar.php'; ?>

    <main class="tournament-shell">
        <div class="tournament-header">
            <h1 class="ft-h1"><?php echo htmlspecialchars($tournoi['format'] ?? 'Tournoi'); ?></h1>
            <?php if (!empty($tournoi['champion'])): ?>
                <div style="font-size: 18px; color: var(--ft-accent); font-weight: 600; margin-top: 8px;">
                    🏆 Champion: <?php echo htmlspecialchars($tournoi['champion']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="tournament-stats">
            <div class="stat-card">
                <div class="label">Équipes</div>
                <div class="value"><?php echo count($tournoi['equipes'] ?? []); ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Matchs</div>
                <div class="value"><?php echo $stats['total_matchs'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Terminés</div>
                <div class="value"><?php echo $stats['matchs_termines'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">À venir</div>
                <div class="value"><?php echo $stats['matchs_a_venir'] ?? 0; ?></div>
            </div>
        </div>

        <div class="bracket-wrap">
            <?php if (empty($bracket)): ?>
                <div class="no-bracket ft-card">
                    <p>Le bracket n'a pas encore été généré.</p>
                    <?php if ($canEdit): ?>
                        <button class="ft-btn ft-btn-primary" onclick="generateBracket()" style="margin-top: 16px;">
                            Générer le bracket
                        </button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="bracket" id="bracket">
                    <svg class="bracket-svg" id="bracket-svg" xmlns="http://www.w3.org/2000/svg"></svg>
                    
                    <?php 
                    $matchIndex = 0;
                    foreach ($bracket as $round => $matches): 
                        $roundName = $roundNames[$round] ?? "Round $round";
                        foreach ($matches as $match):
                            $matchIndex++;
                            $hasWinner = !empty($match['gagnant']);
                            $isFinale = ($round === max(array_keys($bracket)));
                            $isChampion = $isFinale && $hasWinner;
                            $hasReservation = !empty($match['idReservation']);
                    ?>
                        <div class="match <?php echo $hasWinner ? 'has-winner' : ''; ?> <?php echo $isChampion ? 'champion' : ''; ?>" 
                             data-id="<?php echo $match['idMatch']; ?>"
                             data-round="<?php echo $round; ?>"
                             data-index="<?php echo $matchIndex; ?>"
                             data-next-match-id="<?php echo $match['nextMatchId'] ?? ''; ?>">
                            <div class="round-label"><?php echo htmlspecialchars($roundName); ?></div>
                            
                            <?php if ($isChampion): ?>
                                <div style="text-align: center;">
                                    🏆 <?php echo htmlspecialchars($match['gagnant'] ?? 'Champion'); ?>
                                </div>
                            <?php else: ?>
                                <div class="team <?php echo ($match['gagnant'] === $match['equipe']) ? 'winner' : ''; ?>">
                                    <div class="name"><?php echo htmlspecialchars($match['equipe'] ?? 'TBD'); ?></div>
                                    <div class="score"><?php 
                                        if ($match['score']) {
                                            $scores = explode('-', $match['score']);
                                            echo htmlspecialchars($scores[0] ?? '0');
                                        } else {
                                            echo '-';
                                        }
                                    ?></div>
                                </div>
                                <div class="team <?php echo ($match['gagnant'] === $match['equipeAdv']) ? 'winner' : ''; ?>">
                                    <div class="name"><?php echo htmlspecialchars($match['equipeAdv'] ?? 'TBD'); ?></div>
                                    <div class="score"><?php 
                                        if ($match['score']) {
                                            $scores = explode('-', $match['score']);
                                            echo htmlspecialchars($scores[1] ?? '0');
                                        } else {
                                            echo '-';
                                        }
                                    ?></div>
                                </div>
                                <?php if ($canEdit && !$hasWinner && !$hasReservation): ?>
                                    <div class="score-input">
                                        <button onclick="window.location.href='Home.php?from=bracket&idMatch=<?php echo $match['idMatch']; ?>'">Réserver</button>
                                    </div>
                                <?php endif; ?>

                                <?php if ($canEdit && !$hasWinner && $hasReservation): ?>
                                    <div class="score-input">
                                        <input type="number" min="0" id="score1_<?php echo $match['idMatch']; ?>" placeholder="0" style="width: 50px;">
                                        <span>-</span>
                                        <input type="number" min="0" id="score2_<?php echo $match['idMatch']; ?>" placeholder="0" style="width: 50px;">
                                        <button onclick="updateScore(<?php echo $match['idMatch']; ?>)">Valider</button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php 
                        endforeach;
                    endforeach; 
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 24px;">
            <a href="MesTournois.php" class="ft-btn ft-btn-secondary">← Retour aux tournois</a>
        </div>
    </main>

    <?php require '../../includes/Footer.php'; ?>

    <script>
        // Données du bracket pour le positionnement
        const bracketData = <?php echo json_encode($bracket, JSON_UNESCAPED_UNICODE); ?>;
        const roundNames = <?php echo json_encode($roundNames, JSON_UNESCAPED_UNICODE); ?>;

        // Positionner les matchs et dessiner les connexions
        function layoutBracket() {
            const bracket = document.getElementById('bracket');
            const svg = document.getElementById('bracket-svg');
            
            if (!bracket || !svg) return;

            // Nettoyer le SVG
            while (svg.firstChild) svg.removeChild(svg.firstChild);

            const matches = Array.from(bracket.querySelectorAll('.match'));
            const rounds = {};
            
            matches.forEach(m => {
                const round = parseInt(m.dataset.round, 10);
                if (!rounds[round]) rounds[round] = [];
                rounds[round].push(m);
            });

            // Trier par index
            Object.keys(rounds).forEach(r => {
                rounds[r].sort((a, b) => parseInt(a.dataset.index, 10) - parseInt(b.dataset.index, 10));
            });

            const matchWidth = 220;
            const matchHeight = 120;
            const vertGap = 60;
            const horzGap = 80;
            
            // Calculer les positions X pour chaque round
            const roundCount = Object.keys(rounds).length;
            const totalWidth = (roundCount - 1) * (matchWidth + horzGap) + matchWidth;
            const startX = 20;
            
            const colX = {};
            Object.keys(rounds).forEach((r, i) => {
                colX[parseInt(r, 10)] = startX + i * (matchWidth + horzGap);
            });

            // Positionner les matchs du premier round
            const firstRound = Math.min(...Object.keys(rounds).map(Number));
            const firstRoundMatches = rounds[firstRound];
            const firstRoundCount = firstRoundMatches.length;
            const totalHeight = firstRoundCount * matchHeight + (firstRoundCount - 1) * vertGap;
            const startY = 40;

            bracket.style.height = (totalHeight + 80) + 'px';
            svg.setAttribute('width', bracket.clientWidth);
            svg.setAttribute('height', bracket.clientHeight);

            firstRoundMatches.forEach((m, i) => {
                const x = colX[firstRound];
                const y = startY + i * (matchHeight + vertGap);
                m.style.left = x + 'px';
                m.style.top = y + 'px';
            });

            // Positionner les rounds suivants
            for (let round = firstRound + 1; round <= roundCount; round++) {
                if (!rounds[round]) continue;
                
                    rounds[round].forEach((m, i) => {
                    // Trouver les matchs précédents qui pointent vers celui-ci
                    const matchId = parseInt(m.dataset.id, 10);
                    const prevMatches = matches.filter(prev => {
                        const nextMatchId = prev.getAttribute('data-next-match-id');
                        return nextMatchId && parseInt(nextMatchId, 10) === matchId;
                    });

                    if (prevMatches.length > 0) {
                        const y1 = parseFloat(prevMatches[0].style.top) + matchHeight / 2;
                        const y2 = prevMatches.length > 1 ? parseFloat(prevMatches[1].style.top) + matchHeight / 2 : y1;
                        const y = (y1 + y2) / 2 - matchHeight / 2;
                        
                        const x = colX[round];
                        m.style.left = x + 'px';
                        m.style.top = y + 'px';

                        // Dessiner les connexions
                        prevMatches.forEach(prev => {
                            drawConnector(prev, m, svg, bracket);
                        });
                    }
                });
            }
        }

        function drawConnector(source, target, svg, container) {
            const sBox = source.getBoundingClientRect();
            const tBox = target.getBoundingClientRect();
            const containerBox = container.getBoundingClientRect();

            const sx = (sBox.left - containerBox.left) + sBox.width;
            const sy = (sBox.top - containerBox.top) + sBox.height / 2;
            const tx = (tBox.left - containerBox.left);
            const ty = (tBox.top - containerBox.top) + tBox.height / 2;

            const dx = Math.max(30, (tx - sx) * 0.4);
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            const d = `M ${sx} ${sy} C ${sx + dx} ${sy} ${tx - dx} ${ty} ${tx} ${ty}`;
            path.setAttribute('d', d);
            path.setAttribute('stroke', 'rgba(43,217,151,0.9)');
            path.setAttribute('fill', 'none');
            path.setAttribute('stroke-width', '3');
            path.setAttribute('stroke-linecap', 'round');
            svg.appendChild(path);
        }

        // Mettre à jour un score
        function updateScore(idMatch) {
            const score1 = document.getElementById('score1_' + idMatch).value;
            const score2 = document.getElementById('score2_' + idMatch).value;

            if (!score1 || !score2) {
                alert('Veuillez entrer les deux scores');
                return;
            }

            const score = score1 + '-' + score2;

            fetch('../../controllers/TournoiController.php?action=update_score', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'idMatch=' + idMatch + '&score=' + encodeURIComponent(score)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour du score');
            });
        }

        // Générer le bracket
        function generateBracket() {
            if (!confirm('Générer le bracket pour ce tournoi ?')) {
                return;
            }

            fetch('../../controllers/TournoiController.php?action=generate_bracket', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'idTournoi=<?php echo $idTournoi; ?>&force=true'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la génération du bracket');
            });
        }

        // Initialiser le layout
        window.addEventListener('load', function() {
            layoutBracket();
        });

        window.addEventListener('resize', function() {
            if (window._bracketResize) clearTimeout(window._bracketResize);
            window._bracketResize = setTimeout(layoutBracket, 100);
        });
    </script>
</body>
</html>
