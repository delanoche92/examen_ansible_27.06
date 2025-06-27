# Déploiement WordPress avec Ansible (Multi-OS)

Ce projet Ansible permet de déployer une stack WordPress complète (Apache, PHP, MariaDB, WordPress) sur des serveurs **Ubuntu (famille Debian)** et **Rocky Linux (famille RedHat)** de manière automatisée et idempotente.

## Table des matières

- [Déploiement WordPress avec Ansible (Multi-OS)](#déploiement-wordpress-avec-ansible-multi-os)
  - [Table des matières](#table-des-matières)
  - [Prérequis](#prérequis)
  - [Structure du Projet](#structure-du-projet)
  - [Rôles](#rôles)
    - [`common`](#common)
    - [`mariadb_server`](#mariadb_server)
    - [`wordpress`](#wordpress)
  - [Variables](#variables)
    - [Variables du Playbook (`playbooks/deploy_wordpress.yml`)](#variables-du-playbook-playbooksdeploy_wordpressyml)
    - [Variables Secrètes (`group_vars/all/password.yml`)](#variables-secrètes-group_varsallpasswordyml)
  - [Exécution](#exécution)
    - [1. Cloner le dépôt](#1-cloner-le-dépôt)
    - [2. Configurer l'Inventaire](#2-configurer-linventaire)
    - [3. Configurer les Variables Secrètes](#3-configurer-les-variables-secrètes)
    - [4. Lancer le Déploiement](#4-lancer-le-déploiement)
  - [Compatibilité OS](#compatibilité-os)
  - [Contributions](#contributions)
  - [Licence](#licence)

## Prérequis

* **Ansible** (version 2.10 ou supérieure recommandée) installé sur votre machine de contrôle.
* **Accès SSH** aux serveurs cibles avec un utilisateur ayant des privilèges `sudo` (sans mot de passe si possible, via clé SSH).
* Les serveurs cibles doivent être soit **Ubuntu** (ou une distribution basée sur Debian), soit **Rocky Linux** (ou une distribution basée sur RedHat comme CentOS Stream, AlmaLinux).

## Structure du Projet
## Rôles

Ce projet est organisé en trois rôles principaux pour une meilleure modularité et réutilisation.

### `common`

Ce rôle gère les tâches génériques de configuration du système :
* Mise à jour du cache des paquets et mise à niveau des paquets selon la famille d'OS (APT pour Debian/Ubuntu, YUM/DNF pour RedHat/Rocky).
* Installation des paquets essentiels comme Apache/HTTPD, PHP et ses modules, MariaDB client, `wget` et `unzip`.

### `mariadb_server`

Ce rôle est responsable de la configuration de la base de données :
* Démarrage et activation du service MariaDB.
* Sécurisation de l'installation de MariaDB (modification du mot de passe root, suppression des utilisateurs anonymes et de la base de données de test).
* Création de la base de données et de l'utilisateur spécifiés pour WordPress, avec les privilèges nécessaires.

### `wordpress`

Ce rôle déploie l'application WordPress et configure le serveur web :
* Suppression de la page par défaut du serveur web.
* Téléchargement et décompression des fichiers WordPress.
* Copie des fichiers WordPress vers la racine web (`/var/www/html/`) avec les permissions appropriées (`www-data` pour Debian/Ubuntu, `apache` pour RedHat/Rocky).
* Génération dynamique du fichier `wp-config.php` à partir d'un template Jinja2, en utilisant les variables de la base de données et en générant des clés de sécurité uniques.
* Configuration du Virtual Host Apache/HTTPD pour WordPress, activation des sites et modules nécessaires (ex: `mod_rewrite`).
* Redémarrage ou rechargement du service Apache/HTTPD en fonction des changements.

## Variables

Les variables permettent de personnaliser le déploiement sans modifier le code des tâches.

### Variables du Playbook (`playbooks/deploy_wordpress.yml`)


Ces variables sont définies directement dans le playbook principal et sont généralement moins sensibles :

* `wordpress_db_name`: Nom de la base de données WordPress (par défaut: `"wordpress"`).
* `wordpress_db_user`: Nom d'utilisateur de la base de données WordPress (par défaut: `"example"`).
* `wordpress_db_host`: Hôte de la base de données (par défaut: `"localhost"`).

### Variables Secrètes (`group_vars/all/password.yml`)

Ce fichier contient les mots de passe sensibles et **DOIT ÊTRE CHIFFRÉ AVEC ANSIBLE VAULT**.

* `mariadb_root_password`: Mot de passe pour l'utilisateur `root` de MariaDB.
* `wordpress_db_password`: Mot de passe pour l'utilisateur de la base de données WordPress.

**Exemple de `group_vars/all/password.yml` (AVANT CHIFFREMENT) :**

```yaml
---
mariadb_root_password: "votre_mot_de_passe_root_mariadb"
wordpress_db_password: "votre_mot_de_passe_wordpre

modification du fichier d'inventaire
# inventory.ini
[votre_serveur_cible]
ubuntu_server_ip ansible_user=votre_utilisateur_ssh ansible_ssh_private_key_file=/chemin/vers/votre/cle/ssh
rocky_server_ip ansible_user=votre_utilisateur_ssh ansible_ssh_private_key_file=/chemin/vers/votre/cle/ssh

# Exemple:
# [webservers]
# ubuntu-web-01.example.com ansible_user=deployuser ansible_ssh_private_key_file=~/.ssh/id_rsa
# rocky-web-01.example.com ansible_user=deployuser ansible_ssh_private_key_file=~/.ssh/id_rsa
### 1. Cloner le dépôt

```bash
git clone [https://github.com/votre_utilisateur/ansible_wordpress_deployment.git](https://github.com/votre_utilisateur/ansible_wordpress_deployment.git)
cd ansible_wordpress_deployment
# inventory.ini

[ubuntu_servers]
client2 ansible_host=127.0.0.1 ansible_port=2224 ansible_user=root ansible_ssh_pass=P@ssw0rd

[rocky_servers]
client4 ansible_host=127.0.0.1 ansible_port=2226 ansible_user=root ansible_ssh_pass=P@ssw0rd
. Configurer les Variables Secrètes
Ce projet utilise des mots de passe en clair pour simplifier l'exemple. C'est FORTEMENT DÉCONSEILLÉ pour la production. Pour un environnement sécurisé, utilisez Ansible Vault.

Si vous avez précédemment créé un fichier group_vars/all/password.yml chiffré, vous pouvez le supprimer pour cet exemple :

Bash

rm group_vars/all/password.yml
Les mots de passe sont directement définis dans la section vars du playbook playbooks/deploy_wordpress.yml.

4. Lancer le Déploiement
Exécutez le playbook principal en spécifiant votre inventaire.

Bash

ansible-playbook -i inventory.ini playbooks/deploy_wordpress.yml -e 'ansible_ssh_common_args="-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null"'
Si l'exécution se déroule sans erreur, votre installation WordPress devrait être accessible via l'adresse IP ou le nom d'hôte de votre serveur, généralement sur http://votre_serveur_ip_ou_nom_hote:[port_mappé]. Par exemple, pour client2, ce serait http://localhost:8084.

Compatibilité OS
Ce projet utilise les faits (facts) collectés par Ansible (ansible_os_family) pour adapter les tâches aux systèmes d'exploitation. Il prend en charge :

Debian-based systems (comme Ubuntu) utilisant apt et les services Apache (apache2) et MariaDB (mariadb).

RedHat-based systems (comme Rocky Linux) utilisant yum/dnf et les services HTTPD (httpd) et MariaDB (mariadb).

Contributions
Les contributions sont les bienvenues ! N'hésitez pas à ouvrir des issues ou soumettre des Pull Requests.


Licence
Ce proj
et est sous licence MIT License.





![image](https://github.com/user-attachments/assets/d52dbeae-8e4b-499b-95f3-8a8b0b6996c1)
