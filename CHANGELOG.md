# Changelog

## [1.1.0] - 2026-07-27

### Added

- `MediaTenantResolver` 換入點的測試覆蓋。這個 contract 同時掌管存取控制
  (`canAccessManager`)與租戶隔離(`currentCompanyId`),真正的實作由宿主
  透過 `config('media-manager.tenant_resolver')` 提供,但先前完全沒有測試 ——
  binding 若沒真的委派給宿主實作,宿主的權限判斷就形同虛設。
  新測試涵蓋:預設 fail closed、依 config 解析實作、導覽列閘門確實委派、
  租戶 id 透傳。

## [1.0.0] - 2026-07-27

### Changed

- 首個穩定版。此後遵循
  [SEMVER.md](https://github.com/lalalili/.github/blob/main/SEMVER.md)
  定義的 public API 契約,宿主可安全使用 `^1.0` 約束。
- 對其他 lalalili 套件的約束一律收斂為 `^1.0`,取代先前 `^0.x`
  與多段 OR 的寫法。
- `repositories` 改用 GitHub VCS,不再依賴宿主 `packages/` 底下的
  兄弟目錄;測試資源改從 `vendor/lalalili/*` 讀取。
- 移除 `minimum-stability` / `prefer-stable` 宣告,授權統一為 MIT。

### 為什麼是 1.0.0

Composer 對 `^0.1.1` 的解讀是 `>=0.1.1 <0.2.0`,0.x 期間每發一個 minor
都需要所有宿主手動改 `composer.json`,否則 `composer update` 永遠拿不到
新版。本套件生態曾因此讓宿主停在數十個 commit 之前而無人察覺。

## 0.1.0

- Added default File Manager page, package tests, and CI.
- Changed default manager access to deny access unless the host app configures a resolver.
- Removed default company/user ids from the root folder command.
