<?
global $config;
$base = baseUrl();
$lang = $config['app']['lang'] ?? 'fa';

$header = $data['header'];
$header_links = getFilteredList($header, 'header_link_');
$header_auth_links = getFilteredList($header, 'header_auth_link_');
$header_admin_links = getFilteredList($header, 'header_admin_link_');
$header_academy_links = getFilteredList($header, 'header_academy_link_');


// dump($header_links);
// exit();
?>

<header role="banner">
    <!-- Desktop Menu -->
    <nav class="site-nav" role="navigation" aria-label="<?= $lang === 'fa' ? 'منوی اصلی' : 'Main navigation' ?>">
        <ul class="nav-menu" role="list">
            <!-- Logo -->
            <a href="<?=baseUrl() . $header['header_main_logo']['url']?>" aria-label="<?= $lang === 'fa' ? 'صفحه اصلی' : 'Home' ?>">
                <img src="<?=baseUrl() . $header['header_main_logo']['source']?>" alt="<?= translate($header, 'header_main_logo') ?>" loading="eager">
            </a>

            <!-- Space between Logo & Links -->
            <span></span>


            <? foreach($header_links as $key => $value) { ?>
                <li>
                    <a href="<?=baseUrl() . $value['url'] ?>">
                        <?= translate($header_links, $key) ?>
                    </a>
                </li>
            <? } ?>
            <? if(isUser()) { ?>
                <li>
                    <a href="<?=baseUrl() . $header_admin_links['header_admin_link_panel']['url'] ?>">
                        <?= translate($header_admin_links, 'header_admin_link_panel') ?>
                    </a>
                </li>
            <? } ?>


            <? if(isUser()) { ?>
                <!-- Send Request Academy Link -->
                <? if(isNotAcademyOwner()) { ?>
                    <li>
                        <a href="<?=baseUrl() . $header_academy_links['header_academy_link_send_request']['url'] ?>">
                            <?= translate($header_academy_links, 'header_academy_link_send_request') ?>
                        </a>
                    </li>
                <? } ?>
                
                <!-- Academy Enroll -->
                <li>
                    <a href="<?=baseUrl() . $header_auth_links['header_auth_link_academy_enroll']['url'] ?>"><?= translate($header_auth_links, 'header_auth_link_academy_enroll') ?></a>
                </li>
                <!-- Logout -->
                <li>
                    <a href="<?=baseUrl() . $header_auth_links['header_auth_link_logout']['url'] ?>">
                        <?= translate($header_auth_links, 'header_auth_link_logout') ?>
                    </a>
                </li>
                <li><?= translate($header, 'header_greating') . translateStrings($_SESSION, 'fullname') ?></li>
            <? } else { ?>
                <!-- Auth -->
                <li>
                    <a href="<?=baseUrl() . $header_auth_links['header_auth_link_login']['url'] ?>">
                        <?= translate($header_auth_links, 'header_auth_link_login') ?>
                    </a>
                </li>
                <li>
                    <a href="<?=baseUrl() . $header_auth_links['header_auth_link_register']['url'] ?>">
                        <?= translate($header_auth_links, 'header_auth_link_register') ?>
                    </a>
                </li>
            <? } ?>

            <!-- <li>
                <a href="<?//=baseUrl() . $header_auth_links['header_auth_link_logout']['url'] ?>">
                    <?//= translate($header_auth_links, 'header_auth_link_logout') ?>
                </a>
            </li> -->
        </ul>
    </nav>
    <?// View::renderPartial('/academy/sidebar.php')?>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="mobile-menu" role="dialog" aria-modal="true" hidden>
    <ul class="mobile-menu__list" role="list">
      <li><a href="<?= $base ?>/"           class="nav-link"><?= $lang === 'fa' ? 'خانه'       : 'Home'     ?></a></li>
      <li><a href="<?= $base ?>/courses"    class="nav-link"><?= $lang === 'fa' ? 'دوره‌ها'    : 'Courses'  ?></a></li>
      <li><a href="<?= $base ?>/teachers"   class="nav-link"><?= $lang === 'fa' ? 'اساتید'     : 'Teachers' ?></a></li>
      <li><a href="<?= $base ?>/about"      class="nav-link"><?= $lang === 'fa' ? 'درباره ما'  : 'About'    ?></a></li>
      <li><a href="<?= $base ?>/contact"    class="nav-link"><?= $lang === 'fa' ? 'تماس با ما' : 'Contact'  ?></a></li>
      <? if (isGuest()): ?>
        <li><a href="<?= $base ?>/login"    class="nav-link"><?= $lang === 'fa' ? 'ورود'       : 'Login'    ?></a></li>
        <li><a href="<?= $base ?>/register" class="nav-link"><?= $lang === 'fa' ? 'ثبت نام'    : 'Register' ?></a></li>
      <? else: ?>
        <li><a href="<?= $base ?>/profile"  class="nav-link"><?= $lang === 'fa' ? 'پروفایل'    : 'Profile'  ?></a></li>
        <li><a href="<?= $base ?>/logout"   class="nav-link"><?= $lang === 'fa' ? 'خروج'       : 'Logout'   ?></a></li>
      <? endif; ?>
    </ul>
  </div>
</header>
