<div id="page-contact" class="">
    <div class="max-w-xl mx-auto px-4 py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-bold text-center mb-3"><?= e(trans('public.contact.title', 'ارتباط با ما — ارسال پیام جدید')) ?></h1>
        <p class="text-center text-gray-500 mb-10"><?= e(trans('public.contact.note')) ?></p>
        <div class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100">
            <form id="contactPublicForm" onsubmit="submitPublicContact(event)" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2"><?= e(trans('public.contact.name')) ?> *</label>
                    <input type="text" id="cName" required class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="<?= e(trans('public.contact.name_placeholder')) ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2"><?= e(trans('public.contact.email')) ?> *</label>
                    <input type="email" id="cEmail" required class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="<?= e(trans('public.contact.email_placeholder')) ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2"><?= e(trans('public.contact.subject')) ?></label>
                    <input type="text" id="cSubject" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="<?= e(trans('public.contact.subject_placeholder')) ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2"><?= e(trans('public.contact.message')) ?> *</label>
                    <textarea id="cMessage" rows="5" required class="w-full border border-gray-300 rounded-2xl py-3.5 px-5 focus:outline-none focus:border-indigo-500" placeholder="<?= e(trans('public.contact.message_placeholder')) ?>"></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-medium transition"><?= e(trans('public.contact.send')) ?></button>
            </form>
        </div>
    </div>
</div>
