# Multi-level roles

Role level semantics are consistent across departments:

- Level 1: department head; all department capabilities including sensitive approval, deletion and assignment operations.
- Level 2: senior operator; daily management capabilities, excluding critical access assignment and destructive platform operations.
- Level 3: operator; view and limited create/update capabilities required for routine work.

Scopes are `platform`, `website`, `academy`, `branch`, and `self`. A permission grants an action, while domain services continue to restrict records to the role scope.

Risk levels are: 1 read, 2 routine write, 3 management, 4 approval/financial/access assignment, and 5 destructive, deployment or security-critical.

Existing role names remain supported as compatibility roles. New departmental roles use the suffixes `_l1`, `_l2`, and `_l3`.
