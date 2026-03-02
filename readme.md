# Plugin WordPress CRUD Demo

## 📚 Objectif pédagogique

Ce plugin WordPress est conçu pour enseigner les concepts fondamentaux du développement WordPress et PHP orienté objet. Il implémente un système CRUD (Create, Read, Update, Delete) complet avec une architecture MVC (Model-View-Controller).

## 🎯 Concepts abordés

- **Programmation Orientée Objet (POO)** en PHP
- **Architecture MVC** (Model-View-Controller)
- **Hooks WordPress** (actions et filtres)
- **Sécurité WordPress** (nonces, sanitization, validation)
- **Interaction avec la base de données** (wpdb)
- **Gestion des menus d'administration**
- **Enqueue de scripts JavaScript**

---

## 📂 Structure du plugin

```
crud-demo/
├── crud-demo.php              # Fichier principal (Contrôleur)
├── models/
│   └── ElementModel.php       # Modèle de données
├── views/
│   ├── admin-listing-page.php # Vue : liste des éléments
│   └── admin-form-page.php    # Vue : formulaire ajout/modification
└── assets/
    └── js/
        └── admin-crud.js      # JavaScript pour interactions
```

---

## 🔧 Installation

1. Placez le dossier `crud-demo` dans `/wp-content/plugins/`
2. Activez le plugin dans l'administration WordPress
3. Une table `wp_element` sera automatiquement créée
4. Accédez au menu "CRUD Démo" dans l'admin

> [📥 Télécharger le module](https://downgit.github.io/#/home?url=https://github.com/webrelief/dwwm/tree/main/crud-demo)

---

## 📖 Explication détaillée du code

### 1. Fichier principal : `crud-demo.php`

#### En-tête du plugin

```php
/**
 * Plugin Name: CRUD Demo
 * Description: Module WordPress permettant de créer une table...
 * Version: 1.0
 * Author: David
 */
```

**Explication :** Ces commentaires sont obligatoires pour que WordPress reconnaisse le fichier comme un plugin. Ils apparaissent dans la liste des plugins.

---

#### Sécurité d'accès direct

```php
if (!defined('ABSPATH')) {
    exit;
}
```

**Explication :** 
- `ABSPATH` est une constante WordPress définie dans `wp-config.php`
- Si quelqu'un tente d'accéder directement au fichier PHP (sans passer par WordPress), il sera bloqué
- **Bonne pratique de sécurité** à appliquer dans tous les fichiers PHP

---

#### Chargement du modèle

```php
require_once plugin_dir_path(__FILE__) . 'models/ElementModel.php';
```

**Explication :**
- `plugin_dir_path(__FILE__)` retourne le chemin absolu du dossier du plugin
- `require_once` charge le fichier une seule fois (évite les erreurs de redéclaration)
- On sépare la logique métier (modèle) du contrôleur principal

---

#### Classe principale : `CrudDemoPlugin`

```php
class CrudDemoPlugin
{    
    private $elementModel;

    public function __construct()
    {        
        $this->elementModel = new ElementModel();

        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'handleActions']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }
```

**Explication :**

1. **`private $elementModel;`** : Propriété privée pour stocker l'instance du modèle
2. **`__construct()`** : Le constructeur s'exécute automatiquement à la création de l'objet
3. **`new ElementModel()`** : On crée une instance du modèle pour accéder aux données
4. **`add_action()`** : Fonction WordPress pour "accrocher" nos méthodes à des événements WordPress

**Les hooks utilisés :**
- `admin_menu` : Déclenché quand WordPress construit le menu admin
- `admin_init` : Déclenché au début de chaque requête admin (idéal pour traiter les formulaires)
- `admin_enqueue_scripts` : Pour charger CSS/JS dans l'admin

**Syntaxe `[$this, 'nomMethode']` :**
- C'est un **callable** (fonction appelable)
- `$this` fait référence à l'objet actuel
- `'nomMethode'` est le nom de la méthode à appeler

---

#### Chargement des scripts JavaScript

```php
public function enqueueScripts($hook)
{
    if (strpos($hook, 'crud-demo') === false) {
        return;
    }
    
    wp_enqueue_script(
        'crud-demo-admin',
        plugin_dir_url(__FILE__) . 'assets/js/admin-crud.js',
        ['jquery'],
        '1.0',
        true
    );
}
```

**Explication :**

1. **`$hook`** : Identifiant de la page admin actuelle (ex: `toplevel_page_crud-demo-listing`)
2. **`strpos($hook, 'crud-demo') === false`** : Vérifie si "crud-demo" est dans le nom de la page
3. **`return;`** : Si ce n'est pas notre page, on ne charge rien (optimisation)
4. **`wp_enqueue_script()`** : Fonction WordPress pour charger un script correctement

**Paramètres de `wp_enqueue_script()` :**
- Handle unique : `'crud-demo-admin'`
- URL du fichier : `plugin_dir_url(__FILE__) . 'assets/js/admin-crud.js'`
- Dépendances : `['jquery']` (jQuery sera chargé avant notre script)
- Version : `'1.0'` (pour le cache du navigateur)
- Dans le footer : `true` (charge le script en bas de page)

---

#### Création du menu d'administration

```php
public function addAdminMenu()
{
    add_menu_page(
        'CRUD Démo',                      // Titre de la page
        'CRUD Démo',                      // Texte du menu
        'manage_options',                 // Capacité requise
        'crud-demo-listing',              // Slug unique
        [$this, 'renderAdminListingPage'], // Fonction de rendu
        'dashicons-admin-settings',       // Icône
        30                                // Position dans le menu
    );
    
    add_submenu_page(
        'crud-demo-listing',              // Parent slug
        'Ajouter un élément',             // Titre de la page
        'Ajouter',                        // Texte du menu
        'manage_options',                 // Capacité requise
        'crud-demo-form',                 // Slug unique
        [$this, 'renderAdminFormPage']    // Fonction de rendu
    );
}
```

**Explication :**

**`add_menu_page()`** : Crée un menu principal dans l'admin WordPress

- **`'manage_options'`** : Capacité WordPress (seuls les administrateurs peuvent y accéder)
- **`'crud-demo-listing'`** : Le "slug" est l'identifiant unique de la page
- **`[$this, 'renderAdminListingPage']`** : La méthode qui affichera le contenu
- **`'dashicons-admin-settings'`** : Icône Dashicons WordPress
- **`30`** : Position (plus le chiffre est bas, plus c'est haut dans le menu)

**`add_submenu_page()`** : Crée un sous-menu

- Le premier paramètre doit correspondre au slug du menu parent
- Crée automatiquement un lien dans le sous-menu

---

#### Rendu des pages (Vues)

```php
public function renderAdminFormPage()
{
    $element = null;
    $is_edit = false;
    
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = intval($_GET['id']);
        $element = $this->elementModel->getById($id);
        
        if (!$element) {
            wp_die('Élément non trouvé');
        }
        
        $is_edit = true;
    }
    
    include plugin_dir_path(__FILE__) . 'views/admin-form-page.php';
}
```

**Explication :**

1. **Initialisation des variables** : `$element = null;` et `$is_edit = false;`
2. **Détection du mode** : Si `?id=123` est dans l'URL, on est en mode édition
3. **`intval($_GET['id'])`** : Convertit en entier (sécurité basique)
4. **`$this->elementModel->getById($id)`** : Appel au modèle pour récupérer les données
5. **`wp_die()`** : Fonction WordPress pour arrêter l'exécution avec un message
6. **`include`** : Charge la vue (le fichier HTML/PHP)

**Pourquoi les variables sont accessibles dans la vue ?**
- `$element`, `$is_edit` sont définies avant le `include`
- Le fichier inclus hérite de toutes les variables du scope actuel

---

#### Gestion des actions (Traitement des formulaires)

```php
public function handleActions()
{
    // Gestion de la suppression
    if (isset($_GET['page']) && $_GET['page'] === 'crud-demo-listing' 
        && isset($_GET['action']) && $_GET['action'] === 'delete' 
        && isset($_GET['id'])) {
        
        $id = intval($_GET['id']);
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_element_' . $id)) {
            wp_die('Action non autorisée');
        }
        
        $this->elementModel->delete($id);
        
        wp_redirect(admin_url('admin.php?page=crud-demo-listing&deleted=1'));
        exit;
    }
```

**Explication détaillée :**

1. **Vérification multiple** : On vérifie qu'on est sur la bonne page avec la bonne action
2. **`intval($id)`** : Conversion en entier pour sécuriser
3. **`wp_verify_nonce()`** : **CRUCIAL pour la sécurité !**
   - Vérifie que la requête vient bien de notre site
   - Protège contre les attaques CSRF (Cross-Site Request Forgery)
   - Le nonce doit correspondre à celui généré dans la vue
4. **`wp_redirect()`** : Redirige vers une URL
5. **`exit;`** : **IMPORTANT** : Arrête l'exécution après la redirection
6. **`&deleted=1`** : Paramètre GET pour afficher un message de confirmation

---

#### Traitement du formulaire d'ajout/modification

```php
if (isset($_POST['crud_demo_submit'])) {
    if (!isset($_POST['crud_demo_nonce']) 
        || !wp_verify_nonce($_POST['crud_demo_nonce'], 'crud_demo_save')) {
        wp_die('Action non autorisée');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('Vous n\'avez pas les permissions nécessaires');
    }
    
    $name = sanitize_text_field($_POST['name']);
    
    if (empty($name)) {
        wp_redirect(admin_url('admin.php?page=crud-demo-form&error=empty'));
        exit;
    }
    
    if (strlen($name) > 50) {
        wp_redirect(admin_url('admin.php?page=crud-demo-form&error=toolong'));
        exit;
    }
    
    if (isset($_POST['id_element']) && !empty($_POST['id_element'])) {
        $id = intval($_POST['id_element']);
        $this->elementModel->update($id, $name);
        wp_redirect(admin_url('admin.php?page=crud-demo-listing&updated=1'));
    } else {
        $this->elementModel->create($name);
        wp_redirect(admin_url('admin.php?page=crud-demo-listing&added=1'));
    }
    
    exit;
}
```

**Explication du processus de sécurité :**

1. **Vérification du nonce** : Protection CSRF
2. **`current_user_can('manage_options')`** : Vérification des permissions
3. **`sanitize_text_field()`** : **ESSENTIEL !**
   - Nettoie la donnée (enlève HTML, scripts malveillants, etc.)
   - Protection contre les injections XSS
4. **Validation métier** : Vérifie que les données respectent les règles (non vide, longueur max)
5. **Distinction ajout/modification** : Si `id_element` existe, c'est une modification

**Flux de données sécurisé :**
```
Formulaire → Vérification nonce → Vérification permissions 
→ Sanitization → Validation → Base de données
```

---

#### Hooks d'activation/désactivation

```php
$crudDemoPlugin = new CrudDemoPlugin();
register_activation_hook(__FILE__, [$crudDemoPlugin, 'createTable']);
register_deactivation_hook(__FILE__, [$crudDemoPlugin, 'dropTable']);
```

**Explication :**

1. **Instance créée EN DEHORS de la classe** : C'est voulu !
2. **`register_activation_hook()`** : S'exécute UNE SEULE FOIS lors de l'activation
3. **`register_deactivation_hook()`** : S'exécute lors de la désactivation
4. **Pourquoi pas dans le constructeur ?** 
   - Le constructeur s'exécute à chaque requête
   - Ces hooks doivent être enregistrés au niveau du fichier, pas de l'instance

---

### 2. Modèle : `models/ElementModel.php`

```php
class ElementModel
{
    private $table_name;
    private $wpdb;
    
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'element';
    }
```

**Explication :**

1. **`global $wpdb;`** : Accès à l'objet global de base de données WordPress
2. **`$wpdb->prefix`** : Préfixe des tables WordPress (généralement `wp_`)
   - Permet la compatibilité multi-sites
   - Table finale : `wp_element` (ou autre préfixe)

---

#### Récupérer tous les éléments

```php
public function getAll()
{
    $sql = "SELECT * FROM {$this->table_name} ORDER BY id_element DESC";
    return $this->wpdb->get_results($sql);
}
```

**Explication :**

- **`get_results()`** : Retourne un tableau d'objets
- **`ORDER BY id_element DESC`** : Les plus récents en premier
- **Pas de préparation** : OK car pas de variable externe (pas de risque d'injection SQL)

---

#### Récupérer un élément par ID

```php
public function getById($id)
{
    return $this->wpdb->get_row(
        $this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id_element = %d",
            $id
        )
    );
}
```

**Explication :**

1. **`get_row()`** : Retourne UN SEUL objet (ou null)
2. **`prepare()`** : **ESSENTIEL pour la sécurité !**
   - Échappe les variables pour prévenir les injections SQL
   - `%d` = placeholder pour un entier
   - `%s` serait pour une chaîne de caractères
3. **Préparation obligatoire** : Dès qu'on utilise une variable externe dans une requête SQL

---

#### Créer un élément

```php
public function create($name)
{
    $result = $this->wpdb->insert(
        $this->table_name,
        ['name' => $name],
        ['%s']
    );
    
    return $result !== false ? $this->wpdb->insert_id : false;
}
```

**Explication :**

1. **`insert()`** : Méthode WordPress sécurisée pour insérer des données
   - Premier paramètre : nom de la table
   - Deuxième paramètre : tableau associatif (colonne => valeur)
   - Troisième paramètre : types de données (`%s` = string, `%d` = integer)
2. **`$this->wpdb->insert_id`** : ID de l'enregistrement créé (auto-increment)
3. **Opérateur ternaire** : Retourne l'ID si succès, false sinon

---

#### Mettre à jour un élément

```php
public function update($id, $name)
{
    return $this->wpdb->update(
        $this->table_name,
        ['name' => $name],           // Données à mettre à jour
        ['id_element' => $id],       // Condition WHERE
        ['%s'],                      // Format des données
        ['%d']                       // Format de la condition
    ) !== false;
}
```

**Explication :**

- Paramètres : table, données, condition WHERE, formats
- Retourne `true` en cas de succès, `false` sinon

---

#### Supprimer un élément

```php
public function delete($id)
{
    return $this->wpdb->delete(
        $this->table_name,
        ['id_element' => $id],
        ['%d']
    ) !== false;
}
```

**Explication :** Similaire à `update()`, mais pour la suppression.

---

#### Créer la table

```php
public function createTable()
{
    $charset_collate = $this->wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE {$this->table_name} (
        id_element int(11) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL,
        PRIMARY KEY (id_element)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
```

**Explication :**

1. **`get_charset_collate()`** : Récupère le charset et collation de WordPress
   - Exemple : `DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`
   - Assure la compatibilité avec la configuration WordPress
2. **`dbDelta()`** : Fonction WordPress pour créer/mettre à jour des tables
   - Plus intelligent qu'un simple `CREATE TABLE`
   - Détecte les différences et met à jour si besoin
   - **Attention aux espaces** : Syntaxe stricte !
3. **`require_once(ABSPATH . 'wp-admin/includes/upgrade.php')`** : Nécessaire pour utiliser `dbDelta()`

---

### 3. Vue de listing : `views/admin-listing-page.php`

#### Sécurité et structure de base

```php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">CRUD Démo - Liste des éléments</h1>
    <a href="<?php echo admin_url('admin.php?page=crud-demo-form'); ?>" 
       class="page-title-action">Ajouter</a>
    <hr class="wp-header-end">
```

**Explication :**

1. **`class="wrap"`** : Classe WordPress standard pour les pages admin
2. **`class="wp-heading-inline"`** : Style WordPress pour le titre
3. **`admin_url()`** : Génère l'URL de l'admin WordPress
4. **`class="page-title-action"`** : Classe WordPress pour les boutons d'action principaux
5. **`<hr class="wp-header-end">`** : Séparateur WordPress (utilisé pour positionner les notices)

---

#### Affichage des messages de confirmation

```php
<?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
    <div class="notice notice-success is-dismissible">
        <p>Élément ajouté avec succès.</p>
    </div>
<?php endif; ?>
```

**Explication :**

1. **`isset($_GET['added'])`** : Vérifie si le paramètre existe
2. **`$_GET['added'] == 1`** : Vérifie sa valeur
3. **Classes WordPress** :
   - `notice` : Conteneur de notification
   - `notice-success` : Notification verte (succès)
   - `is-dismissible` : Ajoute le bouton de fermeture
4. **Autres classes disponibles** : `notice-error`, `notice-warning`, `notice-info`

---

#### Tableau de données WordPress

```php
<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th scope="col" class="manage-column column-primary" style="width: 80px;">ID</th>
            <th scope="col" class="manage-column">Nom</th>
            <th scope="col" class="manage-column" style="width: 150px;">Actions</th>
        </tr>
    </thead>
    <tbody>
```

**Explication des classes WordPress :**

- **`wp-list-table`** : Style de tableau standard WordPress
- **`widefat`** : Tableau pleine largeur
- **`fixed`** : Colonnes à largeur fixe
- **`striped`** : Lignes alternées (zebra)
- **`manage-column`** : Style pour les en-têtes
- **`column-primary`** : Colonne principale (responsive)

---

#### Boucle d'affichage des éléments

```php
<?php foreach ($elements as $element): ?>
    <tr>
        <td class="column-primary" data-colname="ID">
            <strong><?php echo esc_html($element->id_element); ?></strong>
        </td>
        <td data-colname="Nom">
            <?php echo esc_html($element->name); ?>
        </td>
```

**Explication :**

1. **`foreach`** : Boucle sur tous les éléments
2. **`$element->id_element`** : Accès aux propriétés de l'objet
3. **`esc_html()`** : **CRUCIAL !**
   - Échappe le HTML (convertit `<` en `&lt;`, etc.)
   - Protection contre les attaques XSS
   - **À utiliser TOUJOURS** pour afficher des données
4. **`data-colname`** : Attribut pour le responsive (affiche le nom de colonne sur mobile)

---

#### Boutons d'action

```php
<a href="<?php echo admin_url('admin.php?page=crud-demo-form&id=' . $element->id_element); ?>" 
   class="button button-small">
    Modifier
</a>
<a href="<?php echo wp_nonce_url(admin_url('admin.php?page=crud-demo-listing&action=delete&id=' . $element->id_element), 'delete_element_' . $element->id_element); ?>" 
   class="button button-small button-link-delete">
    Supprimer
</a>
```

**Explication :**

1. **Bouton Modifier** :
   - `&id=123` : Passe l'ID en paramètre GET
   - La page de formulaire détectera cet ID et passera en mode édition

2. **Bouton Supprimer** :
   - **`wp_nonce_url()`** : Ajoute automatiquement le nonce à l'URL
   - Premier paramètre : URL de base
   - Deuxième paramètre : "action" du nonce (doit correspondre à la vérification)
   - `class="button-link-delete"` : Classe personnalisée pour le JavaScript

---

### 4. Vue de formulaire : `views/admin-form-page.php`

#### Variables dynamiques

```php
$page_title = $is_edit ? 'Modifier un élément' : 'Ajouter un élément';
$button_text = $is_edit ? 'Mettre à jour' : 'Ajouter';
$name_value = $is_edit ? esc_attr($element->name) : '';
```

**Explication :**

1. **Opérateur ternaire** : `condition ? valeur_si_vrai : valeur_si_faux`
2. **`$is_edit`** : Variable passée par le contrôleur
3. **`esc_attr()`** : Échappe pour les attributs HTML (différent de `esc_html()`)
   - `esc_html()` : Pour le contenu entre balises
   - `esc_attr()` : Pour les attributs (`value=""`, `href=""`, etc.)

---

#### Nonce et champ caché

```php
<form method="post" action="">
    <?php wp_nonce_field('crud_demo_save', 'crud_demo_nonce'); ?>
    
    <?php if ($is_edit): ?>
        <input type="hidden" name="id_element" value="<?php echo esc_attr($element->id_element); ?>">
    <?php endif; ?>
```

**Explication :**

1. **`method="post"`** : Données sensibles → POST (pas GET)
2. **`action=""`** : Vide = soumet au même URL
3. **`wp_nonce_field()`** : Génère automatiquement un champ caché avec le nonce
   - Premier paramètre : "action" (identifiant)
   - Deuxième paramètre : nom du champ
   - Génère : `<input type="hidden" name="crud_demo_nonce" value="abc123...">`
4. **Champ caché `id_element`** : Permet de différencier ajout/modification

---

#### Formulaire WordPress standard

```php
<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row">
                <label for="name">Nom <span class="description">(requis)</span></label>
            </th>
            <td>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="<?php echo $name_value; ?>" 
                    class="regular-text" 
                    maxlength="50"
                    required
                >
                <p class="description">Maximum 50 caractères.</p>
            </td>
        </tr>
    </tbody>
</table>
```

**Explication :**

1. **`class="form-table"`** : Tableau de formulaire WordPress (style automatique)
2. **`role="presentation"`** : Accessibilité (indique que c'est pour la mise en page)
3. **`<th scope="row">`** : Cellule d'en-tête de ligne
4. **`class="regular-text"`** : Classe WordPress pour les champs texte
5. **`class="description"`** : Classe WordPress pour les textes d'aide
6. **`maxlength="50"`** : Validation HTML5 (côté client)
7. **`required`** : Validation HTML5 (empêche la soumission si vide)

**Important** : La validation HTML5 n'est PAS suffisante ! On valide aussi côté serveur (dans `handleActions()`).

---

#### Boutons de soumission

```php
<p class="submit">
    <input 
        type="submit" 
        name="crud_demo_submit" 
        id="submit" 
        class="button button-primary" 
        value="<?php echo $button_text; ?>"
    >
    <a href="<?php echo admin_url('admin.php?page=crud-demo-listing'); ?>" 
       class="button">Annuler</a>
</p>
```

**Explication :**

1. **`class="submit"`** : Classe WordPress pour les boutons de soumission
2. **`name="crud_demo_submit"`** : Nom utilisé dans `handleActions()` pour détecter la soumission
3. **`class="button button-primary"`** : Classes WordPress
   - `button` : Style de bouton
   - `button-primary` : Bouton principal (bleu)
4. **Bouton Annuler** : Simple lien stylisé en bouton

---

### 5. JavaScript : `assets/js/admin-crud.js`

```javascript
(function($) {
    'use strict';
    
    $(document).ready(function() {
        $('.button-link-delete').on('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
                return false;
            }
        });
    });
    
})(jQuery);
```

**Explication ligne par ligne :**

1. **`(function($) { ... })(jQuery);`** : IIFE (Immediately Invoked Function Expression)
   - Crée un scope isolé
   - Évite les conflits avec d'autres scripts
   - `$` est un alias local pour `jQuery`

2. **`'use strict';`** : Mode strict JavaScript
   - Détecte plus d'erreurs
   - Empêche certaines pratiques dangereuses

3. **`$(document).ready(function() { ... })`** : Attend que le DOM soit chargé

4. **`$('.button-link-delete').on('click', ...)`** : 
   - Sélectionne tous les éléments avec la classe `button-link-delete`
   - Attache un événement `click`

5. **`confirm('...')`** : Boîte de dialogue JavaScript native
   - Retourne `true` si l'utilisateur clique "OK"
   - Retourne `false` si l'utilisateur clique "Annuler"

6. **`e.preventDefault()`** : Empêche l'action par défaut (suivre le lien)

7. **`return false;`** : Stoppe la propagation de l'événement

---

## 🔐 Points de sécurité essentiels

### 1. Protection contre l'accès direct
```php
if (!defined('ABSPATH')) {
    exit;
}
```
- Dans **TOUS** les fichiers PHP

### 2. Nonces (protection CSRF)
```php
// Génération
wp_nonce_field('action_name', 'field_name');
wp_nonce_url($url, 'action_name');
