Addresbook playground project
========================
A simple project for testing Symfony 3.4 with Twig, Vue 2 and TypeScript

# Configure the release app
1. Navigate to the project folder, copy the ``paremeters.yml.dist`` to ``parameters.yml`` and adjust it to your needs:
```bash
cp ./app/config/parameters.yml.dist ./app/config/parameters.yml
```
2. Install the database, if needed (for our example we are using sqlite which needs to be created):
```bash
php ./bin/console doctrine:database:create
php ./bin/console doctrine:schema:update --force
```
3. Clear the cache:
```bash
php ./bin/console cache:clear --env=prod
```

# Set up the development app
1. Navigate to the project folder, copy the ``paremeters.yml.dist`` to ``parameters.yml`` and adjust it to your needs:
```bash
cp ./app/config/parameters.yml.dist ./app/config/parameters.yml
```
2. Intall vue-cli globally, if not already:
```bash
sudo npm install -g @vue/cli
```
3. Install the dependencies:
```bash
composer install
cd ./src/AddressBookBundle/Resources/vue-app
npm install
```
4. Build the app:
```bash
cd ./src/AddressBookBundle/Resources/vue-app
npm run build
```
5. Install the database, if needed (for our example we are using sqlite which needs to be created):
```bash
php bin/console doctrine:database:create
```
6. Clear the cache, if needed:
```bash
php ./bin/console cache:clear
```
