## [2.0.0](https://github.com/shmolf/noted-storage-symfony/compare/v1.0.0...v2.0.0) (2021-07-03)


### ⚠ BREAKING CHANGES

* alter property of entities `NoteTag` and `MarkdownNote`
to `user` rather than `userId`
* **app tokens:** add ability to create an manage app tokens
* lots of FE & fixed Encore w/ yarn berry

### Features

* **app tokens:** add ability to create an manage app tokens ([26335da](https://github.com/shmolf/noted-storage-symfony/commit/26335da7805e5535e9130777355abbbd32112847))
* **docker:** add configs for docker containers ([46f3b32](https://github.com/shmolf/noted-storage-symfony/commit/46f3b32538f94eaca73221a124115f4b01a3d82f))
* **fe:** add dependencies for page rendering ([7e337c3](https://github.com/shmolf/noted-storage-symfony/commit/7e337c3068deec5972363b238b98cd014babd015))
* add missing dependencies & configs ([a12b688](https://github.com/shmolf/noted-storage-symfony/commit/a12b688d6e9c9287587cebfc4fcd86a2f71c998c))
* **user:** create user entity & update security config ([0250880](https://github.com/shmolf/noted-storage-symfony/commit/0250880749aa080e447dbd218c79bd3f1d19034d))
* import code from note'd app ([fa4329c](https://github.com/shmolf/noted-storage-symfony/commit/fa4329ce6d82aafbdef49751d80ee048d0072bb1))
* import user creation/login/editting from FE app ([63b5fc9](https://github.com/shmolf/noted-storage-symfony/commit/63b5fc9c361af37cfd19f531109d7957c1d1964a))
* **doctrine:** initial setup ([3c098de](https://github.com/shmolf/noted-storage-symfony/commit/3c098de0153bffd9914a77aa91b600a6219fc3b2))
* new security service & controller ([51ce526](https://github.com/shmolf/noted-storage-symfony/commit/51ce526af66e8bf434a29ce63d6bf9fec4ccb200))
* **oauth:** outh login & token creation ([f3ec293](https://github.com/shmolf/noted-storage-symfony/commit/f3ec29307ee65d20f9179a8b325a3f11b320ab78))
* successsful authentication with appToken ([d0b4f86](https://github.com/shmolf/noted-storage-symfony/commit/d0b4f86783ef55883db71675fd1004109fb183dc))
* **user:** support for user edit & improved security ([f1c7470](https://github.com/shmolf/noted-storage-symfony/commit/f1c7470f1ae494f49140c08c5c606aa04d1da76b))


### Bug Fixes

* 🔪 package-lock ([a4352b1](https://github.com/shmolf/noted-storage-symfony/commit/a4352b1ce78f4076ce3a235cef6ab23a60949227))
* **gitignore:** add exclusion for package-lock ([668f722](https://github.com/shmolf/noted-storage-symfony/commit/668f7227c0f6809fd7eac6d4ee4871b1923e4f19))
* **docker:** conoslidate & correct configs ([a333dfd](https://github.com/shmolf/noted-storage-symfony/commit/a333dfda9aff79851c80d2d3e41055830deca9fd))
* **docker:** container dependencies ([c66d012](https://github.com/shmolf/noted-storage-symfony/commit/c66d012dafec942e69c1bb3f2eb9686a8d6152e9))
* **docker:** doctrine, nginx, and php all loading well ([c345cf0](https://github.com/shmolf/noted-storage-symfony/commit/c345cf017881f613d9d08305ed8222d511046b01))
* **env:** fix DB vars for Symfony ENV example ([27ea26e](https://github.com/shmolf/noted-storage-symfony/commit/27ea26e29d80f853092d2269975abcd6a4a61dc1))
* fix many errors identified from linting ([5000a9f](https://github.com/shmolf/noted-storage-symfony/commit/5000a9f4cef3ff11e270168b263ccdd54daa5860))
* **docker|docrine:** incremental improvement towards doctrine connection ([3da486b](https://github.com/shmolf/noted-storage-symfony/commit/3da486b11130d577f61e2ae9771063fddd7fd385))
* **docker:** replace `npm` refs with `npx yarn` ([e7ae04b](https://github.com/shmolf/noted-storage-symfony/commit/e7ae04b859a44638031655736a6e5498fa81fc86))
* **composer:** update package version requirement & autoloader optimization ([b4736b3](https://github.com/shmolf/noted-storage-symfony/commit/b4736b306f6dcfb6974fe4a34106352378ddaec8))

## 1.0.0 (2021-05-31)


### Features

* **codesniffer:** add codesniffer support ([1b5bae6](https://github.com/shmolf/noted-storage-symfony/commit/1b5bae6ba13dd6580f2280a84e64f678efb563dc))
* **husky:** add support for husky hooks ([1955c52](https://github.com/shmolf/noted-storage-symfony/commit/1955c52940f0456082beffb256f76025bddeed27))
* **phpunit:** add support for php testing ([17066e3](https://github.com/shmolf/noted-storage-symfony/commit/17066e3df7dda401e79b8f7952c35117348ae5a9))
* **psalm:** add support for psalm code analysis ([0408b9b](https://github.com/shmolf/noted-storage-symfony/commit/0408b9b82ae710b7c3b1f0f17e4a55e1d0d19899))
* **symfony:** init symfony app ([4c9cd58](https://github.com/shmolf/noted-storage-symfony/commit/4c9cd58ed70e5d5f045afe234aee6a66d87fde88))
* **actions:** php workflow ([aa70057](https://github.com/shmolf/noted-storage-symfony/commit/aa700577aa7bbb2ceed0a333b846ee9ccefcff5b))


### Bug Fixes

* add back dev dep ([d223c64](https://github.com/shmolf/noted-storage-symfony/commit/d223c643375531714c884ea5f5668507f08bfcba))
* **composer:** create lock file ([c6f22f5](https://github.com/shmolf/noted-storage-symfony/commit/c6f22f5bb2fe9992078e8abc92bdd5f213fc19c6))
* fix dev dependencies ([dd07f77](https://github.com/shmolf/noted-storage-symfony/commit/dd07f77a958a6869e4943e92f311ffd8a42fcbfc))
* install missing dev dependency ([03fd5a7](https://github.com/shmolf/noted-storage-symfony/commit/03fd5a73898a656ef3efad679a2cb0019e011979))
* move dependency to dev list ([9f7b5da](https://github.com/shmolf/noted-storage-symfony/commit/9f7b5da5da823a9fc0212c7b438ce0bd86a73437))
* rebuild yarn pnp ([53c59f9](https://github.com/shmolf/noted-storage-symfony/commit/53c59f98a6699dc82c2d16a54a704e82cb592bb2))
* reference proper preset in releaserc & remove dead dep ([35f7740](https://github.com/shmolf/noted-storage-symfony/commit/35f7740f1972ba6ad8d683f727fdc62cca758c69))
* **composer:** remove tests from `all-check` till tests exist ([9a5f8af](https://github.com/shmolf/noted-storage-symfony/commit/9a5f8af1ad7705a30bdbbd7184e06cc40ae54c6b))
