# Documentation Complète du Fonctionnement de la Plateforme par Rôle

Ce document détaille le fonctionnement complet de la plateforme, organisé par rôle utilisateur, basé sur les spécifications métiers (requirement.txt) et l'architecture réelle de l'application Laravel.

## Lecteur (Espace Public)

### Description et Accès
- Accès exclusif à l'**Espace Public** de la plateforme.
- Destiné aux particuliers amateurs de littérature (africaine et autres).
- Modèle économique : Accès par abonnement mensuel (ex: 7 000 FCFA), annuel (ex: 50 000 FCFA), ou par achat ponctuel d'œuvres (PDF à 3 000 FCFA, Audio à 3 500 FCFA).
- N'a **aucun accès** à l'espace éducatif ni à l'espace adulte.

### Fonctionnalités détaillées
- **Consultation et Achat** : Parcours du catalogue public, achat de livres (PDF/Audio), souscription et renouvellement d'abonnements.
- **Lecture et Écoute** : Lecteur intégré avec sauvegarde de la progression (marque-pages), lecteur audio avec contrôle de vitesse, téléchargement temporaire (mode hors ligne / PWA).
- **Interactions** : Système de notation (étoiles), publication d'avis, ajout de livres aux favoris.
- **Tableau de Bord** : Historique de lecture, temps total de lecture, recommandations de livres (potentiellement gérées par l'IA).

---

## Auteur

### Description et Accès
- Rôle créateur, peut publier des livres (formats PDF et Audio).
- Les œuvres peuvent être catégorisées comme « publique », « éducative » ou les deux.
- Les publications nécessitent la **validation d'un administrateur**.
- Modèle de rémunération : 80 % des ventes pour les œuvres originales fournies par l'auteur, 60 % pour les œuvres traduites ou produites par la plateforme.

### Fonctionnalités détaillées
- **Gestion de Catalogue** : Upload de livres (fichiers PDF et Audio).
- **Tableau de Bord Analytique** : Suivi des statistiques détaillées (nombre de lectures, écoutes).
- **Finances** : Suivi des revenus générés en temps réel, outil de suivi des versements et demande de paiements.

---

## École / Établissement Scolaire (Espace Éducatif)

### Description et Accès
- Accède à l'**Espace Éducatif** dédié, séparé du contenu grand public.
- Abonnement collectif : l'école paie un forfait groupé donnant accès à ses élèves (ex: 50 000 FCFA pour 50 élèves, 100 000 FCFA pour 150 élèves, etc.).
- Génère un code d'accès ou un QR code unique (ex: "LYCEE-2410") que les élèves utilisent pour s'inscrire et lier leur compte à l'école.
- Ne gère pas directement les œuvres publiques, mais sélectionne des livres dans un catalogue éducatif spécifique.

### Fonctionnalités détaillées
- **Gestion des Élèves** : Suivi du nombre d'élèves actifs (plafond défini par l'admin), gestion des inscriptions via code d'établissement.
- **Suivi Pédagogique** : Attribution de livres éducatifs et de quiz automatiques.
- **Statistiques Globales** : Suivi des progrès, du temps total de lecture/écoute pour l'ensemble de l'école.
- **Tableau de Bord** : Renouvellement manuel ou automatique de l'abonnement groupé.

---

## Élève / Étudiant (Espace Éducatif)

### Description et Accès
- Fait partie de l'**Espace Éducatif**.
- S'inscrit via un code d'établissement ou un QR code fourni par son école.
- Accède **uniquement** à la bibliothèque éducative de son établissement (livres recommandés, programme scolaire).
- Ne paie pas individuellement (couvert par l'abonnement de l'école).

### Fonctionnalités détaillées
- **Bibliothèque Personnalisée** : Accès aux livres recommandés par l'école/les professeurs, section "Mes téléchargements", "Reprendre ma lecture/écoute".
- **Évaluation (Quiz IA)** : Réalisation de quiz (10 questions automatiques générées par l'IA) avec correction immédiate après lecture de certains ouvrages.
- **Gamification** : Tableau de bord affichant un système de points, classement entre élèves, obtention de badges (ex: "Lecteur du mois"), et génération de certificats virtuels (PDF téléchargeables).
- **Tableau de Bord** : Statistiques personnelles (nombre de livres lus, temps total, historique de progression).

---

## Professeur (Espace Éducatif)

### Description et Accès
- Fait partie de l'**Espace Éducatif**.
- Délégué par l'école pour gérer les classes et le suivi pédagogique de proximité.

### Fonctionnalités détaillées
- **Gestion des Classes (`ClasseController`)** : Administration de classes virtuelles.
- **Attribution d'Œuvres (`BookAssignmentController`)** : Sélection et assignation de lectures spécifiques aux classes ou élèves.
- **Suivi des Progrès (`ProgressController`)** : Visualisation des performances et statistiques de lecture des élèves.
- **Gestion des Évaluations (`QuizController`)** : Supervision des résultats aux quiz et des tentatives.
- **Communication** : Messagerie interne pour interagir avec les élèves.

---

## Parent (Espace Éducatif)

### Description et Accès
- Fait partie de l'**Espace Éducatif**.
- A accès à un tableau de bord permettant le suivi de ses enfants (élèves).

### Fonctionnalités détaillées
- **Suivi (`DashboardController`)** : Consultation des progrès de lecture, des résultats de quiz et de l'assiduité (temps passé) de l'enfant.

---

## Lecteur Adulte (Espace Adulte Privé)

### Description et Accès
- Accède à l'**Espace Adulte**, un univers strictement privé, séparé et invisible du reste de la plateforme (non indexé, navigation cachée).
- Inscription **uniquement sur invitation** via un lien privé sécurisé.
- Aucun lien ni contenu de cet espace n'apparaît sur la plateforme publique ou éducative.

### Fonctionnalités détaillées
- **Inscription Restreinte (`InvitationController`)** : Accès au formulaire d'inscription uniquement avec un token valide (`/adult-invitation/{token}`).
- **Catalogue Dédié** : Achat, abonnement, lecture de PDF et écoute d'audios provenant de la bibliothèque adulte (via les routes du préfixe `/adult`).

---

## Administrateur

### Description et Accès
- Possède les droits absolus de supervision et de gestion sur toute la plateforme.
- Gère la validation des contenus, la modération et les aspects financiers centraux.

### Fonctionnalités détaillées
- **Validation et Modération** : Validation des auteurs, des écoles et des livres. Catégorisation stricte des œuvres (publique, éducative, adulte).
- **Gestion Financière** : Paramétrage des offres d'abonnements, supervision des paiements, calcul automatique des revenus à reverser aux auteurs et génération de rapports de répartition des gains.
- **Gestion Éducative** : Attribution des quiz aux œuvres éducatives, définition du nombre maximum d'élèves par plan d'abonnement école.
- **Administration Système** : Gestion des utilisateurs (tous rôles confondus), possibilité d'usurpation d'identité pour le support (`stop-impersonating`).
- **Tableau de Bord Central** : Vues analytiques complètes de l'activité de la plateforme.

---

## Modules et Fonctionnalités Transverses

### Quiz IA et Évaluations
- Les livres éducatifs approuvés peuvent intégrer un quiz automatique.
- Chaque quiz comporte 10 questions générées par l'IA (via API IA ou base de données interne), basées sur le contenu de l'ouvrage.
- Les questions sont aléatoires à chaque tentative, avec une correction immédiate.
- Les résultats sont stockés (`QuizAttempt`) et visibles dans les tableaux de bord de l'élève, du professeur et de l'école.

### Modèle Économique & Paiements (`PaymentController`, `Purchase`, `Subscription`)
- Paiements intégrés : Mobile Money (selon les pays, via le modèle `Country`) et Cartes bancaires (Visa/MasterCard).
- Les paiements peuvent concerner des abonnements récurrents ou des achats uniques.
- Les revenus générés (`Revenue`) déclenchent un calcul de redistribution (`AuthorPayout`) en fin de mois.

### Messagerie & Notifications
- **Messagerie Interne (`MessagingApiController`, `Conversation`)** : Communication principalement utilisée dans l'espace éducatif (ex: Enseignant vers Élève).
- **Notifications (`NotificationApiController`, `Notification`)** : Alertes in-app pour tous les utilisateurs (validation de livre pour un auteur, nouveau livre attribué pour un élève, etc.).

### Gamification et Rétention (`Badge`, `UserBadge`)
- Le système évalue des critères précis (`books_required`, `minutes_required`, `quizzes_required`) stockés dans la table `badges`.
- Un pointage global (`points` sur la table `users`) permet d'établir des classements (leaderboards).
- Les élèves reçoivent des certificats (générés en PDF via `barryvdh/laravel-dompdf`).

### Expérience de Lecture / Écoute Multilingue
- Support des langues locales et internationales (Français, Anglais, Baoulé, Malinké, Bété, Wolof, Swahili, etc.).
- Fonctionnalités prévues : Traduction instantanée ou lecture bilingue.
