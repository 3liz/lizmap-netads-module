# NetADS

[![Packagist](https://img.shields.io/packagist/v/lizmap/lizmap-netads-module)](https://packagist.org/packages/lizmap/lizmap-netads-module)

## Documentation

Présentation, guide, installation, Lizmap Web Client

Pour la configuration de l'extension lizmap : https://docs.3liz.org/qgis-netads-plugin/

## Installation

Il est recommandé d'installer le module avec [Composer](https://getcomposer.org/), le gestionnaire de paquet pour PHP. Si vous ne pouvez pas
l'utiliser, utilisez la méthode manuelle indiquée plus bas.

NB : tous les chemins ci-dessous sont relatifs au dossier de Lizmap Web Client.

### Copie des fichiers automatique avec Composer

* Dans `lizmap/my-packages`, créer le fichier `composer.json` s'il n'existe pas déjà, en copiant le fichier `composer.json.dist`,
* puis installer le module avec Composer :

```bash
    cp -n lizmap/my-packages/composer.json.dist lizmap/my-packages/composer.json
    composer require --working-dir=lizmap/my-packages "lizmap/lizmap-netads-module"
```

### Copie des fichiers manuelle (sans composer)


 * Téléchargez l'archive sur la page des [versions dans GitHub](https://github.com/3liz/lizmap-netads-module/releases).

 * Extrayez les fichiers de l'archive et copier le répertoire `netads` dans `lizmap/lizmap-modules/`.

### Installation du module

* Allez dans le répertoire `lizmap/install/` pour lancer la configuration de l'installateur

```bash
php configurator.php netads
```
* Lancez enfin l'installation du module :

```bash
php installer.php
./clean_vartmp.sh
./set_rights.sh
```

### Configuration du module

 * Connectez-vous à lizmap en tant qu'administrateur, en configurer le module depuis la section NetADS > Configuration

 * Saisissez les différents champs nécessaires :
   * Login
   * Mot de passe chiffré
   * URL de recherche des dossier NetADS
   * URL de consultation des dossiers NetADS
   * L'identifiant du client NetADS par défaut n'est pas obligatoire, celui défini dans le projet lizmap est utilisé

 * Pour que les liens vers la parcelle depuis NetADS fonctionnent correctement (ie, zoom sur la parcelle), il faut que la couche `parcelles` soit publiée en WFS

## Configuration du projet QGIS

Pour que votre projet QGIS/Lizmap utilise les fonctionnalités du module, il faut :
 * que le nom du fichier projet soit `netads` (netads.qgs donc)
 * que le projet ait une variable de projet `netads_idclient` contenant votre identifiant client NetADS
