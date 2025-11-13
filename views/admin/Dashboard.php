
<?php
/** @var array $users */
/** @var array $terrains */
/** @var array $prices */
/** @var array|null $feedback */
/** @var string $currentSection */
$users = $users ?? [];
$terrains = $terrains ?? [];
$prices = $prices ?? [];
$feedback = $feedback ?? null;

$currentSection = $currentSection ?? 'users';
$roleOptions = ['client' => 'Client', 'admin' => 'Administrateur'];
$etatOptions = ['actif' => 'Actif', 'inactif' => 'Inactif', 'suspendu' => 'Suspendu'];
$tailleOptions = ['mini' => 'Mini', 'moyen' => 'Moyen', 'grand' => 'Grand'];
$typeOptions = [
    'gazon_naturel' => 'Gazon naturel',
    'gazon_artificiel' => 'Gazon artificiel',
    'dur' => 'Terrain dur',
];
$priceCategories = [
    'terrain' => 'Tarif terrain',
    'service' => 'Service additionnel',
];
$adminTabs = [
    'users' => ['label' => 'Utilisateurs'],
    'terrains' => ['label' => 'Terrains'],
    'prices' => ['label' => 'Tarifs'],
];

$totalUsers = count($users);
$adminUsers = 0;
$activeUsers = 0;
$etatBreakdown = ['actif' => 0, 'inactif' => 0, 'suspendu' => 0, 'autre' => 0];
foreach ($users as $user) {
    if (($user['role'] ?? '') === 'admin') {
        $adminUsers++;
    }
    $etatKey = $user['etat'] ?? 'autre';
    if (!array_key_exists($etatKey, $etatBreakdown)) {
        $etatBreakdown['autre']++;
    } else {
        $etatBreakdown[$etatKey]++;
    }
    if ($etatKey === 'actif') {
        $activeUsers++;
    }
}
if (($etatBreakdown['autre'] ?? 0) === 0) {
    unset($etatBreakdown['autre']);
}
$recentUsers = array_slice($users, 0, 5);

$terrainCount = count($terrains);
$availableTerrains = 0;
$terrainByType = [];
$terrainBySize = [];
foreach ($terrains as $terrain) {
    if ((int) ($terrain['disponible'] ?? 0) === 1) {
        $availableTerrains++;
    }
    $typeKey = $terrain['type'] ?? 'non_defini';
    $sizeKey = $terrain['taille'] ?? 'non_defini';
    $terrainByType[$typeKey] = ($terrainByType[$typeKey] ?? 0) + 1;
    $terrainBySize[$sizeKey] = ($terrainBySize[$sizeKey] ?? 0) + 1;
}
arsort($terrainByType);
arsort($terrainBySize);
$topTerrainTypes = array_slice($terrainByType, 0, 4, true);
$topTerrainSizes = array_slice($terrainBySize, 0, 3, true);

$priceCount = count($prices);
$totalPricingValue = 0.0;
foreach ($prices as $priceRow) {
    $totalPricingValue += (float) ($priceRow['prix'] ?? 0);
}
$averagePrice = $priceCount > 0 ? $totalPricingValue / $priceCount : 0.0;
$priceHighlights = $prices;
usort($priceHighlights, static function ($a, $b) {
    return ($b['prix'] ?? 0) <=> ($a['prix'] ?? 0);
});
$priceHighlights = array_slice($priceHighlights, 0, 3);
$priceTablePreview = array_slice($prices, 0, 6);
$tabMetrics = [
    'users' => $totalUsers,
    'terrains' => $terrainCount,
    'prices' => $priceCount,
];

$availabilityRate = $terrainCount > 0 ? (int) round(($availableTerrains / max($terrainCount, 1)) * 100) : 0;
$roleBreakdown = [];
foreach ($users as $user) {
    $roleKey = $user['role'] ?? 'autre';
    $roleBreakdown[$roleKey] = ($roleBreakdown[$roleKey] ?? 0) + 1;
}
arsort($roleBreakdown);

$dominantTerrainLabel = 'Non défini';
if (!empty($topTerrainTypes)) {
    foreach ($topTerrainTypes as $typeKey => $count) {
        $dominantTerrainLabel = $typeOptions[$typeKey] ?? ucfirst((string) $typeKey);
        break;
    }
}

$priceValues = [];
foreach ($prices as $priceRow) {
    $priceValues[] = (float) ($priceRow['prix'] ?? 0);
}
$priceMin = !empty($priceValues) ? min($priceValues) : 0.0;
$priceMax = !empty($priceValues) ? max($priceValues) : 0.0;
$priceRangeLabel = $priceCount > 0
    ? number_format($priceMin, 2, ',', ' ') . ' - ' . number_format($priceMax, 2, ',', ' ') . ' Dhs'
    : 'Non défini';

$sectionMeta = [
    'users' => [
        'title' => 'Utilisateurs & gouvernance',
        'description' => 'Créez, ajustez et pilotez les accès en toute confiance.',
        'action' => 'Ajouter un utilisateur',
        'anchor' => '#users-create',
    ],
    'terrains' => [
        'title' => 'Terrains & inventaire',
        'description' => 'Gardez vos surfaces à jour et exploitables.',
        'action' => 'Ajouter un terrain',
        'anchor' => '#terrains-create',
    ],
    'prices' => [
        'title' => 'Tarifs & services',
        'description' => 'Structurez vos offres et harmonisez la grille.',
        'action' => 'Ajouter un tarif',
        'anchor' => '#prices-create',
    ],
];
$activeSectionMeta = $sectionMeta[$currentSection] ?? reset($sectionMeta);

$activityFeed = [];
foreach (array_slice($users, 0, 2) as $user) {
    $activityFeed[] = [
        'sort' => (int) ($user['id'] ?? 0),
        'icon' => 'bi-person-gear',
        'title' => trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')),
        'subtitle' => $roleOptions[$user['role'] ?? ''] ?? 'Utilisateur',
        'value' => $user['email'] ?? 'Non renseigné',
    ];
}
foreach (array_slice($terrains, 0, 2) as $terrain) {
    $typeKey = $terrain['type'] ?? '';
    $sizeKey = $terrain['taille'] ?? '';
    $activityFeed[] = [
        'sort' => (int) ($terrain['id'] ?? 0),
        'icon' => 'bi-geo-alt',
        'title' => $terrain['nom'] ?? 'Terrain #' . (int) ($terrain['id'] ?? 0),
        'subtitle' => trim(($tailleOptions[$sizeKey] ?? $sizeKey) . ' • ' . ($typeOptions[$typeKey] ?? $typeKey)),
        'value' => (int) ($terrain['disponible'] ?? 0) === 1 ? 'Disponible' : 'Hors ligne',
    ];
}
foreach (array_slice($prices, 0, 2) as $priceRow) {
    $activityFeed[] = [
        'sort' => (int) (($priceRow['prix'] ?? 0) * 100),
        'icon' => 'bi-cash-stack',
        'title' => $priceRow['categorie'] ?? 'Tarif',
        'subtitle' => $priceRow['reference'] ?? 'Grille',
        'value' => number_format((float) ($priceRow['prix'] ?? 0), 2, ',', ' ') . ' Dhs',
    ];
}
usort($activityFeed, static function (array $a, array $b) {
    return ($b['sort'] ?? 0) <=> ($a['sort'] ?? 0);
});
$activityFeed = array_slice($activityFeed, 0, 6);

$todayLabel = date('d/m/Y');
?>
<div class="ft-admin ft-admin--revamp">

    <section class="ft-admin__prologue">
        <article class="ft-card ft-hero-card">
            <div class="ft-hero-card__body">
                <p class="ft-badge ft-badge--ghost">Mise à jour <?php echo htmlspecialchars($todayLabel, ENT_QUOTES, 'UTF-8'); ?></p>
                <h1>Command Center Foot Fields</h1>
                <p>Surveillez l’activité critique et orientez les décisions clés depuis une console claire et cohérente avec l’identité FootTime.</p>
                <div class="ft-hero-card__actions">
                    <a class="ft-btn ft-btn-primary" href="<?php echo htmlspecialchars($activeSectionMeta['anchor'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($activeSectionMeta['action'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <button type="button" class="ft-btn ft-btn-secondary" onclick="window.location.reload();">Actualiser</button>
                </div>
            </div>
            <div class="ft-hero-card__metrics">
                <div class="ft-hero-metric">
                    <span>Utilisateurs actifs</span>
                    <strong><?php echo $activeUsers; ?></strong>
                    <small><?php echo $totalUsers; ?> comptes suivis</small>
                </div>
                <div class="ft-hero-metric">
                    <span>Inventaire disponible</span>
                    <strong><?php echo $availabilityRate; ?>%</strong>
                    <small><?php echo $availableTerrains; ?> / <?php echo $terrainCount; ?> terrains</small>
                </div>
                <div class="ft-hero-metric">
                    <span>Ticket moyen</span>
                    <strong><?php echo number_format($averagePrice, 2, ',', ' '); ?> Dhs</strong>
                    <small>Plage <?php echo htmlspecialchars($priceRangeLabel, ENT_QUOTES, 'UTF-8'); ?></small>
                </div>
            </div>
        </article>
        <article class="ft-card ft-hero-feed">
            <header>
                <p class="ft-label">Activité récente</p>
                <h3>Pulse board</h3>
            </header>
            <?php if (empty($activityFeed)): ?>
                <p class="ft-empty">Aucune activité à afficher.</p>
            <?php else: ?>
                <ul class="ft-timeline">
                    <?php foreach ($activityFeed as $activity): ?>
                        <li>
                            <span class="ft-timeline__icon bi <?php echo htmlspecialchars($activity['icon'], ENT_QUOTES, 'UTF-8'); ?>"></span>
                            <div>
                                <strong><?php echo htmlspecialchars($activity['title'] ?: 'Non renseigné', ENT_QUOTES, 'UTF-8'); ?></strong>
                                <p><?php echo htmlspecialchars($activity['subtitle'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <span class="ft-pill"><?php echo htmlspecialchars($activity['value'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
    </section>

    <section class="ft-admin__overview">
        <div class="ft-metrics-grid">
            <article class="ft-metric-card">
                <div class="ft-metric-card__head">
                    <span class="ft-pill">Utilisateurs</span>
                    <span class="ft-trend <?php echo $activeUsers >= max(1, $totalUsers / 2) ? 'is-up' : 'is-down'; ?>"><?php echo $activeUsers; ?> actifs</span>
                </div>
                <strong class="ft-metric-card__value"><?php echo $totalUsers; ?></strong>
                <p><?php echo $adminUsers; ?> administrateurs • <?php echo $roleBreakdown['client'] ?? 0; ?> clients</p>
            </article>
            <article class="ft-metric-card">
                <div class="ft-metric-card__head">
                    <span class="ft-pill">Terrains</span>
                    <span class="ft-trend is-neutral"><?php echo htmlspecialchars($dominantTerrainLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <strong class="ft-metric-card__value"><?php echo $terrainCount; ?></strong>
                <p><?php echo $availabilityRate; ?>% disponibles</p>
            </article>
            <article class="ft-metric-card">
                <div class="ft-metric-card__head">
                    <span class="ft-pill">Tarifs</span>
                    <span class="ft-trend is-up"><?php echo number_format($averagePrice, 0, ',', ' '); ?> Dhs</span>
                </div>
                <strong class="ft-metric-card__value"><?php echo $priceCount; ?></strong>
                <p>Catalogue harmonisé</p>
            </article>
            <article class="ft-metric-card">
                <div class="ft-metric-card__head">
                    <span class="ft-pill">Valeur catalogue</span>
                    <span class="ft-trend is-neutral"><?php echo htmlspecialchars($priceRangeLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <strong class="ft-metric-card__value"><?php echo number_format($totalPricingValue, 0, ',', ' '); ?> Dhs</strong>
                <p>Top 3 :
                    <?php if (empty($priceHighlights)): ?>
                        n/a
                    <?php else: ?>
                        <?php echo htmlspecialchars(implode(', ', array_map(static function ($priceRow) {
                            return (string) ($priceRow['categorie'] ?? 'Tarif');
                        }, $priceHighlights)), ENT_QUOTES, 'UTF-8'); ?>
                    <?php endif; ?>
                </p>
            </article>
        </div>

        <div class="ft-panels-grid">
            <article class="ft-panel ft-panel--progress">
                <header>
                    <p class="ft-label">Santé des comptes</p>
                    <h3>Etat des utilisateurs</h3>
                </header>
                <?php if (empty($etatBreakdown)): ?>
                    <p class="ft-empty">Aucune donnée utilisateur.</p>
                <?php else: ?>
                    <ul class="ft-progress-list">
                        <?php foreach ($etatBreakdown as $etat => $count): ?>
                            <?php $percentage = $totalUsers > 0 ? round(($count / $totalUsers) * 100) : 0; ?>
                            <li>
                                <div>
                                    <span><?php echo htmlspecialchars($etatOptions[$etat] ?? ucfirst((string) $etat), ENT_QUOTES, 'UTF-8'); ?></span>
                                    <strong><?php echo $count; ?></strong>
                                </div>
                                <div class="ft-progress">
                                    <span style="width: <?php echo $percentage; ?>%"></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
            <article class="ft-panel">
                <header>
                    <p class="ft-label">Inventaire</p>
                    <h3>Typologie des terrains</h3>
                    <small>Leader : <?php echo htmlspecialchars($dominantTerrainLabel, ENT_QUOTES, 'UTF-8'); ?></small>
                </header>
                <?php if (empty($topTerrainTypes) && empty($topTerrainSizes)): ?>
                    <p class="ft-empty">Aucun terrain référencé.</p>
                <?php else: ?>
                    <div class="ft-dual-list">
                        <?php if (!empty($topTerrainTypes)): ?>
                            <ul>
                                <li class="ft-label">Par type</li>
                                <?php foreach ($topTerrainTypes as $type => $count): ?>
                                    <li>
                                        <span><?php echo htmlspecialchars($typeOptions[$type] ?? ucfirst((string) $type), ENT_QUOTES, 'UTF-8'); ?></span>
                                        <strong><?php echo $count; ?></strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <?php if (!empty($topTerrainSizes)): ?>
                            <ul>
                                <li class="ft-label">Par taille</li>
                                <?php foreach ($topTerrainSizes as $size => $count): ?>
                                    <li>
                                        <span><?php echo htmlspecialchars($tailleOptions[$size] ?? ucfirst((string) $size), ENT_QUOTES, 'UTF-8'); ?></span>
                                        <strong><?php echo $count; ?></strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </article>
            <article class="ft-panel">
                <header>
                    <p class="ft-label">Performance tarifaire</p>
                    <h3>Synthèse</h3>
                </header>
                <?php if (empty($priceHighlights)): ?>
                    <p class="ft-empty">Aucun tarif enregistré.</p>
                <?php else: ?>
                    <ul class="ft-insight-list">
                        <?php foreach ($priceHighlights as $priceRow): ?>
                            <li>
                                <span><?php echo htmlspecialchars($priceRow['categorie'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                                <strong><?php echo number_format((float) ($priceRow['prix'] ?? 0), 2, ',', ' '); ?> Dhs</strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
        </div>
    </section>
    <?php if ($feedback): ?>
        <div class="ft-alert <?php echo ($feedback['success'] ?? false) ? 'ft-alert-success' : 'ft-alert-error'; ?>">
            <?php echo htmlspecialchars((string) ($feedback['message'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <section class="ft-admin__section">
        <?php if ($currentSection === 'users'): ?>
            <div class="ft-workspace">
                <div class="ft-workspace__column">
                    <article class="ft-panel ft-panel--form" id="users-create">
                        <header>
                            <h3>Nouvel utilisateur</h3>
                            <p>Inscrivez un administrateur ou un client en quelques clics.</p>
                        </header>
                        <form method="post" class="ft-stack">
                            <input type="hidden" name="action" value="create_user">
                            <input type="hidden" name="section" value="users">
                            <div class="ft-form-row">
                                <label>Nom
                                    <input type="text" name="nom" required>
                                </label>
                                <label>Prénom
                                    <input type="text" name="prenom" required>
                                </label>
                                <label>Email
                                    <input type="email" name="email" required>
                                </label>
                                <label>Téléphone
                                    <input type="tel" name="telephone" required>
                                </label>
                            </div>
                            <div class="ft-form-row">
                                <label>Adresse
                                    <input type="text" name="adresse" placeholder="Quartier, ville...">
                                </label>
                                <label>Rôle
                                    <select name="role" required>
                                        <?php foreach ($roleOptions as $value => $label): ?>
                                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label>Etat
                                    <select name="etat" required>
                                        <?php foreach ($etatOptions as $value => $label): ?>
                                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label>Mot de passe
                                    <input type="password" name="password" required>
                                </label>
                            </div>
                            <div class="ft-row-actions">
                                <button type="submit" class="ft-btn ft-btn-primary">Créer le compte</button>
                            </div>
                        </form>
                    </article>
                    <?php if (!empty($recentUsers)): ?>
                        <article class="ft-panel ft-panel--table">
                            <header>
                                <h3>Dernières inscriptions</h3>
                                <p>Vue condensée des 5 derniers profils.</p>
                            </header>
                            <div class="ft-table-wrapper">
                                <table class="ft-data-table ft-data-table--compact">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom complet</th>
                                            <th>Rôle</th>
                                            <th>Etat</th>
                                            <th>Téléphone</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentUsers as $user): ?>
                                            <tr>
                                                <td>#<?php echo (int) ($user['id'] ?? 0); ?></td>
                                                <td><?php echo htmlspecialchars(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($roleOptions[$user['role'] ?? ''] ?? ($user['role'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($etatOptions[$user['etat'] ?? ''] ?? ($user['etat'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($user['telephone'] ?? 'Non renseigné', ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>
                <div class="ft-workspace__column ft-workspace__column--wide">
                    <article class="ft-panel ft-panel--list">
                        <header>
                            <div>
                                <h3>Comptes existants</h3>
                                <p><?php echo $totalUsers; ?> comptes suivis • <?php echo $adminUsers; ?> administrateurs</p>
                            </div>
                            <button type="button" class="ft-btn ft-btn-secondary" onclick="window.location.reload();">Actualiser</button>
                        </header>
                        <div class="ft-records">
                            <?php if (empty($users)): ?>
                                <p class="ft-empty">Aucun utilisateur disponible.</p>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <?php
                                    $userId = (int) ($user['id'] ?? 0);
                                    $roleKey = $user['role'] ?? '';
                                    $roleLabel = $roleOptions[$roleKey] ?? $roleKey;
                                    $etatKey = $user['etat'] ?? '';
                                    $etatLabel = $etatOptions[$etatKey] ?? $etatKey;
                                    ?>
                                    <article class="ft-record" data-role="<?php echo htmlspecialchars($roleKey, ENT_QUOTES, 'UTF-8'); ?>" data-etat="<?php echo htmlspecialchars($etatKey, ENT_QUOTES, 'UTF-8'); ?>">
                                        <div class="ft-record__head">
                                            <div>
                                                <p class="ft-label">#<?php echo $userId; ?></p>
                                                <h4><?php echo htmlspecialchars(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></h4>
                                            </div>
                                            <div class="ft-record__tags">
                                                <span class="ft-chip"><?php echo htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                                <span class="ft-chip ft-chip--state-<?php echo htmlspecialchars($etatKey, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($etatLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                        </div>
                                        <ul class="ft-record__meta">
                                            <li><i class="bi bi-envelope"></i><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
                                            <li><i class="bi bi-telephone"></i><?php echo htmlspecialchars($user['telephone'] ?? 'Non renseigné', ENT_QUOTES, 'UTF-8'); ?></li>
                                        </ul>
                                        <form method="post" class="ft-stack">
                                            <input type="hidden" name="action" value="update_user">
                                            <input type="hidden" name="section" value="users">
                                            <input type="hidden" name="id" value="<?php echo $userId; ?>">
                                            <div class="ft-form-row">
                                                <label>Nom
                                                    <input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </label>
                                                <label>Prénom
                                                    <input type="text" name="prenom" value="<?php echo htmlspecialchars($user['prenom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </label>
                                                <label>Email
                                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </label>
                                                <label>Téléphone
                                                    <input type="tel" name="telephone" value="<?php echo htmlspecialchars($user['telephone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </label>
                                            </div>
                                            <div class="ft-form-row">
                                                <label>Adresse
                                                    <input type="text" name="adresse" value="<?php echo htmlspecialchars($user['adresse'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </label>
                                                <label>Rôle
                                                    <select name="role" required>
                                                        <?php foreach ($roleOptions as $value => $label): ?>
                                                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $roleKey === $value ? ' selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </label>
                                                <label>Etat
                                                    <select name="etat" required>
                                                        <?php foreach ($etatOptions as $value => $label): ?>
                                                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $etatKey === $value ? ' selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </label>
                                                <label>Mot de passe (optionnel)
                                                    <input type="password" name="password" placeholder="Laisser vide pour conserver">
                                                </label>
                                            </div>
                                            <div class="ft-record__actions">
                                                <button type="submit" class="ft-btn ft-btn-secondary">Sauvegarder</button>
                                            </div>
                                        </form>
                                        <form method="post" class="ft-inline-form" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="section" value="users">
                                            <input type="hidden" name="id" value="<?php echo $userId; ?>">
                                            <button type="submit" class="ft-btn ft-btn-danger">Supprimer</button>
                                        </form>
                                    </article>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            </div>
        <?php elseif ($currentSection === 'terrains'): ?>
            <div class="ft-workspace">
                <div class="ft-workspace__column">
                    <article class="ft-panel ft-panel--form" id="terrains-create">
                        <header>
                            <h3>Ajouter un terrain</h3>
                            <p>Ajoutez une surface au catalogue et rendez-la réservable.</p>
                        </header>
                        <form method="post" enctype="multipart/form-data" class="ft-stack">
                            <input type="hidden" name="action" value="create_terrain">
                            <input type="hidden" name="section" value="terrains">
                            <div class="ft-form-row">
                                <label>Nom
                                    <input type="text" name="nom" required>
                                </label>
                                <label>Taille
                                    <select name="taille" required>
                                        <?php foreach ($tailleOptions as $value => $label): ?>
                                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label>Type
                                    <select name="type" required>
                                        <?php foreach ($typeOptions as $value => $label): ?>
                                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label>Prix (Dhs)
                                    <input type="number" name="prix" min="0" step="0.01" placeholder="0.00">
                                </label>
                            </div>
                            <div class="ft-form-row">
                                <label class="ft-switch">
                                    <input type="checkbox" name="disponible" value="1" checked>
                                    Disponible à la réservation
                                </label>
                                <label>Photo (JPG, PNG ou WEBP)
                                    <input type="file" name="photo" accept="image/*">
                                </label>
                            </div>
                            <div class="ft-row-actions">
                                <button type="submit" class="ft-btn ft-btn-primary">Ajouter le terrain</button>
                            </div>
                        </form>
                    </article>
                    <article class="ft-panel">
                        <header>
                            <h3>Top typologies</h3>
                            <p>Formats les plus demandés.</p>
                        </header>
                        <?php if (empty($topTerrainTypes) && empty($topTerrainSizes)): ?>
                            <p class="ft-empty">Aucune donnée terrain.</p>
                        <?php else: ?>
                            <div class="ft-dual-list">
                                <?php if (!empty($topTerrainTypes)): ?>
                                    <ul>
                                        <li class="ft-label">Par type</li>
                                        <?php foreach ($topTerrainTypes as $type => $count): ?>
                                            <li>
                                                <span><?php echo htmlspecialchars($typeOptions[$type] ?? ucfirst((string) $type), ENT_QUOTES, 'UTF-8'); ?></span>
                                                <strong><?php echo $count; ?></strong>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <?php if (!empty($topTerrainSizes)): ?>
                                    <ul>
                                        <li class="ft-label">Par taille</li>
                                        <?php foreach ($topTerrainSizes as $size => $count): ?>
                                            <li>
                                                <span><?php echo htmlspecialchars($tailleOptions[$size] ?? ucfirst((string) $size), ENT_QUOTES, 'UTF-8'); ?></span>
                                                <strong><?php echo $count; ?></strong>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                </div>
                <div class="ft-workspace__column ft-workspace__column--wide">
                    <article class="ft-panel ft-panel--list">
                        <header>
                            <div>
                                <h3>Inventaire détaillé</h3>
                                <p><?php echo $terrainCount; ?> terrains • <?php echo $availableTerrains; ?> disponibles</p>
                            </div>
                        </header>
                        <div class="ft-records">
                            <?php if (empty($terrains)): ?>
                                <p class="ft-empty">Aucun terrain configuré.</p>
                            <?php else: ?>
                                <?php foreach ($terrains as $terrain): ?>
                                    <?php
                                    $terrainId = (int) ($terrain['id'] ?? 0);
                                    $photoRelative = $terrain['photo'] ?? null;
                                    $photoSrc = $photoRelative ? '../../' . str_replace('\\', '/', ltrim($photoRelative, '/\\')) : null;
                                    $lastUpdate = $terrain['date_modification'] ?? null;
                                    $sizeKey = $terrain['taille'] ?? '';
                                    $sizeLabel = $tailleOptions[$sizeKey] ?? $sizeKey;
                                    $typeKey = $terrain['type'] ?? '';
                                    $typeLabel = $typeOptions[$typeKey] ?? $typeKey;
                                    $isAvailable = (int) ($terrain['disponible'] ?? 0) === 1;
                                    ?>
                                    <article class="ft-record ft-record--terrain">
                                        <div class="ft-record__head">
                                            <div>
                                                <p class="ft-label">#<?php echo $terrainId; ?></p>
                                                <h4><?php echo htmlspecialchars($terrain['nom'] ?? 'Terrain #' . $terrainId, ENT_QUOTES, 'UTF-8'); ?></h4>
                                            </div>
                                            <div class="ft-record__tags">
                                                <span class="ft-chip"><?php echo htmlspecialchars($sizeLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                                <span class="ft-chip"><?php echo htmlspecialchars($typeLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                                <?php if (isset($terrain['prix']) && $terrain['prix'] !== null && $terrain['prix'] !== ''): ?>
                                                    <span class="ft-chip" style="border-color:rgba(43,217,151,.6);background:rgba(43,217,151,.15);"><?php echo number_format((float) $terrain['prix'], 2, ',', ' '); ?> Dhs</span>
                                                <?php endif; ?>
                                                <span class="ft-chip <?php echo $isAvailable ? 'ft-chip--success' : 'ft-chip--danger'; ?>"><?php echo $isAvailable ? 'Disponible' : 'Hors ligne'; ?></span>
                                            </div>
                                        </div>
                                        <?php if ($lastUpdate): ?>
                                            <p class="ft-record__note">MAJ <?php echo htmlspecialchars(substr((string) $lastUpdate, 0, 10), ENT_QUOTES, 'UTF-8'); ?></p>
                                        <?php endif; ?>
                                        <form method="post" enctype="multipart/form-data" class="ft-stack">
                                            <input type="hidden" name="action" value="update_terrain">
                                            <input type="hidden" name="section" value="terrains">
                                            <input type="hidden" name="id" value="<?php echo $terrainId; ?>">
                                            <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($photoRelative ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            <div class="ft-form-row">
                                                <label>Nom
                                                    <input type="text" name="nom" value="<?php echo htmlspecialchars($terrain['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </label>
                                                <label>Taille
                                                    <select name="taille" required>
                                                        <?php foreach ($tailleOptions as $value => $label): ?>
                                                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $sizeKey === $value ? ' selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </label>
                                                <label>Type
                                                    <select name="type" required>
                                                        <?php foreach ($typeOptions as $value => $label): ?>
                                                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $typeKey === $value ? ' selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </label>
                                                <label>Prix (Dhs)
                                                    <input type="number" name="prix" min="0" step="0.01" value="<?php echo htmlspecialchars((string) ($terrain['prix'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="0.00">
                                                </label>
                                            </div>
                                            <div class="ft-form-row">
                                                <label class="ft-switch">
                                                    <input type="checkbox" name="disponible" value="1"<?php echo $isAvailable ? ' checked' : ''; ?>>
                                                    Disponible
                                                </label>
                                                <label>Nouveau visuel
                                                    <input type="file" name="photo" accept="image/*">
                                                </label>
                                            </div>
                                            <?php if ($photoSrc): ?>
                                                <div class="ft-upload__preview">
                                                    <img src="<?php echo htmlspecialchars($photoSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="Photo terrain">
                                                </div>
                                            <?php endif; ?>
                                            <div class="ft-record__actions">
                                                <button type="submit" class="ft-btn ft-btn-secondary">Mettre à jour</button>
                                            </div>
                                        </form>
                                        <form method="post" class="ft-inline-form" onsubmit="return confirm('Supprimer ce terrain ?');">
                                            <input type="hidden" name="action" value="delete_terrain">
                                            <input type="hidden" name="section" value="terrains">
                                            <input type="hidden" name="id" value="<?php echo $terrainId; ?>">
                                            <input type="hidden" name="photo" value="<?php echo htmlspecialchars($photoRelative ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            <button type="submit" class="ft-btn ft-btn-danger">Supprimer</button>
                                        </form>
                                    </article>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            </div>
        <?php else: ?>
            <div class="ft-workspace">
                <div class="ft-workspace__column">
                    <article class="ft-panel ft-panel--form" id="prices-create">
                        <header>
                            <h3>Ajouter un tarif</h3>
                            <p>Structurez vos offres et services.</p>
                        </header>
                        <form method="post" class="ft-stack">
                            <input type="hidden" name="action" value="create_price">
                            <input type="hidden" name="section" value="prices">
                            <div class="ft-form-row">
                                <label>Catégorie
                                    <select name="categorie" required>
                                        <?php foreach ($priceCategories as $value => $label): ?>
                                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label>Référence
                                    <input type="text" name="reference" placeholder="Option ou format">
                                </label>
                                <label>Prix (Dhs)
                                    <input type="number" name="prix" min="0" step="0.01" required>
                                </label>
                            </div>
                            <div class="ft-form-row">
                                <label>Description
                                    <textarea name="description" placeholder="Notes complémentaires"></textarea>
                                </label>
                            </div>
                            <div class="ft-row-actions">
                                <button type="submit" class="ft-btn ft-btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </article>
                    <?php if (!empty($priceTablePreview)): ?>
                        <article class="ft-panel ft-panel--table">
                            <header>
                                <h3>Bilan rapide</h3>
                                <p>Aperçu de la grille.</p>
                            </header>
                            <div class="ft-table-wrapper">
                                <table class="ft-data-table ft-data-table--compact">
                                    <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Référence</th>
                                            <th>Tarif</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($priceTablePreview as $priceRow): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($priceRow['categorie'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($priceRow['reference'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo number_format((float) ($priceRow['prix'] ?? 0), 2, ',', ' '); ?> Dhs</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>
                <div class="ft-workspace__column ft-workspace__column--wide">
                    <article class="ft-panel ft-panel--list">
                        <header>
                            <div>
                                <h3>Catalogue complet</h3>
                                <p><?php echo $priceCount; ?> lignes tarifaires • <?php echo number_format($totalPricingValue, 0, ',', ' '); ?> Dhs</p>
                            </div>
                        </header>
                        <div class="ft-records">
                            <?php if (empty($prices)): ?>
                                <p class="ft-empty">Aucune tarification disponible.</p>
                            <?php else: ?>
                                <?php foreach ($prices as $priceRow): ?>
                                    <?php $categorie = $priceRow['categorie'] ?? ''; ?>
                                    <article class="ft-record">
                                        <div class="ft-record__head">
                                            <div>
                                                <p class="ft-label"><?php echo htmlspecialchars($priceCategories[$categorie] ?? 'Tarif', ENT_QUOTES, 'UTF-8'); ?></p>
                                                <h4><?php echo htmlspecialchars($priceRow['reference'] ?? 'Option', ENT_QUOTES, 'UTF-8'); ?></h4>
                                            </div>
                                            <div class="ft-record__tags">
                                                <span class="ft-chip"><?php echo number_format((float) ($priceRow['prix'] ?? 0), 2, ',', ' '); ?> Dhs</span>
                                            </div>
                                        </div>
                                        <p class="ft-record__note"><?php echo htmlspecialchars($priceRow['description'] ?? 'Pas de description', ENT_QUOTES, 'UTF-8'); ?></p>
                                        <form method="post" class="ft-stack">
                                            <input type="hidden" name="action" value="update_price">
                                            <input type="hidden" name="section" value="prices">
                                            <input type="hidden" name="categorie_original" value="<?php echo htmlspecialchars($categorie, ENT_QUOTES, 'UTF-8'); ?>">
                                            <div class="ft-form-row">
                                                <label>Catégorie
                                                    <select name="categorie" required>
                                                        <?php foreach ($priceCategories as $value => $label): ?>
                                                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $categorie === $value ? ' selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </label>
                                                <label>Référence
                                                    <input type="text" name="reference" value="<?php echo htmlspecialchars($priceRow['reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                </label>
                                                <label>Prix (Dhs)
                                                    <input type="number" name="prix" min="0" step="0.01" value="<?php echo htmlspecialchars((string) ($priceRow['prix'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </label>
                                            </div>
                                            <div class="ft-form-row">
                                                <label>Description
                                                    <textarea name="description"><?php echo htmlspecialchars($priceRow['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                                </label>
                                            </div>
                                            <div class="ft-record__actions">
                                                <button type="submit" class="ft-btn ft-btn-secondary">Mettre à jour</button>
                                            </div>
                                        </form>
                                        <form method="post" class="ft-inline-form" onsubmit="return confirm('Supprimer cette catégorie ?');">
                                            <input type="hidden" name="action" value="delete_price">
                                            <input type="hidden" name="section" value="prices">
                                            <input type="hidden" name="categorie" value="<?php echo htmlspecialchars($categorie, ENT_QUOTES, 'UTF-8'); ?>">
                                            <button type="submit" class="ft-btn ft-btn-danger">Supprimer</button>
                                        </form>
                                    </article>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>
