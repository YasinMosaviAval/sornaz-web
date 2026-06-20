<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$footer = $data['footer'];
$footer_links_1 = getFilteredList($footer, 'footer_row_1_link_');
$footer_links_2 = getFilteredList($footer, 'footer_row_2_link_');

global $config;
$lang = $config['app']['lang'] ?? 'fa';
$year = date('Y');
?>


<!--
  <footer>
      <div>
          <div>
              <h4><?//= translate($footer, 'footer_row_title_1') ?></h4>
              <ul>
                  <?// foreach($footer_links_1 as $key => $value) { ?>
                      <li>
                          <a href="<?//=baseUrl() . $value['url'] ?>">
                              <?//= translate($footer_links_1, $key) ?>
                          </a>
                      </li>
                  <?// } ?>
              </ul>
          </div>
          <div>
              <h4><?//= translate($footer, 'footer_row_title_2') ?></h4>
              <ul>
                  <?// foreach($footer_links_2 as $key => $value) { ?>
                      <li>
                          <a href="<?//=baseUrl() . $value['url'] ?>">
                              <?//= translate($footer_links_2, $key) ?>
                          </a>
                      </li>
                  <?// } ?>
              </ul>
          </div>
          <?//= $footer['footer_approval_1']['text_fa'] ?>
          <?//= $footer['footer_approval_2']['text_fa'] ?>
      </div>
      <p><?//= translate($footer, 'footer_copyright') ?></p>
  </footer>
-->


<footer class="site-footer" role="contentinfo">

  <div class="footer-grid">

    <!-- ستون ۱: برند -->
    <div>
      <a href="<?= baseUrl() ?>/" class="footer-logo">
        <img src="<?= baseUrl() ?>/assets/images/logo/black_logo_transparent.png" alt="Sornaz Academy" width="100" loading="lazy">
      </a>
      <!-- <p class="footer-tagline"><?//= $lang === 'fa' ? 'برنامه موسیقی سرناز — آموزش حرفه‌ای موسیقی' : 'Sornaz Academy — Professional Music Education'?></p> -->
      <p><?= translate($settings, 'about_us_main_description') ?></p>
      <!-- Social -->
      <ul class="footer-social" role="list" aria-label="<?= $lang === 'fa' ? 'شبکه‌های اجتماعی' : 'Social media' ?>">
        <li><a href="#" aria-label="Instagram" rel="noopener noreferrer"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a></li>
        <li><a href="#" aria-label="Telegram"  rel="noopener noreferrer"><i class="fa-brands fa-telegram"  aria-hidden="true"></i></a></li>
        <li><a href="#" aria-label="YouTube"   rel="noopener noreferrer"><i class="fa-brands fa-youtube"   aria-hidden="true"></i></a></li>
      </ul>
    </div>

    <!-- ستون ۲: دسترسی سریع -->
    <div>
      <h4 class="footer-col__title"><?= $lang === 'fa' ? 'دسترسی سریع' : 'Quick Access' ?></h4>
      <!-- <h4><?//= translate($footer, 'footer_row_title_1') ?></h4> -->
      <ul class="footer-links" role="list">
        <li><a href="<?= baseUrl() ?>/"          class="footer-link"><?= $lang === 'fa' ? 'خانه'       : 'Home'     ?></a></li>
        <? foreach($footer_links_1 as $key => $value) { ?>
            <li>
                <a href="<?=baseUrl() . $value['url'] ?>">
                    <?= translate($footer_links_1, $key) ?>
                </a>
            </li>
        <? } ?>
        <!-- <li><a href="<?//= baseUrl() ?>/courses"   class="footer-link"><?//= $lang === 'fa' ? 'دوره‌ها'    : 'Courses'  ?></a></li> -->
        <!-- <li><a href="<?//= baseUrl() ?>/teachers"  class="footer-link"><?//= $lang === 'fa' ? 'اساتید'     : 'Teachers' ?></a></li> -->
      </ul>
      <br>
      <h4><?= translate($footer, 'footer_row_title_2') ?></h4>
      <ul class="footer-links" role="list">
          <? foreach($footer_links_2 as $key => $value) { ?>
              <li>
                  <a href="<?=baseUrl() . $value['url'] ?>">
                      <?= translate($footer_links_2, $key) ?>
                  </a>
              </li>
          <? } ?>
      </ul>
    </div>

    <!-- ستون ۳: دوره‌ها -->
    <div>
      <h4 class="footer-col__title"><?= $lang === 'fa' ? 'دوره‌های محبوب' : 'Popular Courses' ?></h4>
      <ul class="footer-links" role="list">
        <li><a href="<?= baseUrl() ?>/courses/guitar"  class="footer-link"><?= $lang === 'fa' ? 'گیتار'    : 'Guitar'   ?></a></li>
        <li><a href="<?= baseUrl() ?>/courses/piano"   class="footer-link"><?= $lang === 'fa' ? 'پیانو'    : 'Piano'    ?></a></li>
        <li><a href="<?= baseUrl() ?>/courses/violin"  class="footer-link"><?= $lang === 'fa' ? 'ویولن'    : 'Violin'   ?></a></li>
        <li><a href="<?= baseUrl() ?>/courses/voice"   class="footer-link"><?= $lang === 'fa' ? 'آواز'     : 'Vocals'   ?></a></li>
      </ul>
    </div>

    <!-- ستون ۴: تماس -->
    <div>
      <address class="footer-address">
        <ul class="footer-legal" role="list">
          <li><a href="<?= baseUrl() ?>/privacy" class="footer-link"><?= $lang === 'fa' ? 'حریم خصوصی' : 'Privacy'      ?></a></li>
          <li><a href="<?= baseUrl() ?>/terms"   class="footer-link"><?= $lang === 'fa' ? 'قوانین'      : 'Terms of Use' ?></a></li>
        </ul>
      </address>
      <br>
      <h4 class="footer-col__title"><?= $lang === 'fa' ? 'تماس با ما' : 'Contact' ?></h4>
      <address class="footer-address">
        <ul class="footer-links" role="list">
          <li>
            <i class="fa-solid fa-phone" aria-hidden="true"></i>
            <a href="tel:+98XXXXXXXXXX" class="footer-link">۰۲۱-XXXX-XXXX</a>
          </li>
          <li>
            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
            <a href="mailto:info@sornaz.com" class="footer-link">info@sornaz.com</a>
          </li>
          <li>
            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
            <p><?= $lang === 'fa' ? 'تهران، ایران' : 'Tehran, Iran' ?></p>
          </li>
        </ul>
      </address>
    </div>

  </div>

  <!-- Bottom Bar -->
  <!-- <div class="footer-bottom"> -->
    <p class="footer-copyright"><?= translate($footer, 'footer_copyright') ?></p>
    <!-- <p class="footer-copyright">
      &copy; <?//= $year ?> — <?//= $lang === 'fa' ? 'تمامی حقوق برای آموزشگاه موسیقی سرناز محفوظ است.' : 'All rights reserved. Sornaz Academy.'?>
    </p> -->
  <!-- </div> -->

</footer>
