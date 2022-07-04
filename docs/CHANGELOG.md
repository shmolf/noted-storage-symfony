## [4.1.0](https://github.com/shmolf/noted-storage-symfony/compare/v4.0.0...v4.1.0) (2022-07-04)


### Features

* list oauth tokens ([70a7d00](https://github.com/shmolf/noted-storage-symfony/commit/70a7d00f0d381ef109e1aa3f06c59527271e8c79))


### Bug Fixes

* remove unusable method ([af94141](https://github.com/shmolf/noted-storage-symfony/commit/af9414153436585191ca950023e4187c1f232a54))

## [4.0.0](https://github.com/shmolf/noted-storage-symfony/compare/v3.1.0...v4.0.0) (2022-07-03)


### ⚠ BREAKING CHANGES

* CHANGES
- is supposed to allow a Note API to support multiple Note Rendering sites

### Features

* support multi-host applications ([10c975a](https://github.com/shmolf/noted-storage-symfony/commit/10c975a5e4d2500d8e82566da032de53aa1e13e8))


### Bug Fixes

* **tsconfig:** exclude the vendor dir ([d736685](https://github.com/shmolf/noted-storage-symfony/commit/d7366853850cdfc440daa29aa7e40bac33466c44))
* **lint:** resolve some linting errors ([773f50d](https://github.com/shmolf/noted-storage-symfony/commit/773f50dd1cb9823a85e3a1884b708f2a057db68c))
* **workflow:** update php version ([fec8773](https://github.com/shmolf/noted-storage-symfony/commit/fec87733953c40d888c8f3cfa53a91ea6eb1305b))

## [3.1.0](https://github.com/shmolf/noted-storage-symfony/compare/v3.0.3...v3.1.0) (2022-05-01)


### Features

* **flex:** update symfony flex dependency ([53ce190](https://github.com/shmolf/noted-storage-symfony/commit/53ce1908984c7238ec609cba95fc674ab236fd05))

### [3.0.3](https://github.com/shmolf/noted-storage-symfony/compare/v3.0.2...v3.0.3) (2021-08-08)


### Bug Fixes

* **cors:** add 'delete' to allowable headers ([c3f9710](https://github.com/shmolf/noted-storage-symfony/commit/c3f9710f92314b4d030fb529b71af30346683fcd))

### [3.0.2](https://github.com/shmolf/noted-storage-symfony/compare/v3.0.1...v3.0.2) (2021-08-06)


### Bug Fixes

* **note:** new notes need empty strings for content and title ([8b52651](https://github.com/shmolf/noted-storage-symfony/commit/8b52651b1f471448f78318a38b15965d360158b1))

### [3.0.1](https://github.com/shmolf/noted-storage-symfony/compare/v3.0.0...v3.0.1) (2021-08-06)


### Bug Fixes

* **apache:** needed apache dependency ([7a793e9](https://github.com/shmolf/noted-storage-symfony/commit/7a793e9861438e41ce4805c7a8fa7f5e95d68960))

## [3.0.0](https://github.com/shmolf/noted-storage-symfony/compare/v2.0.0...v3.0.0) (2021-08-06)


### ⚠ BREAKING CHANGES

* **token:** new refresh/access token & hopefully working oauth page
* **token:** replace apptoken association with user
* **routes:** reorganize some routes (oauth vs apptoken)

### Features

* **codespaces:** add codespace container config ([49dd155](https://github.com/shmolf/noted-storage-symfony/commit/49dd155e04b8f166a0d7a121d6a6e86abeb6dd64))
* **oauth|refresh:** add method for creating new refresh tokken ([2d7842c](https://github.com/shmolf/noted-storage-symfony/commit/2d7842c084d264a7c94fe823e79bbaf8d98a0ac7))
* **routes:** add note-related routes ([f76f933](https://github.com/shmolf/noted-storage-symfony/commit/f76f93374b160fdb885181601d62399ebc596c62))
* **oauth:** add refresh & access token entities ([1c9a7f5](https://github.com/shmolf/noted-storage-symfony/commit/1c9a7f5d2d8f8e64e324776dd414702afaf8a3ce))
* **oauth:** add register function ([a7262ec](https://github.com/shmolf/noted-storage-symfony/commit/a7262ec7f799b1757f762671a23630bb54b62243))
* **register:** autoclose oauth login on success ([045657c](https://github.com/shmolf/noted-storage-symfony/commit/045657c034e6eafeeb5295213c0457c968c749b1))
* **token authentication:** cleanup irrelev exceptions & start on access token ([9b6ed4d](https://github.com/shmolf/noted-storage-symfony/commit/9b6ed4dbf5f3ce02aa763add0872596f3b319664))
* **docker:** consolidate configs into DockerFiles ([d76b329](https://github.com/shmolf/noted-storage-symfony/commit/d76b329cc04fd9a7ced9ecbc40614e355fff8447))
* **oauth|apptoken:** did a couple of things ([bc08d17](https://github.com/shmolf/noted-storage-symfony/commit/bc08d17dba3e1a80e2627974f4a13d2164766b44))
* **refresh-token:** extend lifetime ([deba952](https://github.com/shmolf/noted-storage-symfony/commit/deba952a5b1d482a7f4d76be61b7e57b7819c70e))
* **cors|refresh:** getting bloody close to being able to refresh the token ([9a9176e](https://github.com/shmolf/noted-storage-symfony/commit/9a9176e260092bd883cd21af6f9c399e167aea9e))
* **routes:** move the oauth routes around to new config file ([7507ea8](https://github.com/shmolf/noted-storage-symfony/commit/7507ea8197b50dffd55edb9501f0ccc4761e0d33))
* **token:** new refresh/access token & hopefully working oauth page ([8d2fe96](https://github.com/shmolf/noted-storage-symfony/commit/8d2fe960b68f21b7e47cae99cbe20190eb58c078))
* **oauth:** preliminary registration logic ([2cd1ba1](https://github.com/shmolf/noted-storage-symfony/commit/2cd1ba1c621fc6fe4ac2813d6dc3220b0482772e))
* **oauth|register:** provide more token data in response ([24fd8b9](https://github.com/shmolf/noted-storage-symfony/commit/24fd8b9544c7bc848c62ac21a03371dc99a13f5a))
* **routes:** re-organize API routes ([1481163](https://github.com/shmolf/noted-storage-symfony/commit/1481163e0fc0f9e771e1b71a92914bf4565817a5))
* **routes:** reorganize some routes (oauth vs apptoken) ([8fcd1e7](https://github.com/shmolf/noted-storage-symfony/commit/8fcd1e74568bb79fa36f309d9525d0881e56c799))
* **token:** replace apptoken association with user ([741b9cb](https://github.com/shmolf/noted-storage-symfony/commit/741b9cb7b984aa79531c17da99322edae37959be))
* **note|controller:** sure-up some logic & eliminate `clientuuid` refs ([707b470](https://github.com/shmolf/noted-storage-symfony/commit/707b4704e23a0c15429548d339a9476300156d4c))
* **codespaces:** use predefined container config ([89c7338](https://github.com/shmolf/noted-storage-symfony/commit/89c733800b85bc5bfc3e944d2c8d9bff66a5162c))


### Bug Fixes

* address linting error by changing prop init ([0c98470](https://github.com/shmolf/noted-storage-symfony/commit/0c9847081b224683b43a555b8abfe91730563f61))
* **readme:** badge link to point to this repo ([0c2996c](https://github.com/shmolf/noted-storage-symfony/commit/0c2996c37e99084faacb89f949f99c6ccbcbab2f))
* **entity:** cleanup `userId` refs to `user` ([0f0eb36](https://github.com/shmolf/noted-storage-symfony/commit/0f0eb360222a563796a496640f12a6d646e9d914))
* correct `userId` ref & add migration ([55d754d](https://github.com/shmolf/noted-storage-symfony/commit/55d754d2c0c650c081d2333d1ca6fd00ed40d50c))
* **psalm|lint:** enhance psalm config & restore/fix code ([f24bf36](https://github.com/shmolf/noted-storage-symfony/commit/f24bf36221bdf22330193d919641392e4d57ad63))
* **oauth|tokens:** format expiration date ([0534607](https://github.com/shmolf/noted-storage-symfony/commit/0534607c52c4341f909eacd800815ac579922c0e))
* **token:** improve AppToken validation ([ec5fac9](https://github.com/shmolf/noted-storage-symfony/commit/ec5fac9672c4263541c89009653ca8c2d2b5bb51))
* **oauth|refresh-tokens|access-tokens:** jfc, finally got some form of oauth login working ([7ef92fe](https://github.com/shmolf/noted-storage-symfony/commit/7ef92fefa77bd6db27ca40e8e56e38cddf2df53d))
* **composer:** license field ([c482155](https://github.com/shmolf/noted-storage-symfony/commit/c482155cd156e8e3d93a28b6e86dde2596bd74c9))
* **NoteTag:** name property should be in `main` group, and null-coallesce default ([8e6842f](https://github.com/shmolf/noted-storage-symfony/commit/8e6842f015171b44fea5110cf92ffda30983af86))
* **oauth:** need to communicate to parent that it's ready for the pipe ([e0ebd47](https://github.com/shmolf/noted-storage-symfony/commit/e0ebd474b15ff6d8bb617b21ffd7af57f221297c))
* **routes:** oauth register pointed to wrong controller ([80e1803](https://github.com/shmolf/noted-storage-symfony/commit/80e18034d1942439971de0f5ed21c8d7d752be63))
* **repo:** refer to recently updated method sig ([b34bb82](https://github.com/shmolf/noted-storage-symfony/commit/b34bb82b85660508d870223c2330b280b5646e88))
* **oauth:** registration bugs ([204eac8](https://github.com/shmolf/noted-storage-symfony/commit/204eac861ea92cbcaf14b17f4e5d745261a9b8c5))
* remove debug lines ([2e485aa](https://github.com/shmolf/noted-storage-symfony/commit/2e485aaffe0d23d1860de0c22851f0cd55836981))
* **oauth:** remove some stale code ([83377a9](https://github.com/shmolf/noted-storage-symfony/commit/83377a929c2d38da7143e423d583a58f4acfd267))
* **oauth|register:** remove unused accessToken field ([ee2b5ae](https://github.com/shmolf/noted-storage-symfony/commit/ee2b5ae722547518b966e6bff2bd891304a28878))
* **security:** reorganize routes and auth requirements ([9a75895](https://github.com/shmolf/noted-storage-symfony/commit/9a75895b01010ef66339f191ec6b4f3560588ca8))
* update param types ([2f3d3cb](https://github.com/shmolf/noted-storage-symfony/commit/2f3d3cbf26c95dea03ab3f160bea2e1d6decce30))
* **oauth|register:** update refresh uri to be absolute url ([b041f48](https://github.com/shmolf/noted-storage-symfony/commit/b041f48117f62a8458895f16a9e33dfe235ea657))
* **token authentication:** update to use Access over App tokens ([7f2584c](https://github.com/shmolf/noted-storage-symfony/commit/7f2584cdef96aa34595211551d02e4df6fa83524))


### Reverts

* **codespaces:** delete devcontainer config ([2e05563](https://github.com/shmolf/noted-storage-symfony/commit/2e05563ecd47ff1e4d4a5a61303c0fa82150a565))

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
