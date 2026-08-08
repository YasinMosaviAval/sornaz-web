<?
$about_array = getFilteredList(setIndexforDataArray($about_us, 'variable_name'), 'about_us');
$about_title_array = getFilteredList(setIndexforDataArray($about_us, 'variable_name'), 'about_us_title');
$about_description_array = getFilteredList(setIndexforDataArray($about_us, 'variable_name'), 'about_us_description');
?>

<div id="page-about" class="">
    <div class="max-w-3xl mx-auto px-4 py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-bold text-center mb-4"><?= $about_array['about_us_main_title']['translated_value'] ?></h1>
        <div class="bg-white rounded-3xl p-8 shadow-sm mb-6 leading-relaxed text-gray-600 text-justify">
            <p class="pb-5 text-gray-600 leading-relaxed text-justify"><?= $about_array['about_us_main_description']['translated_value'] ?></p>
        </div>
        <div class="space-y-3" id="aboutAccordions">
            <? for($item = 1; $item <= sizeof($about_title_array); $item++) { ?>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <button type="button" onclick="toggleAccordion(this)" class="w-full flex items-center justify-between px-6 py-4 text-right font-bold hover:bg-gray-50">
                        <span><?= $about_title_array['about_us_title_' . $item]['translated_value'] ?></span>
                        <i class="fas fa-plus text-indigo-500 accordion-icon text-sm"></i>
                    </button>
                    <div class="accordion-body px-6">
                        <p class="pb-5 text-gray-600 leading-relaxed text-justify"><?= $about_description_array['about_us_description_' . $item]['translated_value'] ?></p>
                    </div>
                </div>
            <? } ?>
        </div>
    </div>
</div>