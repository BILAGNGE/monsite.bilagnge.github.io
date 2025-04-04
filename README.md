# 🌐 monsite - Site Web PHP  

Bienvenue sur le projet **monsite**, un site web PHP simple qui permet aux utilisateurs de s’inscrire, se connecter, modifier leur profil et gérer leurs informations personnelles (photo, bio, réseaux sociaux, etc.).

## 📁 Structure du projet

### Fichiers principaux
- `index.php` : Page d'accueil.  
- `login.html` : Connexion.  
- `signup.php` : Inscription.  
- `profile.php` : Affichage du profil utilisateur.  
- `edit_profile.php` : Modification du profil.  
- `reset_password.php` : Réinitialisation du mot de passe.  
- `reset_request.php` : Demande de réinitialisation.  
- `contact.php` : Page de contact.  
- `logout.php` : Déconnexion.  
- `users_list.php` : Liste des utilisateurs (admin).  

### Dossiers
- `includes/` : Fichiers inclus (fonctions, configuration…).  
- `galerie_profile/` : Images de profils des utilisateurs.  

### CSS
- `style.css` : Style global.  
- `csslogin.css`, `csssignup.css`, `cssreset_password.css` : Styles spécifiques aux pages.  

---

## 🚀 Installation et exécution

1. **Cloner le dépôt :**
   ```bash
   git clone https://github.com/BILAGNGE/monsite.git
   ```
2. **Placer le dossier** dans `htdocs` (XAMPP) ou `www` (WAMP).  
3. **Lancer votre serveur web** (Apache).  
4. Accéder au site via :  
   ```
   http://localhost/monsite
   ```

---

## 👤 Utilisation du site

### 🆕 Nouvel utilisateur
1. Accédez à `signup.php`.  
2. Remplissez le formulaire (nom, prénom, email, mot de passe).  
3. Cliquez sur **"S'inscrire"**.  
4. Une fois inscrit, connectez-vous via `login.html`.

### 🔐 Connexion
Rendez-vous sur `login.html` et connectez-vous avec vos identifiants.

### ⚙️ Gérer son profil
- Une fois connecté, vous pouvez consulter votre profil (`profile.php`).  
- Pour mettre à jour vos informations personnelles (photo, bio, réseaux sociaux), utilisez `edit_profile.php`.

---

## 👀 Comptes de démonstration

### 🔹 Utilisateur "test" (profil non encore mis à jour)
- **Nom** : `test`  
- **Mot de passe** : `testtest`  
⚠️ Le profil "test" **n'a pas encore été complété** :
- Pas de photo de profil
- Pas de liens vers les réseaux sociaux  
➡ Une fois que l'utilisateur mettra à jour son profil via `edit_profile.php`, tout s’affichera automatiquement dans la page de profil.

---

### ✅ Utilisateur "ciel" (profil complet)
- **Nom** : `ciel`  
- **Email** : `ciel@gmail.com`  
- **Date de naissance** : `1 avril 2000`  
- **Adresse** : `ciel road`  
- **Bio** : `ciel bio`  
- **Photo de profil** : ✅  
- **Facebook** : ✅  
- **Twitter** : ✅  
- **Instagram** : ✅  
- **Mot de passe** : `ciel`  

➡ Connectez-vous avec ce compte pour voir un exemple de **profil utilisateur complet et fonctionnel**.

---

## 🔧 Conseils techniques

- ⚠️ Assurez-vous d'avoir une base de données configurée.  
- ✅ Vérifiez les permissions sur `galerie_profile/` pour permettre le téléversement de photos.  
- 🔒 Pour la production, pensez à sécuriser les entrées (contre les injections SQL, XSS, etc.).

---

📬 Pour toute question ou suggestion, rendez-vous sur le dépôt GitHub :  
👉 [https://github.com/BILAGNGE/monsite]
