<?php

// ── Core Role Check ───────────────────────────────────────────────────────

function has_role(string $role, string $targetRole): bool {
  // return strhas($role, "|$targetRole|");
  return strhas($role, "$targetRole");
}

function get_current_role(): string {
  return session_get('role', '');
}


// ── Role Checkers ─────────────────────────────────────────────────────────

function isGuest(): bool {
  return getUserId() === 0;
}

function isUser(): bool {
  return !isGuest();
}

function isSuperAdmin(): bool {
  return isGuest() ? false : has_role(get_current_role(), 'superadmin');
}

function isAdmin(): bool {
  return isGuest() ? false : (
    has_role(get_current_role(), 'admin') || isSuperAdmin()
  );
}

function isAcademyOwner(): bool {
  return isGuest() ? false : (
    has_role(get_current_role(), 'academy_owner') || has_role(get_current_role(), 'academy_branch_owner')
  );
}

function isManager(): bool {
  return isGuest() ? false : (
    has_role(get_current_role(), 'academy_manager' ) || has_role(get_current_role(), 'academy_owner')
  );
}

// function isManager(): bool {
//   return isGuest() ? false : (
//     has_role(get_current_role(), 'manager1') || has_role(get_current_role(), 'manager2')
//   );
// }

// function isManager1(): bool {
//   return isGuest() ? false : has_role(get_current_role(), 'manager1');
// }

// function isManager2(): bool {
//   return isGuest() ? false : has_role(get_current_role(), 'manager2');
// }

function isReceptor(): bool {
  return isGuest() ? false : (has_role(get_current_role(), 'receptionist'));
}

// function isReceptor(): bool {
//   return isGuest() ? false : (
//     has_role(get_current_role(), 'receptor1') || has_role(get_current_role(), 'receptor2')
//   );
// }

// function isReceptor1(): bool {
//   return isGuest() ? false : has_role(get_current_role(), 'receptor1');
// }

// function isReceptor2(): bool {
//   return isGuest() ? false : has_role(get_current_role(), 'receptor2');
// }

function isTeacher(): bool {
  return isGuest() ? false : (
    has_role(get_current_role(), 'teacher1') || has_role(get_current_role(), 'teacher2')
  );
}

function isTeacher1(): bool {
  return isGuest() ? false : has_role(get_current_role(), 'teacher1');
}

function isTeacher2(): bool {
  return isGuest() ? false : has_role(get_current_role(), 'teacher2');
}

function isAuthor(): bool {
  return isGuest() ? false : (
    has_role(get_current_role(), 'author') || isVipAuthor()
  );
}

function isVipAuthor(): bool {
  return isGuest() ? false : has_role(get_current_role(), 'vipauthor');
}

function isVip(): bool {
  return isGuest() ? false : has_role(get_current_role(), 'vip');
}


// ── Negations ─────────────────────────────────────────────────────────────

function isNotUser(): bool      { return !isUser(); }
function isNotAdmin(): bool     { return !isAdmin(); }
function isNotSuperAdmin(): bool{ return !isSuperAdmin(); }
function isNotManager(): bool   { return !isManager(); }
function isNotReceptor(): bool  { return !isReceptor(); }
function isNotTeacher(): bool   { return !isTeacher(); }
function isNotAcademyOwner(): bool   { return !isAcademyOwner(); }


// ── Role Name (نمایشی) ────────────────────────────────────────────────────

function get_role_name(): string {
  if (isGuest()) return 'میهمان';

  $parts = [];

  if (isSuperAdmin())       $parts[] = 'مدیر کل';
  elseif (isAdmin())        $parts[] = 'مدیر';

  // if (isManager1())         $parts[] = 'مدیر آموزشگاه ارشد';
  // elseif (isManager2())     $parts[] = 'مدیر آموزشگاه';

  // if (isReceptor1())        $parts[] = 'منشی ارشد';
  // elseif (isReceptor2())    $parts[] = 'منشی';

  if (isTeacher1())         $parts[] = 'استاد ارشد';
  elseif (isTeacher2())     $parts[] = 'استاد';

  if (isVipAuthor())        $parts[] = 'نویسنده ویژه';
  elseif (isAuthor())       $parts[] = 'نویسنده';

  if (isVip())              $parts[] = 'عضو ویژه';
  elseif (isUser())         $parts[] = 'کاربر عادی';

  return implode('، ', $parts);
}


// ── Gate: توقف اجرا اگه مجوز نداشت ──────────────────────────────────────
// این توابع برای استفاده مستقیم در Controller‌های قدیمیه
// در کد جدید از $this->requireRole() در BaseController استفاده کن

function _deny(string $message = 'Forbidden'): never {
  $isApi = str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');
  if ($isApi) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $message]);
  } else {
    http_response_code(403);
    echo $message;
  }
  exit;
}

function grantUser(): void          { if (isGuest())        _deny(); }
function grantAdmin(): void         { if (isNotAdmin())     _deny(); }
function grantSuperAdmin(): void    { if (!isSuperAdmin())  _deny(); }
function grantPanel(): void         { if (isNotUser())      _deny(); }
function grantAuthor(): void        { if (!isAuthor())      _deny(); }
function grantVipAuthor(): void     { if (!isVipAuthor())   _deny(); }
function grantAcademyManager(): void  { if (isNotManager())  _deny(); }

function grantAdminPanel(): void {
  if (isNotAdmin() && isNotManager()) _deny();
}

function grantAcademyManaging(): void {
  if (isNotManager() && isNotReceptor()) _deny();
}
