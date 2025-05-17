# MindMap CLI
## Description
Un outil CLI pour créer des mindmaps à partir de fichiers markdown. Il permet de visualiser la structure d'un document et de naviguer facilement entre les sections.

# Prérequis
- PHP 8 ou supérieur
- Composer
## Installation
```bash
# Cloner le dépôt
git clone https://github.com/Bheutschi/mindmap-cli
```
```bash
# Se déplacer dans le répertoire du projet
cd mindmap-cli
```
```bash
# Installer les dépendances
composer install
```
# Utilisation
```bash
# Lancer le script
php script.php create 
```
À ce moment vous aurez la possiblité de choisir entre plusieurs options :

![Lancement de application](/docs/images/screen-launch.png)
Pour selectionner vous pouvez directement taper le numéro qui est à coté du choix ou mettre le nom de la commande qui a de l'autocomplétion.

### Créer une mindmap
Pour créer une mindmap par exemple je met 0 je lui donne un nom et j'appui sur entrer.
![Création d'une mindmap](/docs/images/screen-create.png)

La mindmap va alors se stocker dans le dossier /data
Pour importer une mindmap il faut mettre votre fichier json dans le dossier.
Je vais essayer d'ajouter la possibilité de l'importer depuis un autre dossier mais pour le moment il faut le mettre dans le dossier data.

### Affichage des mindmaps
Si vous selectionnez l'option 5 vous pourrez voir la mindmap en forme d'arbe.
![Affichage d'une mindmap](/docs/images/screen-tree.png)

# Fonctionnalitée à ajouter
- [ ] Ajouter la possibilité d'importer une mindmap depuis un autre dossier.
- [ ] Ajouter la possibilité de modifier une mindmap.
- [ ] Ajouter la possibilité de supprimer un noeud enfant.






















