# 🎮 GAMY – Module de Connexion (Login)

Bienvenue sur le dépôt du projet **GAMY**, un site web de guides pour jeux vidéo, réalisé par notre groupe de 6 étudiants.  
Ce dépôt correspond à la tâche que j’ai réalisée : **le module utilisateur (connexion)**.

---

## 📌 Objectif de ma tâche

Mettre en place une page de connexion pour les utilisateurs contenant :

- Un champ **Email**
- Un champ **Mot de passe**
- Un champ **ID utilisateur**
- Un design **Cyberpunk sombre (noir/rouge)**
- Un **logo GAMY** avec une ligne rouge fine en dessous
- Une **barre de navigation** contenant les icônes :
  - 🏠 Accueil
  - 🎧 Support

---

## 💻 Travail effectué

✅ **Page `login.html`** :
- Structure HTML avec les champs nécessaires (Email, Mot de passe, ID utilisateur)
- Icônes ajoutées via Font Awesome pour la barre de navigation
- Formulaire centré au milieu de la page avec des champs **obligatoires** (utilisation de l'attribut `required`)

✅ **Fichier `style.css`** :
- Arrière-plan noir clair avec une ligne rouge fine sous la barre de navigation
- Texte, logo et boutons en rouge, avec des effets au survol pour plus de dynamisme
- Mise en forme responsive et moderne pour assurer une bonne lisibilité sur toutes les tailles d'écran
- Ajout d'un lien vers la page d'inscription pour les nouveaux utilisateurs

---

## 💼 Front-office

Le **front-office** de cette application se réfère à la partie visible par l'utilisateur, où il peut interagir avec la page de connexion. La page présente :

- Un formulaire de connexion avec les champs **Email**, **Mot de passe**, et **ID utilisateur**.
- Un design **Cyberpunk** utilisant une palette de couleurs noire et rouge.
- Une barre de navigation avec des icônes pour l'**Accueil** et le **Support**.
- Un formulaire bien centré, offrant une expérience utilisateur claire et fluide.

---

## ⚙️ Back-office

Le **back-office** se réfère à la partie gestion de l'application qui traite les données soumises par l'utilisateur. Bien que pour le moment, cette partie n'inclut que la mise en place du formulaire et du design :

- **Stockage des informations utilisateur** : L'interface de connexion sera bientôt connectée à une base de données pour valider les utilisateurs en fonction de leurs informations.
- **Gestion des erreurs** : Une gestion des erreurs (comme un mot de passe incorrect ou un email inexistant) sera ajoutée dans les étapes futures avec des messages d'erreur visibles pour l'utilisateur.
- **Interface d'administration** (à venir) : Une page pour la gestion des utilisateurs, qui permettra de visualiser et gérer les comptes utilisateurs via un panneau d'administration sécurisé.

---

## 📂 Fichiers principaux

| Fichier       | Description                             |
|---------------|-----------------------------------------|
| `login.html`  | La page de connexion utilisateur avec le formulaire et la barre de navigation |
| `style.css`   | Feuille de style commune (design UI)    |
| `index.html`  | Page d'accueil avec le même design      |

---

## 🔧 Prochaines étapes

- Ajouter une validation des champs via JavaScript pour garantir des entrées correctes.
- Intégrer la page avec la base de données pour gérer les connexions réelles des utilisateurs (si nécessaire).
- Ajouter des messages d'erreur ou de succès après la soumission du formulaire pour améliorer l'expérience utilisateur.
- Implémenter une page de gestion des utilisateurs (Back-office).

---

## 🙋‍♀️ Réalisé par

- **[Wala Bentahar GitHub]**
- Étudiante responsable du module Login
- Projet GAMY – Groupe de 6 membres

---

Merci à ma tutrice pour le suivi 👩‍🏫
