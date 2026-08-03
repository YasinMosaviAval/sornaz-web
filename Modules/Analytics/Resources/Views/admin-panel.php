<div class="relative h-screen overflow-hidden">
    <div id="sidebarOverlay" class="hidden fixed inset-0 z-30 bg-black/40 lg:hidden"></div>
    <div class="flex h-full">
        <? component('sidebar'); ?>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
        <? component('header'); ?>
        <main class="flex-1 overflow-auto p-8" id="mainContent">
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
            //component('posts');
            //component('about-us');
            //component('contact-us');
            //component('articles');
            //component('academies');
            //component('academy-enroll');
            //component('academy-requests');


            //component('home');
            //component('login');
            //component('register');
            
            
            ?>
        </main>
    </div>
</div>

