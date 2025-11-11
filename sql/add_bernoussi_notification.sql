-- Script pour ajouter une notification pour le terrain "bernoussi" déjà ajouté
-- À exécuter une seule fois si le terrain a été ajouté avant l'implémentation des notifications

INSERT INTO `newsletter` (`type`, `titre`, `message`, `id_utilisateur`, `date_creation`, `lu`) 
VALUES (
    'nouveau_terrain',
    'Nouveau terrain disponible : bernoussi',
    'Un nouveau terrain "bernoussi" (Gazon Naturel) a été ajouté à notre complexe sportif. Réservez dès maintenant !',
    NULL,
    NOW(),
    0
);

