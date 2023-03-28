# projetbe
(documentation technique et utilisateur envoyées par mail)
Installation :
1. git clone https://github.com/Maxkaiser7/projet_bienetre.git
2. entrez ces 3 lignes dans la console 
composer install
yarn install
yarn encore dev
3. créez une base de données (phpMyAdmin)
4. ajoutez le nom de votre base dans le .env à DATABASE_URL
5. Entrez ces lignes dans la console 
php bin/console make:migration 
php bin/console doctrine:migrations:migrate
6. Copiez-collez le contenu du fichier à la racine de ce projet DB.md dans 'SQL' de phpMyAdmin
7. Inscrivez-vous sur le site
8. Entrez ceci dans 'SQL' de phpMyAdmin (remplacez admin@bienetre.be par votre mail)
UPDATE `utilisateur`
   SET `roles` = '[\"ROLE_ADMIN\"]', `is_verified` = 1
   WHERE `email` = 'admin@bienetre.be';
