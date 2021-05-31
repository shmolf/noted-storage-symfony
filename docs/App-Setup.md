# Summary
These instructions are for Ubuntu 18.04, but past experience says that these will be identical, or nearly
the same for Ubuntu 18.10 or 20.X.

In case you prefer to setup this application from the ground up yourself, here's the steps I've taken.

Before starting, don't bother updating `composer.json`, since that'll be replaced as part of the [Init Symfony](#init-symfony) step.

# Environment Setup
## PHP 7.4 - Only run if PHP 7.4 (or higher) isn't already installed
[Site resource](https://www.cloudbooklet.com/upgrade-php-version-to-php-7-4-on-ubuntu/)

Add a new repo that houses the `php7.4` libraries.
```bash
sudo apt install -y software-properties-common && sudo add-apt-repository ppa:ondrej/php
```

```bash
sudo apt update &&\
sudo apt install -y php7.4 &&\
sudo apt install -y php7.4-common php7.4-mysql php7.4-xml php7.4-xmlrpc php7.4-curl &&\
sudo apt install -y php7.4-gd php7.4-imagick php7.4-cli php7.4-dev php7.4-imap &&\
sudo apt install -y php7.4-mbstring php7.4-opcache php7.4-soap php7.4-zip php7.4-intl &&\
```
I needed to disable `php7.2`, and enabled `php7.4`, so please adjust this command as suits your Environment.
```bash
sudo a2dismod php7.2 && sudo a2enmod php7.4 && sudo service apache2 restart
```

## SimpleXML required for CodeSniffer
```bash
sudo apt install -y php-xml
```

# Steps
## Clone
I keep each application as a directory within `/var/www`. Please adjust this path to your setup.
```bash
cd /var/www && git clone git@github.com:shmolf/noted-storage-symfony.git
```

Allow the `www-data` group access to the directory, and adjust permission (`chmod`) as you need.
```bash
chown <your username>:www-data noted-storage-symfony/
```
```bash
chmod <your criteria> noted-storage-symfony/
```

# Application Setup
## Init Symfony
This'll initialize the symfony files into a `sym` subdirectory. We cannot use the current directory,
since it's technically not empty.
```bash
symfony new --version=lts ./sym
```

Using one of the strategies from [Baeldung's article](https://www.baeldung.com/linux/move-files-hidden-parent), we'll
move the files from this subdirectory, to the current.
```bash
mv ./sym/{.,}* ./
```

Feel free to confirm everything was moved. Ignore the `.git` directory, since we don't need to copy that over.
If satisfied, remove the `sym` directory.
```bash
rm -rf sym
```

Go ahead and commit, since this is a good foundational point.

## Add CodeSniffer
```bash
composer require --dev "squizlabs/php_codesniffer"
```
> Do you want to execute this recipe?
`y`

Create `phpcs.xml.dist` file with:
<details>
<summary>This configuration</summary>

For the official config example, please see the
[squizlabs repo](https://github.com/squizlabs/PHP_CodeSniffer/blob/master/phpcs.xml.dist).

```xml
<?xml version="1.0" encoding="UTF-8"?>

<ruleset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/squizlabs/php_codesniffer/phpcs.xsd">

    <arg name="basepath" value="."/>
    <arg name="cache" value=".phpcs-cache"/>
    <arg name="colors"/>
    <arg name="extensions" value="php"/>

    <rule ref="PSR12"/>

    <file>config</file>
    <file>src</file>
    <file>tests</file>

</ruleset>
```
</details>

And, append to `composer.json`'s `scripts` section
<details>
<summary>Changes diff</summary>

```diff
    "scripts": {
        ...
+       "lint": [
+           "@lint-php"
+       ],
+       "lint-php": [
+           "phpcs ./phpcs.xml.dist"
+       ]
    },
```
</details>

This way, we can run the command, `composer lint`, and it'll run the sub-command `lint-php`, in addition
to any other commands we might add later.

## Add Psalm
```bash
composer require --dev vimeo/psalm psalm/plugin-symfony && \
vendor/bin/psalm-plugin enable psalm/plugin-symfony && \
./vendor/bin/psalm --init && \
./vendor/bin/psalm
```

Append to `composer.json`'s `scripts` section
<details>
<summary>Changes diff</summary>

Since there's now Linting, and Code analysis, let's add an all-encompassing script as well. `all-checks`

```diff
    "scripts": {
        ...
+       "analysis" : [
+           "@psalm"
+       ],
+       "psalm": "psalm",
+       "all-checks": [
+           "@lint",
+           "@analysis"
+       ]
    },
```
</details>

## Add PHPUnit
```bash
composer require --dev phpunit/phpunit
```

Append to `composer.json`'s `scripts` section
<details>
<summary>Changes diff</summary>

```diff
    "scripts": {
        ...
+       "test": "phpunit",
        "all-checks": [
            "@lint",
            "@analysis",
+           "test"
        ]
    },
```
</details>

## Add Semantic Release

To add support for [Semantic Release](https://github.com/semantic-release/semantic-release), there's a number
of things we'd want to install/configure.
1. To accomplish all of this, you'll need [Yarn](https://yarnpkg.com/) installed globally.
   1. This setup will rely [yarn berry](https://yarnpkg.com/getting-started/install#per-project-install), installed
      through yarn.
       - The benefit, is that clones of the repo, don't need yarn installed, since the binary is already included.
1. We need to be sure only approved commit messages are being used. To accomplist this, we'll setup
   [CommitLint](https://github.com/conventional-changelog/commitlint)
1. To have changelogs, tagging, and possibly more handled as part of the Continuous Integration (CI) process,
   we'll setup the [Semantic Release](https://github.com/semantic-release/semantic-release) library.
1. We'll use [Husky](https://typicode.github.io/husky/#/) to manage the hook for CommitLint

### Yarn Berry
```bash
yarn set version berry
```
Next, we'll want to go ahead and update the `.gitignore` with the
[recommended changes](https://yarnpkg.com/getting-started/qa#which-files-should-be-gitignored).
<details>
<summary>List of files for Zero-Installs</summary>

```
.yarn/*
!.yarn/cache
!.yarn/patches
!.yarn/plugins
!.yarn/releases
!.yarn/sdks
!.yarn/versions
```
</details>

**Note:** If at some point, you get an error while trying to commit, run the yarn rebuild command, `npx yarn rebuild`.
<details>
<summary>Error Example</summary>

```
Internal Error: Assertion failed: Expected workspace to have been resolved
    at ie.refreshWorkspaceDependencies (/var/www/noted-storage-symfony/.yarn/releases/yarn-berry.cjs:2:355538)
    at ie.restoreInstallState (/var/www/noted-storage-symfony/.yarn/releases/yarn-berry.cjs:2:383145)
    at processTicksAndRejections (internal/process/task_queues.js:95:5)
    at async Ye.execute (/var/www/noted-storage-symfony/.yarn/releases/yarn-berry.cjs:2:108919)
    at async Ye.validateAndExecute (/var/www/noted-storage-symfony/.yarn/releases/yarn-berry.cjs:2:660570)
    at async Y.run (/var/www/noted-storage-symfony/.yarn/releases/yarn-berry.cjs:17:3854)
    at async ne.execute (/var/www/noted-storage-symfony/.yarn/releases/yarn-berry.cjs:2:58296)
    at async ne.validateAndExecute (/var/www/noted-storage-symfony/.yarn/releases/yarn-berry.cjs:2:660570)
    at async Y.run (/var/www/noted-storage-symfony/.yarn/releases/yarn-berry.cjs:17:3854)
    at async Y.runExit (/var/www/noted-storage-symfony/.yarn/releases/yarn-berry.cjs:17:4021)
husky - commit-msg hook exited with code 1 (error)
```
</details>

### Init `package.json`
```bash
npm init
```
Just keep pressing enter. Feel free to fill the Author field, when that question comes up.  
Regardless, we'll want to make some tweaks to the `package.json`, after it's generated.

<details>
<summary>Diff of changes</summary>

```diff
  {
    "name": "noted-storage-symfony",
    "version": "1.0.0",
    "description": "Dev environment for package",
-   "main": "index.js",
    "directories": {
-      "doc": "docs",
-      "test": "tests"
+      "doc": "docs"
    },
    "scripts": {
-     "test": "echo \"Error: no test specified\" && exit 1"
+     "semantic-release": "semantic-release"
    },
    "repository": {
      "type": "git",
      "url": "git+https://github.com/shmolf/noted-storage-symfony.git"
    },
    "author": "Nicholas Browning",
    "license": "MIT",
    "bugs": {
      "url": "https://github.com/shmolf/noted-storage-symfony/issues"
    },
    "homepage": "https://github.com/shmolf/noted-storage-symfony#readme"
  }
```
</details>

### Install Dependencies

```bash
npx yarn add -D husky @commitlint/{config-conventional,cli} semantic-release @semantic-release/{git,changelog}
```
1. `husky` - This gets us Husky-managed git hooks
1. `@commitlint/{config-conventional,cli}` - This gets us the CommitLint, which'll verify our commit messages meet the standard
1. `semantic-release` - This is the core of Semantic Release
1. `@semantic-release/{git,changelog}` - Two plugins. One for generating commits as part of release (changelog file),
   and the other for generating the changelog file.

### Configure CommitLint

<details>
<summary>`commitlint.config.js`</summary>

Since this project is opting to use the [Conventional Commits convention](https://www.conventionalcommits.org/en/v1.0.0/),
that's the library we installed. And therefor, the library we're referencing in the configuration.
```bash
echo "module.exports = {extends: ['@commitlint/config-conventional']};" > commitlint.config.js
```
</details>

### Configure Husky
```bash
npx husky install && npx husky add .husky/commit-msg 'npx --no-install yarn commitlint --edit "$1"'
```

Feel free to test out that commit lint works, by committing something with a poorly formatted commit message.  
Something like `test - of the worst nature`.

If for whatever reason, the commit goes through (incomplete setup), then 'uncommit' with `git reset HEAD^`.

### Configure Semantic Release

<details>
<summary>`.releaserc.json`</summary>

```json
{
  "branches": ["main"],
  "plugins": [
    ["@semantic-release/commit-analyzer", {
      "preset": "conventionalchangelog",
      "parserOpts": {
        "noteKeywords": ["BREAKING CHANGE", "BREAKING CHANGES", "BREAKING"]
      }
    }],
    ["@semantic-release/release-notes-generator", {
      "preset": "conventionalchangelog",
      "parserOpts": {
        "noteKeywords": ["BREAKING CHANGE", "BREAKING CHANGES", "BREAKING"]
      },
      "writerOpts": {
        "commitsSort": ["subject", "scope"]
      }
    }],
    [
      "@semantic-release/changelog",
      {
        "changelogFile": "docs/CHANGELOG.md"
      }
    ],
    "@semantic-release/github",
    [
      "@semantic-release/git",
      {
        "assets": ["docs/CHANGELOG.md"]
      }
    ]
  ]
}

```
</details>

#### Setup CI for Semantic Release Support
Because we're committing to the repo as part of semantic release, you need to setup a Personal Access token.
1. [Github URL](https://github.com/settings/tokens/new)
1. Select `write:packages`
   - This will auto-select `read:packages` and `repo:*`
1. Provide a name for the token at the top, and click 'Generate Token' at the bottom
   - I opted for a name like `Noted-Storage-Symfony-CI`, so I could discern where and how it was being used
1. Make sure you copy the token string. You'll need that in the repo's CI Variables settings.
1. Go to your Repo's 'New Secret' page: `https://github.com/<your username>/<your repo>/settings/secrets/actions/new`
1. According to the
   [Documentation](https://github.com/semantic-release/semantic-release/blob/master/docs/usage/ci-configuration.md#push-access-to-the-remote-repository),
   the name of the Github Token should explicitly be `GH_TOKEN`.
   - I'm pretty sure I tried `GITHUB_TOKEN` in the past, and it didn't work, but I may have messed up, as I was still learning.

## Finalize Composer/NPM configs

<details>
<summary>`composer.json`</summary>

```diff
  {
+     "name": "shmolf/noted-storage-symfony",
+     "description": "Symfony application for storing Note'd markdown notes",
```
</details>

<details>
<summary>`package.json`</summary>

```diff
  {
-   "name": "noted-storage-symfony",
+   "name": "@shmolf/noted-storage-symfony",
```
</details>
