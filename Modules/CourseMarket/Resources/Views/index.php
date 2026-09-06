<?php
$titles = ['catalog'=>'فروشگاه دوره‌ها','manage'=>'ساخت و فروش دوره','library'=>'دوره‌های خریداری‌شده','edit'=>'استودیوی ساخت دوره','show'=>$course['title'] ?? 'دوره','receipt'=>'نتیجه خرید','error'=>'پیام'];
$pageTitle = $titles[$mode];
$money = fn($amount) => (int)$amount ? number_format((int)$amount).' تومان' : 'رایگان';
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
 <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
 <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
 <title><?= e($pageTitle) ?> | سرناز</title>
 <link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css">
 <link rel="stylesheet" href="/assets/course-market/course-market.css">
 <script src="/assets/course-market/course-market.js" defer></script>
</head>
<body>
<header class="topbar"><a class="brand" href="/">سُرناز <span>یاد بگیر، آموزش بده</span></a><a href="/analytics/admin-panel">بازگشت به پنل کاربری ←</a></header>
<div class="shell">
 <nav class="market-nav" aria-label="دوره‌های آموزشی">
  <a href="/course-market" <?= $mode==='catalog'?'aria-current="page"':'' ?>>فروشگاه دوره‌ها</a>
  <a href="/course-market/manage" <?= in_array($mode,['manage','edit'])?'aria-current="page"':'' ?>>ساخت و فروش دوره</a>
  <a href="/course-market/library" <?= $mode==='library'?'aria-current="page"':'' ?>>خریدهای من</a>
 </nav>
 <main>
 <div id="notice" role="status" aria-live="polite" hidden></div>
 <?php if (in_array($mode, ['catalog','manage','library'])): ?>
 <section class="hero"><div><p class="eyebrow">آموزش موسیقی، بدون مرز</p><h1><?= e($pageTitle) ?></h1><p><?= $mode==='manage'?'دانش و تجربه‌ات را به یک دوره آموزشی تبدیل کن.':($mode==='library'?'دوره‌هایی که تهیه کرده‌اید، همیشه در دسترس شما هستند.':'از اولین نت تا اجرای حرفه‌ای؛ مسیر یادگیری خودت را پیدا کن.') ?></p></div><?php if($mode==='manage'): ?><a class="button" href="/course-market/create">＋ ساخت دوره جدید</a><?php endif; ?></section>
 <?php if (!$items): ?><section class="empty"><h2><?= $mode==='manage'?'اولین دوره‌ات را بساز':($mode==='library'?'هنوز دوره‌ای تهیه نکرده‌اید':'هنوز دوره‌ای منتشر نشده است') ?></h2><p><?= $mode==='manage'?'مثلاً آموزش گیتار از پایه تا پیشرفته؛ با فصل‌ها، ویدیوها و تمرین‌های خودت.':'دوره‌های منتشرشده برای خرید در فروشگاه نمایش داده می‌شوند.' ?></p><a class="button secondary" href="<?= $mode==='manage'?'/course-market/create':'/course-market' ?>"><?= $mode==='manage'?'شروع ساخت دوره':'مشاهده فروشگاه' ?></a></section><?php endif; ?>
 <div class="cards"><?php foreach ($items as $item): ?>
  <article class="card"><?php if($item['cover_id']): ?><img class="cover" src="/course-market/media/<?= (int)$item['cover_id'] ?>" alt="<?= e($item['title']) ?>" loading="lazy"><?php else: ?><div class="cover placeholder">♫</div><?php endif; ?>
   <div class="card-body"><?php if($mode==='manage'): ?><span class="badge"><?= $item['status']==='published'?'منتشرشده':'پیش‌نویس' ?></span><?php endif; ?><h2><?= e($item['title']) ?></h2><p class="excerpt"><?= e(mb_substr($item['description'],0,160)) ?></p><div class="card-footer"><strong><?= e($money($item['price'])) ?></strong><a href="/course-market/courses/<?= (int)$item['id'] ?><?= $mode==='manage'?'/edit':'' ?>"><?= $mode==='manage'?'ویرایش دوره':($mode==='library'?'شروع یادگیری':'مشاهده دوره') ?> ←</a></div></div>
  </article>
 <?php endforeach; ?></div>
 <?php if ($mode==='manage'): ?><section class="panel"><h2>فروش‌های موفق</h2><?php if(!$sales): ?><p class="muted">پس از اولین خرید، گزارش فروش اینجا نمایش داده می‌شود.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>دوره</th><th>مبلغ</th><th>شماره پیگیری</th><th>تاریخ</th></tr></thead><tbody><?php foreach($sales as $sale): ?><tr><td><?= e($sale['title']) ?></td><td><?= e($money($sale['amount'])) ?></td><td><?= e($sale['reference_id'] ?? 'رایگان') ?></td><td><?= e($sale['paid_at']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section><?php endif; ?>
 <?php elseif ($mode==='edit'): ?>
 <section class="hero"><div><p class="eyebrow">استودیوی مدرس</p><h1>دوره‌ات را قدم‌به‌قدم بساز</h1><p>اطلاعات دوره را وارد کن، فصل‌ها را بچین و محتوای هر درس را اضافه کن.</p></div><span id="save-state" class="badge">ذخیره نشده</span></section>
 <form id="course-editor">
 <section class="panel"><h2>۱. معرفی دوره</h2><div class="fields"><label>عنوان دوره<input name="title" required maxlength="180" placeholder="آموزش گیتار از پایه تا پیشرفته"></label><label>قیمت (تومان)<input name="price" type="number" min="0" max="1000000000" step="1" required value="0"><small>برای دوره رایگان، صفر وارد کنید.</small></label></div><label>توضیحات دوره<textarea name="description" rows="5" maxlength="20000" placeholder="هنرجو در این دوره چه چیزهایی یاد می‌گیرد؟ پیش‌نیازها چیست؟"></textarea></label><div class="cover-editor"><img id="cover-preview" class="cover-preview" alt="تصویر جلد دوره" hidden><label>تصویر جلد<input id="cover-upload" type="file" accept="image/jpeg,image/png,image/webp"><small>JPG، PNG یا WebP، حداکثر ۱۰ مگابایت</small></label></div></section>
 <section class="panel"><div class="section-heading"><div><h2>۲. فصل‌ها و درس‌ها</h2><p class="muted">هر درس می‌تواند متن، چند تصویر و چند ویدیو داشته باشد.</p></div><button type="button" id="add-chapter" class="secondary">＋ افزودن فصل</button></div><div id="chapters"></div><p id="curriculum-empty" class="empty-hint">با افزودن اولین فصل، ساخت محتوای دوره را شروع کنید.</p></section>
 <footer class="save-bar"><p>برای انتشار، جلد، توضیحات و محتوای درس‌ها را تکمیل کنید.</p><div><button type="submit" data-status="draft" class="secondary">ذخیره پیش‌نویس</button><button type="submit" data-status="published">ذخیره و انتشار برای فروش</button><a id="view-course" hidden>مشاهده دوره ←</a></div></footer>
 </form>
 <script type="application/json" id="course-data"><?= json_encode($course, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE) ?></script>
 <?php elseif ($mode==='show'): ?>
 <section class="course-intro"><?php if($course['cover_id']): ?><img class="detail-cover" src="/course-market/media/<?= (int)$course['cover_id'] ?>" alt="<?= e($course['title']) ?>"><?php endif; ?><div><p class="eyebrow">دوره آموزشی سرناز</p><h1><?= e($course['title']) ?></h1><p class="prose"><?= e($course['description']) ?></p><strong class="price"><?= e($money($course['price'])) ?></strong><?php if(!$course['access']): ?><?php if(auth()->check()): ?><button type="button" data-buy="<?= (int)$course['id'] ?>"><?= (int)$course['price']?'خرید دوره':'افزودن به دوره‌های من' ?></button><?php else: ?><a class="button" href="/system/login">ورود برای تهیه دوره</a><?php endif; ?><?php else: ?><span class="badge">به محتوای دوره دسترسی دارید</span><?php endif; ?></div></section>
 <section class="panel"><h2>سرفصل‌های دوره</h2><?php $fileMap=array_column($course['files']??[],null,'id'); foreach($course['curriculum'] as $ci=>$chapter): ?><section class="chapter"><h3><?= ($ci+1).'. '.e($chapter['title']) ?></h3><?php foreach($chapter['lessons'] as $li=>$lesson): ?><details class="lesson"><summary><?= ($li+1).'. '.e($lesson['title']) ?> <span class="muted"><?= $course['access']?'مشاهده درس':'پس از تهیه دوره' ?></span></summary><?php if($course['access'] && !empty($lesson['locked'])): ?><form data-unlock-lesson action="/course-market/courses/<?= (int)$course['id'] ?>/lessons/<?= (int)$lesson['post_id'] ?>/unlock" method="post"><label>رمز اختصاصی درس<input type="password" name="password" required maxlength="72" autocomplete="off"></label><button type="submit">باز کردن درس</button></form><?php elseif($course['access']): ?><p class="prose"><?= e($lesson['text']) ?></p><div class="lesson-media"><?php foreach($lesson['media'] as $mid): ?><?php if(str_starts_with($fileMap[$mid]['mime']??'','video/')): ?><video controls preload="none" playsinline src="/course-market/media/<?= (int)$mid ?>"></video><?php else: ?><img loading="lazy" src="/course-market/media/<?= (int)$mid ?>" alt="تصویر درس <?= e($lesson['title']) ?>"><?php endif; ?><?php endforeach; ?></div><?php else: ?><p>برای مشاهده ویدیوها، تصاویر و متن درس، دوره را تهیه کنید.</p><?php endif; ?></details><?php endforeach; ?></section><?php endforeach; ?></section>
 <?php elseif($mode==='receipt'): ?><section class="empty"><h1>خرید شما با موفقیت انجام شد</h1><p>شماره پیگیری: <?= e($order['reference_id'] ?? '') ?></p><a class="button" href="/course-market/courses/<?= (int)$order['course_id'] ?>">مشاهده دوره</a></section>
 <?php else: ?><section class="empty"><h1>امکان انجام درخواست وجود ندارد</h1><p><?= e($message) ?></p><a class="button" href="/course-market">بازگشت به دوره‌ها</a></section><?php endif; ?>
 </main>
</div>
</body></html>
