CONTRIBUTING
============

*This document is a draft for contributing documentation. Please modify it if needed.*

While contributing, please ensure your compliance to the following policies, in order to keep the project clean :)

Joining the project
-------------------

Please open an issue before building something bigger than a small typo ;)

Git branches policy
-------------------

### Pull Request

Pull Requests should be merged in `develop` branch, tested and eventually merged in master.

### Master

The master branch is dedicated for **stable releases** of this project. Theorically, it should **always** be stable and safe to use directly this branch in production.

**Simple** hot-fixes should be commited in this branch, and then eventually propagated to other branches.

Note : InsaLan.fr does not have version numbers / specific releases. Each master commit is a tiny release.

### Develop

Unstable features should be tested in this branch. `develop` should **ALWAYS** be more updated than `master` (or even).

When needed, and if all tests are successful, this branch can be merged in `master`.

### Feature branches

Used when necessary, then merged in `develop` and destroyed.

Migrations policy
-----------------

### Creating migrations

Doctrine migrations are used for database structure updates directly in production.
**It is a really hazardous and critical point of the project**. A wrong or forgotten migration file could corrupt a production database, so be **really** cautious.

For dev purposes, you can only use :

```bash
php app/console doctrine:schema:update --force
```

But when merging `develop` into `master`, you **HAVE TO** build a new **migration** for production purposes.

```bash
php checkout master                         # The current database structure
php doctrine:schema:drop --force            # Clean-up the database
php app/console doctrine:schema:create
php app/console doctrine:fixtures:load
php checkout develop                        # Update to the last structure
php app/console doctrine:migrations:diff    # Create a raw migration file
```

Then edit the file generated under `app/DoctrineMigrations` to fit your needs. You may not have to edit it.
Then, you can test your new migration with :

```bash
php app/console doctrine:migrations:migrate
```

Of course, this file should be included on your final commit before merging to master.

### Using migrations

On the production side, it's then really easy.

```bash
git clone
php app/console doctrine:migrations:status  # Just to check the situation
php app/console doctrine:migrations:migrate # Execute missed migrations safely
```