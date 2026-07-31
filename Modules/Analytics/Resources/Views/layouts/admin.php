<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sornaz UserInterface</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700&display=swap');
        body { font-family: Vazirmatn, Tahoma, sans-serif; }
        .sidebar { transition: all 0.3s; }
        .card-hover:hover { transform: translateY(-5px); transition: all 0.3s; }
    </style>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> -->
    <?
    // pushStyle('reset.css');
    ?>
    <?//=styles()?>
</head>
<body class="bg-gray-100">
    <?//= baseUrl() ?>
    <?// component('header'); ?>
    <main class="site-content">
        <?=$slot?>
    </main>
    <?// component('footer'); ?>
    <?
    pushScript('dashboard.js');
    pushScript('add-user-modal.js');
    // pushScript('users.js');
    pushScript('students.js');
    pushScript('teacher-templates.js');
    pushScript('teachers.js');
    pushScript('branch-templates.js');
    pushScript('branches.js');
    pushScript('classroom-templates.js');
    pushScript('classrooms.js');
    pushScript('course-templates.js');
    pushScript('courses.js');
    pushScript('term-templates.js');
    pushScript('terms.js');
    pushScript('gallery-templates.js');
    pushScript('gallery.js');
    pushScript('reports.js');
    pushScript('finance.js');
    pushScript('messages.js');
    pushScript('notifications.js');
    // pushScript('contracts.js');
    pushScript('account.js');
    pushScript('roles.js');
    pushScript('permissions.js');
    pushScript('scheduling-rules.js');
    pushScript('schedules.js');
    pushScript('member-schedules.js');
    pushScript('awards.js');
    pushScript('certificates.js');
    pushScript('educations.js');
    pushScript('events.js');
    pushScript('polls.js');
    pushScript('favorites.js');
    pushScript('lesson-templates.js');
    pushScript('lessons.js');
    pushScript('instrument-templates.js');
    pushScript('instruments.js');
    pushScript('publications.js');
    pushScript('ratings.js');
    pushScript('points.js');
    pushScript('availabilities.js');
    pushScript('availability-exceptions.js');
    pushScript('badges.js');
    pushScript('approvals.js');
    pushScript('profiles.js');
    pushScript('rating-summaries.js');
    pushScript('posts.js');
    pushScript('about-us.js');
    pushScript('contact-us.js');
    pushScript('articles.js');
    pushScript('academies.js');
    pushScript('academy-enroll.js');
    pushScript('academy-requests.js');
    // pushScript('home.js');
    // pushScript('auth.js');
    
    pushScript('experiences.js');
    pushScript('admin.js');
    ?>
    <div id="modalContainer"></div>
    <?=scripts()?>
</body>
</html>
