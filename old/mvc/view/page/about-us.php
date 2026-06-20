<?
$about_us = $data['about_us'];

foreach ($about_us as $item) {
    if(str_contains($item['variable_name'], 'title_')) {
        $about_us_items_title[] = $item;
    }

    if(str_contains($item['variable_name'], 'description_')) {
        $about_us_items_description[] = $item;
    }
}
?>

<main class="about-us">
    <div>
        <h1><?= translate($about_us, 'about_us_main_title') ?></h1>
    </div>
    <a href="<?=baseUrl() . '/page/errorPage' ?>">خطا</a>
    <img src="<?=baseUrl()?>/assets/images/logo/black_logo_transparent.png" alt="برنامه موسیقی سُرناز">
    <section>
        <p><?= translate($about_us, 'about_us_main_description') ?></p>
        <div>
            <? for($i = 0; $i < sizeof($about_us_items_title); $i++) { ?>
                <div>
                    <!-- <button data-id=<?//=$i?>> -->
                    <button id="accordion-header<?=$i?>" data-id=<?=$i?>>
                        <span class="header-icon"><?= $about_us_items_title[$i]['icon'] ?></span>
                        <span><?= translate($about_us_items_title, $i) ?></span>
                        <span class="accordion-toggle">+</span>
                    </button>
                    <div id="accordion-content<?=$i?>">
                        <p><?= translate($about_us_items_description, $i) ?></p>
                    </div>
                </div>
            <? } ?>
        </div>
    </section>
</main>

<script>
    $(function() {
        $('button').on('click', function() {
            let current_element = '#accordion-content' + $(this).data('id')
            localStorage.setItem(current_element, localStorage.getItem(current_element) == 'none' ? '0px' : 'none')
            $(current_element).css('max-height', localStorage.getItem(current_element));
        });
    });
</script>
