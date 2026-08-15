# Access control model

The application uses capability-based RBAC:

- `access_system_permissions` contains atomic capabilities such as `terms.update` and `finance.installments.pay`.
- `access_system_roles` contains the 19 platform, academy and branch roles.
- `access_system_role_permissions` grants capabilities to roles.
- `user_roles` assigns one or more roles to a user.
- `user_permissions` grants exceptional direct capabilities to a user, optionally with an expiry time.
- `access_system_setting_permissions` maps an individual `z_settings.setting_id` to the capability required to change it.

Effective access is the union of active, unexpired direct permissions and active, unexpired role permissions. Record scope (own resource, academy or branch) must still be enforced by the corresponding domain service.

`access_system_setting_permissions` is intentionally retained. The current appearance settings are mapped to `settings.appearance.manage`, allowing safe delegation of appearance management without granting all site settings. If field-level settings authorization is permanently abandoned, this table can be removed; while the current controller uses it, it must not be deleted.

Role and permission labels are stored in `f_translations` for both `fa` and `en`, with `title`, `summary` and `description` fields.
