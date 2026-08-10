<div class="relative h-screen overflow-hidden">
    <div id="sidebarOverlay" class="hidden fixed inset-0 z-30 bg-black/40 lg:hidden"></div>
    <div class="flex h-full">
        <? component('sidebar'); ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
        <? component('header'); ?>
        <main class="flex-1 overflow-auto p-8" id="mainContent">
            <?php if ($message = session()->getFlash('admin_test_message')): ?>
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800"><?= e($message) ?></div>
            <?php endif; ?>
            <?php if ($error = session()->getFlash('admin_test_error')): ?>
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"><?= e($error) ?></div>
            <?php endif; ?>
            <?
            component('add-user-modal');

            component('dashboard');
            component('account');
            component('reports');
            component('messages');
            component('notifications');
            component('students');
            component('teachers');
            component('branches');
            component('classrooms');
            component('courses');
            component('terms');
            component('gallery');
            component('finance');
            component('scheduling-rules');
            component('schedules');
            component('instruments');
            component('lessons');
            component('member-schedules');
            component('availabilities');
            component('availability-exceptions');
            component('points');
            component('users');
            component('roles');
            component('permissions');

            
            
            
            
            
            //component('awards');
            //component('certificates');
            //component('experiences');
            //component('educations');
            //component('events');
            //component('polls');
            //component('favorites');
            //component('publications');
            //component('ratings');
            //component('badges');
            //component('approvals');
            //component('contracts');
            //component('profiles');
            //component('rating-summaries');



            component('posts');
            component('about-us');
            component('contact-us');
            component('articles');
            component('academies');
            component('academy-enroll');
            component('academy-requests');
            component('tests', ['testStats' => $testStats ?? []]);
            // component('home');
            // component('login');
            // component('register');
            
            
            ?>
        </main>
    </div>
</div>

