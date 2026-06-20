<?php

// ── Debug ─────────────────────────────────────────────────────────────────

function dump(mixed $var, bool $return = false): string|null {
  if (is_array($var) || is_object($var)) {
    $out = print_r($var, true);
  } else {
    $out = (string) $var;
  }
  $html = "\n<pre style='direction:ltr;text-align:left'>$out</pre>\n";
  if ($return) return $html;
  echo $html;
  return null;
}

function dd(mixed ...$vars): never {
  foreach ($vars as $var) dump($var);
  exit;
}

function hr(bool $return = false): string|null {
  if ($return) return "<hr>\n";
  echo "<hr>\n";
  return null;
}

function br(bool $return = false): string|null {
  if ($return) return "<br>\n";
  echo "<br>\n";
  return null;
}

function brSpan(bool $return = false): string|null {
  if ($return) return "<span class='br'>\n";
  echo "<span>\n";
  return null;
}


// ── User / Session ────────────────────────────────────────────────────────

function getUserId(): int {
  return (int) session_get('user_id', 0);
}

function getUserName(): string {
  return session_get('fullname', 'Guest');
}

function session_get(string $field, mixed $default = null): mixed {
  return $_SESSION[$field] ?? $default;
}

function session_isset(string $field): bool {
  return isset($_SESSION[$field]);
}

function session_set(string $field, mixed $value): void {
  $_SESSION[$field] = $value;
}

function session_set_if_undefined(string $field, mixed $value): void {
  if (!isset($_SESSION[$field])) {
    $_SESSION[$field] = $value;
  }
}

function session_forget(string $field): void {
  unset($_SESSION[$field]);
}


// ── Request ───────────────────────────────────────────────────────────────

function post(string $field, mixed $default = null): mixed {
  return $_POST[$field] ?? $default;
}

function getRequestUri(): string {
  return $_SERVER['REQUEST_URI'] ?? '/';
}

function getFullUrl(): string {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function baseUrl(): string {
  global $config;
  return $config['app']['base'] ?? '';
}

function fullBaseUrl(): string {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  return $scheme . '://' . $_SERVER['HTTP_HOST'] . baseUrl();
}


// ── Security ──────────────────────────────────────────────────────────────

/**
 * هش رمز عبور — از password_hash استفاده می‌کنه (نه md5)
 */
function encryptPasswordNew(string $password): string {
  return password_hash($password, PASSWORD_BCRYPT);
}

//  old method
function encryptPassword($password){
  global $config;
  return md5($password . $config['salt']);
}


/**
 * بررسی رمز عبور
 */
function verifyPassword(string $password, string $hash): bool {
  // سازگاری با هش‌های قدیمی md5
  global $config;
  if (strlen($hash) === 32) {
    return md5($password . ($config['app']['salt'] ?? '')) === $hash;
  }
  return password_verify($password, $hash);
}

function generateHash(int $length = 32): string {
  $chars  = '2345679acdefghjkmnpqrstuvwxyz';
  $result = '';
  $max    = strlen($chars) - 1;
  for ($i = 0; $i < $length; $i++) {
    $result .= $chars[random_int(0, $max)];
  }
  return $result;
}


// ── String ────────────────────────────────────────────────────────────────

function strhas(string $string, string $search, bool $caseSensitive = false): bool {
  if ($caseSensitive) {
    return str_contains($string, $search);
  }
  return str_contains(strtolower($string), strtolower($search));
}

function twoDigitNumber(int $number): string {
  return $number < 10 ? "0$number" : (string) $number;
}


// ── Date ──────────────────────────────────────────────────────────────────

function getCurrentDateTime(): string {
  return date('Y-m-d H:i:s');
}

function jdate(string $date, string $format = 'Y-m-d'): string {
  $timestamp      = strtotime($date);
  $secondsInDay   = 86400;
  $daysPassed     = (int) floor($timestamp / $secondsInDay) + 1;

  $days  = $daysPassed - 19;
  $month = 11;
  $year  = 1348;

  $daysInMonths = [31,31,31,31,31,31,30,30,30,30,30,29];
  $monthNames   = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور',
                   'مهر','آبان','آذر','دی','بهمن','اسفند'];

  while (true) {
    $idx = $month - 1;
    if ($days > $daysInMonths[$idx]) {
      $days -= $daysInMonths[$idx];
      $month++;
      if ($month === 13) {
        $year++;
        if (($year - 1347) % 4 === 0) $days--;
        $month = 1;
      }
    } else {
      break;
    }
  }

  return str_replace(
    ['Y', 'm', 'd', 'M'],
    [$year, twoDigitNumber($month), twoDigitNumber($days), $monthNames[$month - 1]],
    $format
  );
}


// ── Page Meta ─────────────────────────────────────────────────────────────

function setPageTitle(string $title): void {
  global $config;
  $appTitle = $config['page']['title'] ?? 'App';
  $config['page']['current_title'] = "$appTitle | $title";
}

function getPageTitle(): string {
  global $config;
  return $config['page']['current_title'] ?? $config['page']['title'] ?? 'App';
}

function setNoIndex(): void {
  global $config;
  $config['page']['noindex'] = true;
}

function setNoFollow(): void {
  global $config;
  $config['page']['nofollow'] = true;
}

function getRobotState(): string {
  global $config;
  $parts = [];
  $parts[] = ($config['page']['noindex']  ?? false) ? 'noindex'  : 'index';
  $parts[] = ($config['page']['nofollow'] ?? false) ? 'nofollow' : 'follow';
  return implode(',', $parts);
}


// ── Settings ──────────────────────────────────────────────────────────────

function initializeSettings(): void {
  session_set_if_undefined('viewType', 'grid');
  session_set_if_undefined('theme', 'light');
}


// ── Math / Price ──────────────────────────────────────────────────────────

function computeDiscountedPrice(float $price, float $discount, int $quantity = 1): float {
  return $quantity * ($price - $discount * $price / 100);
}


// ── i18n ──────────────────────────────────────────────────────────────────

function translate(array $settingsArray, string $variableName, string $text = 'title'): string {
  // return $settingsArray[$variableName][$text] ?? '';
  return $settingsArray[$variableName][$text];
  // global $config;
  // $lang = $config['app']['lang'] ?? 'fa';
  // return $settingsArray[$variableName][$text . '_' . $lang] ?? '';
}

function translateStrings(array $usersArray, string $variableName): string {
  global $config;
  $lang = $config['app']['lang'] ?? 'fa';
  return $usersArray[$variableName . '_' . $lang] ?? '';
}


// ── Data Helpers ──────────────────────────────────────────────────────────

function setIndexforDataArray(array $array, string $index): array {
  $data = [];
  foreach ($array as $value) {
    $data[$value[$index]] = $value;
  }
  return $data;
}

// function setVariableNameforDataArray(array $array): array {
//   $data = [];
//   foreach ($array as $value) {
//     $data[$value['variable_name']] = $value;
//   }
//   return $data;
// }

function getFilteredList(array $listArray, string $filterString): array {
  $result = [];
  foreach ($listArray as $value) {
    if (str_contains($value['variable_name'], $filterString)) {
      $result[$value['variable_name']] = $value;
    }
  }
  return $result;
}


// ── Pagination HTML ───────────────────────────────────────────────────────

function pagination(
  string $url,
  int    $showCount,
  string $activeClass,
  string $deactiveClass,
  int    $currentPageIndex,
  int    $pageCount,
  string $jsFunction = null
): string {
  $tag    = $jsFunction ? 'span' : 'a';
  $action = $jsFunction
    ? "onclick=\"$jsFunction(#)\""
    : "href=\"$url/#\"";

  $makeItem = function(int $page, bool $active) use ($tag, $action, $activeClass, $deactiveClass): string {
    $a = str_replace('#', (string) $page, $action);
    $class = $active ? $deactiveClass : $activeClass;
    return "<$tag $a class=\"$class\">$page</$tag>";
  };

  $html = $makeItem(1, false) . '<span>..</span>';

  for ($i = $currentPageIndex - $showCount; $i <= $currentPageIndex + $showCount; $i++) {
    if ($i <= 1 || $i >= $pageCount) continue;
    $html .= $makeItem($i, $i === $currentPageIndex);
  }

  $html .= '<span>..</span>' . $makeItem($pageCount, false);
  return $html;
}


// ── Image Helpers ─────────────────────────────────────────────────────────

function get_article_origin_source(string $imageSource, string $type = 'png'): ?string {
  global $config;
  $lang  = $config['app']['lang'] ?? 'fa';
  $other = $lang === 'fa' ? 'en' : 'fa';

  $primary   = baseUrl() . "/assets/images/articles/origin/{$imageSource}{$lang}.{$type}";
  $secondary = baseUrl() . "/assets/images/articles/origin/{$imageSource}{$other}.{$type}";

  if (file_exists('.' . $primary))   return $primary;
  if (file_exists('.' . $secondary)) return $secondary;
  return null;
}

function get_article_thumbnail_source(string $imageSource, string $type = 'png'): ?string {
  global $config;
  $lang  = $config['app']['lang'] ?? 'fa';
  $other = $lang === 'fa' ? 'en' : 'fa';

  $candidates = [
    baseUrl() . "/assets/images/articles/thumbnails/{$imageSource}{$lang}-300x169.{$type}",
    baseUrl() . "/assets/images/articles/origin/{$imageSource}{$lang}.{$type}",
    baseUrl() . "/assets/images/articles/thumbnails/{$imageSource}{$other}-300x169.{$type}",
    baseUrl() . "/assets/images/articles/origin/{$imageSource}{$other}.{$type}",
  ];

  foreach ($candidates as $src) {
    if (file_exists('.' . $src)) return $src;
  }
  return null;
}


// ── Table HTML ────────────────────────────────────────────────────────────

function showTable(
  array  $tableArray,
  string $tableName,
  array  $settings,
  array  $tableHeadersTitle = [],
  string $tableAction       = 'preview',
  string $id                = null,
  string $tableDeleteUrl    = null,
  string $tableActionUrl    = null
): string {
  ob_start(); ?>

  <div class="backup-list">
    <h3><?= htmlspecialchars($tableName) ?></h3>
    <table class="backup-table">
      <thead>
        <tr>
          <th><input type="checkbox"></th>
          <th>ردیف</th>
          <?php foreach ($tableHeadersTitle as $key => $val): ?>
            <th><?= $val['table_name'] === null ? '' : translate($tableHeadersTitle, $key) ?></th>
          <?php endforeach; ?>
          <th><?= translate($settings, 'tables_action_title') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tableArray as $i => $row): ?>
          <tr>
            <td><input type="checkbox"></td>
            <td><?= $i + 1 ?></td>
            <?php foreach ($row as $val): ?>
              <td><?= htmlspecialchars((string) $val) ?></td>
            <?php endforeach; ?>
            <td class="actions">
              <?php if ($tableAction === 'edit'): ?>
                <a href="<?= baseUrl() . $tableActionUrl . '/' . $row[$id] ?>" class="edit-cat">
                  <?= translate($settings, 'tables_action_edit') ?>
                </a>
              <?php elseif ($tableAction === 'preview'): ?>
                <a href="<?= baseUrl() . $tableActionUrl . '/' . $row[$id] ?>" class="edit-cat">
                  <?= translate($settings, 'tables_action_preview') ?>
                </a>
              <?php endif; ?>
              <a href="<?= baseUrl() . $tableDeleteUrl . '/' . $row[$id] ?>" class="delete-cat">
                <?= translate($settings, 'tables_action_delete') ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php return ob_get_clean();
}
