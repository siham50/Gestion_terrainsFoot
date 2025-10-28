# ⚽ Gestion des Réservations de Terrains de Foot

## 🎓 Mini Projet 2 — École Nationale des Sciences Appliquées de Tétouan
> TP4 — Application Web PHP Orientée Objet  
> Auteurs : _Hariss Houssam_, _El Fadil Assel_, _El Maaroufi Siham_, _El Ouazzani Touhami Aymane_, _Sadiki Abderrahim_  
> Encadrant : _[Al Achhab Mohammed]_  
> Année universitaire : 2025

---

## 🧩 Objectif du projet

Développer une application web permettant la **gestion complète des réservations de terrains de football**, incluant :

- La **réservation en ligne** par les utilisateurs (date, créneau horaire, taille/type de terrain, services optionnels)
- La **gestion administrative** (terrains, prix, factures, tournois) via une **interface administrateur**
- La **génération automatique des factures**
- L’**implémentation AJAX**
- L’**envoi automatique d’e-mails de confirmation**

---

## 🏗️ Architecture du projet (MVC)

```
/reservation_terrains/
│
├── index.php                           # Page d’accueil / redirection vers réservation
│
├── config/
│   ├── database.php                    # Connexion PDO à la base de données
│   └── config.php                      # Constantes globales (ex : EMAIL_ADMIN, BASE_URL)
│
├── classes/                            # Modèles (logique métier)
│   ├── Database.php                    # Gestion de la connexion PDO
│   ├── Terrain.php                     # Modèle de terrain
│   ├── Reservation.php                 # Modèle de réservation
│   ├── Utilisateur.php                 # Modèle utilisateur
│   ├── Facture.php                     # Modèle facture
│   ├── Prix.php                        # Modèle des tarifs terrains/services
│   ├── Tournoi.php                     # Modèle tournoi
│   ├── Equipe.php                      # Modèle d’équipe (tournoi)
│   └── Newsletter.php                  # Modèle d’abonné à la newsletter
│
├── controllers/                        # Logique applicative
│   ├── TerrainController.php           # Gère les terrains (affichage dispo, CRUD admin)
│   ├── ReservationController.php       # Gère les réservations (création, modif, facture)
│   ├── UtilisateurController.php       # Gère connexion / inscription
│   ├── AdminController.php             # Dashboard et actions d’administration
│   ├── TournoiController.php           # Gestion complète des tournois
│   └── NewsletterController.php        # Abonnement, désabonnement, envoi des mails
│
├── views/
│   ├── public/
│   │   ├── home.php                    # Page d’accueil
│   │   ├── reservation_form.php        # Formulaire de réservation
│   │   ├── confirmation.php            # Confirmation de réservation
│   │   ├── disponibilite.php           # Disponibilités AJAX
│   │   ├── tournois.php                # Liste / inscription tournois
│   │   └── newsletter.php              # Formulaire d’abonnement newsletter
│   │
│   └── admin/
│       ├── dashboard.php               # Tableau de bord administrateur
│       ├── ajouter_terrain.php
│       ├── modifier_prix.php
│       ├── liste_reservations.php
│       ├── factures.php
│       ├── gestion_tournois.php        # CRUD des tournois
│       ├── gestion_equipes.php         # Gérer les équipes des tournois
│       └── newsletter_admin.php        # Gestion des abonnés + envoi d’email groupé
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   ├── js/
│   │   ├── main.js
│   │   ├── disponibilite.js            # Requêtes AJAX pour dispo terrains
│   │   └── newsletter.js               # Abonnement AJAX
│   └── images/
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── navbar.php
│
├── utils/
│   ├── functions.php                   # Fonctions génériques
│   ├── mailer.php                      # Envoi d’e-mails (confirmation, newsletter)
│   └── pdf_generator.php               # Génération des factures PDF
│
└── README.md                           # Documentation du projet
```

---

## ⚙️ Convention de code

### 1. **Nommage**

| Élément | Convention | Exemple |
|----------|-------------|----------|
| Classe | PascalCase | `ReservationController` |
| Méthode | camelCase | `verifierDisponibilite()` |
| Variable | camelCase | `$prixHeure`, `$dateReservation` |
| Constante | MAJUSCULES | `const TAUX_TVA = 0.20;` |
| Table SQL | snake_case | `reservation`, `terrain` |
| Colonne SQL | snake_case | `date_reservation`, `terrain_id` |

---

### 2. **Indentation et style**

- Indentation : **4 espaces**
- Accolades sur la **même ligne**
- Une **ligne vide entre chaque méthode**
- Commentaires **DocBlocks** pour les classes et les méthodes

---

### 3. **Organisation MVC**

- **Classes (Models)** : définissent la logique métier et interagissent avec la base de données.  
- **Contrôleurs (Controllers)** : gèrent la logique applicative, reçoivent les requêtes et envoient les données aux vues.  
- **Vues (Views)** : affichent les données à l’utilisateur via HTML/CSS/JS.

---

### 4. **Rôles et accès**

| Rôle | Accès |
|------|-------|
| **Utilisateur** | Réservation, disponibilité, modification avant 48h |
| **Administrateur** | Gestion des terrains, prix, factures, tournois, envoi d’e-mails |

---

### 5. **Fonctionnalités principales**

✅ Formulaire de réservation complet (date, créneau, type, options)  
✅ Enregistrement des données en base via PDO  
✅ Modification possible jusqu’à **48h avant le match**  
✅ Affichage **asynchrone** des disponibilités via **AJAX**  
✅ Génération automatique de **factures PDF**  
✅ Gestion des **tournois** et des **services optionnels**  
✅ Interface **administrateur sécurisée**  
✅ Envoi d’**e-mails de confirmation** aux utilisateurs  

---

## 💾 Base de données

**Nom :** `foot_fields`

**Tables principales :**
- `utilisateur`
- `terrain`
- `reservation`
- `facture`
- `prix`
- `tournoi`
- `equipe`

---

## 🔐 Sécurité & Bonnes pratiques

- 🔒 Requêtes préparées (`PDO::prepare`)
- 🧹 Nettoyage des entrées (`htmlspecialchars`, `filter_var`)
- 🔐 Authentification & gestion des sessions pour les administrateurs
- ⚡ Validation des formulaires côté **client et serveur**
- 🗃️ Respect strict du **modèle MVC**
- 🌐 Séparation des scripts JS & CSS dans le dossier `/assets/`

---

## 👨‍💻 Auteurs

Projet réalisé par :
- **Hariss Houssam**  
- **El Fadil Assel**  
- **El Maaroufi Siham**  
- **El Ouazzani Touhami Aymane**  
- **Sadiki Abderrahim**

**Filière :** Génie Informatique  
**École :** École Nationale des Sciences Appliquées – Tétouan  
📅 **Octobre 2025**

---

## 🧾 Licence

Projet académique — libre de réutilisation à des fins **pédagogiques uniquement**.
