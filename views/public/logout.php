<?php
session_start();

$_SESSION = [];
if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]
	);
}
session_destroy();

function ft_sanitize_redirect(string $target): string {
	$target = trim($target);
	if ($target === '') {
		return 'Home.php';
	}

	// Empêcher les redirections externes
	if (preg_match('#^(https?:)?//#i', $target)) {
		return 'Home.php';
	}

	// Éviter de revenir sur la page de login / logout
	$invalidTargets = ['login.php', 'register.php', 'logout.php'];
	if (in_array(strtolower($target), $invalidTargets, true)) {
		return 'Home.php';
	}

	return $target;
}

$redirect = $_GET['redirect'] ?? '';
$redirect = ft_sanitize_redirect($redirect);

// Fallback au référent si aucune cible valide fournie
if ($redirect === 'Home.php' && !empty($_SERVER['HTTP_REFERER'])) {
	$refererPath = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
	$refererQuery = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);

	if (is_string($refererPath)) {
		if (strpos($refererPath, '/views/public/') !== false) {
			$basename = basename($refererPath);
			if ($basename && !in_array(strtolower($basename), ['login.php', 'logout.php'], true)) {
				$redirect = $basename;
				if ($refererQuery) {
					$redirect .= '?' . $refererQuery;
				}
			}
		}
	}
}

header("Location: " . $redirect);
exit;

