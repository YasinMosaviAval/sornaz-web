<?

$contact_array = setIndexforDataArray($contact_us, 'variable_name');

$contact_category_title_array = getFilteredList(setIndexforDataArray($contact_us, 'variable_name'), 'contact_us_category_title');
$contact_category_description_array = getFilteredList(setIndexforDataArray($contact_us, 'variable_name'), 'contact_us_category_description');
// dump($contact_array);
// dump($header_array);
// dump($footer_array);

?>

<div id="page-contact" class="">
    <div class="max-w-xl mx-auto px-4 py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-bold text-center mb-3"><?= $contact_array['contact_us_header']['translated_value'] ?></h1>
        <p class="text-center text-gray-500 mb-10"><?= $contact_array['contact_us_note']['translated_value'] ?></p>
        <div class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100">
            <form id="contactPublicForm" onsubmit="submitPublicContact(event)" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2"><?= $contact_array['contact_us_name_label']['translated_value'] ?> *</label>
                    <input type="text" id="cName" required class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="<?= $contact_array['contact_us_name_placeholder']['translated_value'] ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2"><?= $contact_array['contact_us_email_label']['translated_value'] ?> *</label>
                    <input type="email" id="cEmail" required class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="<?= $contact_array['contact_us_email_placeholder']['translated_value'] ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2"><?= $contact_array['contact_us_category_label']['translated_value'] ?></label>
                    <input type="text" id="cSubject" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="<?= $contact_array['contact_us_category_placeholder']['translated_value'] ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2"><?= $contact_array['contact_us_message_label']['translated_value'] ?> *</label>
                    <textarea id="cMessage" rows="5" required class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="<?= $contact_array['contact_us_message_placeholder']['translated_value'] ?>"></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition"><?= $contact_array['contact_us_send_button']['translated_value'] ?></button>
            </form>
        </div>
    </div>
</div>