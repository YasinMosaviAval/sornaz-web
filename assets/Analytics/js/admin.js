function showSection(id) {
    const section = id;

    document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'));

    const target = document.getElementById(section);
    if (target) {
        target.classList.remove('hidden');
    }

    document.getElementById(id).classList.remove('hidden');
    
    if (section === 'messages') {
        if (typeof renderMessagesBranchTabs === 'function') renderMessagesBranchTabs();
        if (typeof filterMessagesByBranch === 'function') filterMessagesByBranch('all');
    }
    if (section === 'notifications') {
        if (typeof renderNotificationsBranchTabs === 'function') renderNotificationsBranchTabs();
        if (typeof filterNotificationsByBranch === 'function') filterNotificationsByBranch('all');
    }
    if (section === 'contracts') {
        if (typeof renderContractsBranchTabs === 'function') renderContractsBranchTabs();
        if (typeof filterContractsByBranch === 'function') filterContractsByBranch('all');
    }
    if (section === 'account') {
        if (typeof renderAccountInfo === 'function') renderAccountInfo();
    }
    if (section === 'roles') {
        if (typeof renderRolesBranchTabs === 'function') renderRolesBranchTabs();
        if (typeof filterRolesByBranch === 'function') filterRolesByBranch('all');
    }
    if (section === 'permissions') {
        if (typeof renderPermissionsBranchTabs === 'function') renderPermissionsBranchTabs();
        if (typeof filterPermissionsByBranch === 'function') filterPermissionsByBranch('all');
    }
    if (section === 'schedules') {
        if (typeof renderSchedulesBranchTabs === 'function') renderSchedulesBranchTabs();
        if (typeof filterSchedules === 'function') filterSchedules();
    }
    if (section === 'scheduling-rules') {
        if (typeof renderRulesBranchTabs === 'function') renderRulesBranchTabs();
        if (typeof filterRulesByBranch === 'function') filterRulesByBranch('all');
    }
    if (section === 'dashboard') {
        if (typeof renderDashboardBranchTabs === 'function') renderDashboardBranchTabs();
        if (typeof renderDashboard === 'function') renderDashboard();
    }
    if (section === 'member-schedules') {
        if (typeof loadMemberSchedules === 'function') loadMemberSchedules().catch(error=>alert(error.message));
        else {
            if (typeof renderMemberSchedulesBranchTabs === 'function') renderMemberSchedulesBranchTabs();
            if (typeof filterMemberSchedules === 'function') filterMemberSchedules();
        }
    }
    // if (section === 'experiences') {
    //     if (typeof renderExperiencesBranchTabs === 'function') renderExperiencesBranchTabs();
    //     if (typeof filterExperiencesByBranch === 'function') filterExperiencesByBranch('all');
    // }
    if (section === 'experiences') {
        setTimeout(() => {
            if (typeof renderExperiencesBranchTabs === 'function') renderExperiencesBranchTabs();
            if (typeof filterExperiencesByBranch === 'function') filterExperiencesByBranch('all');
        }, 100);
    }
    if (section === 'awards') {
        if (typeof renderAwardsBranchTabs === 'function') renderAwardsBranchTabs();
        if (typeof filterAwardsByBranch === 'function') filterAwardsByBranch('all');
    }
    if (section === 'certificates') {
        if (typeof renderCertificatesBranchTabs === 'function') renderCertificatesBranchTabs();
        if (typeof filterCertificatesByBranch === 'function') filterCertificatesByBranch('all');
    }
    if (section === 'educations') {
        setTimeout(() => {
            if (typeof renderEducationsBranchTabs === 'function') renderEducationsBranchTabs();
            if (typeof filterEducationsByBranch === 'function') filterEducationsByBranch('all');
        }, 100);
    }
    if (section === 'events') {
        setTimeout(() => {
            if (typeof renderEventsBranchTabs === 'function') renderEventsBranchTabs();
            if (typeof filterEventsByBranch === 'function') filterEventsByBranch('all');
        }, 100);
    }
    if (section === 'polls') {
        setTimeout(() => {
            if (typeof renderPollsBranchTabs === 'function') renderPollsBranchTabs();
            if (typeof filterPollsByBranch === 'function') filterPollsByBranch('all');
        }, 100);
    }
    if (section === 'favorites') {
        setTimeout(() => {
            if (typeof renderFavoritesBranchTabs === 'function') renderFavoritesBranchTabs();
            if (typeof filterFavoritesByBranch === 'function') filterFavoritesByBranch('all');
        }, 100);
    }
    if (section === 'instruments') {
        setTimeout(() => {
            if (typeof renderInstrumentsBranchTabs === 'function') renderInstrumentsBranchTabs();
            if (typeof filterInstrumentsByBranch === 'function') filterInstrumentsByBranch('all');
        }, 100);
    }
    if (section === 'lessons') {
        setTimeout(async () => {
            try {
                if (typeof loadLessonDatabaseData === 'function') await loadLessonDatabaseData();
                else {
                    if (typeof renderLessonsBranchTabs === 'function') renderLessonsBranchTabs();
                    if (typeof filterLessonsByBranch === 'function') filterLessonsByBranch('all');
                }
            } catch (error) {
                console.error('Lesson database loading failed:', error);
                alert(error.message || 'بارگذاری درس‌های شعب ناموفق بود.');
            }
        }, 100);
    }
    if (section === 'chart-gallery' && typeof window.openChartGallery === 'function') {
        window.openChartGallery();
    }
    if (section === 'publications') {
        setTimeout(() => {
            if (typeof renderPublicationsBranchTabs === 'function') renderPublicationsBranchTabs();
            if (typeof filterPublicationsByBranch === 'function') filterPublicationsByBranch('all');
        }, 100);
    }
    if (section === 'ratings') {
        setTimeout(() => {
            if (typeof renderRatingsBranchTabs === 'function') renderRatingsBranchTabs();
            if (typeof filterRatingsByBranch === 'function') filterRatingsByBranch('all');
        }, 100);
    }
    if (section === 'points') {
        setTimeout(() => {
            if (typeof renderPointsBranchTabs === 'function') renderPointsBranchTabs();
            if (typeof filterPointsByBranch === 'function') filterPointsByBranch('all');
        }, 100);
    }
    if (section === 'availabilities') {
        setTimeout(() => {
            if (typeof renderAvailabilitiesBranchTabs === 'function') renderAvailabilitiesBranchTabs();
            if (typeof filterAvailabilitiesByBranch === 'function') filterAvailabilitiesByBranch('all');
        }, 100);
    }
    if (section === 'availability-exceptions') {
        setTimeout(() => {
            if (typeof renderExceptionsBranchTabs === 'function') renderExceptionsBranchTabs();
            if (typeof filterExceptionsByBranch === 'function') filterExceptionsByBranch('all');
        }, 100);
    }
    if (section === 'badges') {
        setTimeout(() => {
            if (typeof renderBadgesBranchTabs === 'function') renderBadgesBranchTabs();
            if (typeof filterBadgesByBranch === 'function') filterBadgesByBranch('all');
        }, 100);
    }
    if (section === 'approvals') {
        setTimeout(() => {
            if (typeof renderApprovalsBranchTabs === 'function') renderApprovalsBranchTabs();
            if (typeof filterApprovalsByBranch === 'function') filterApprovalsByBranch('all');
        }, 100);
    }
    if (section === 'profiles') {
        setTimeout(() => {
            if (typeof renderProfilesBranchTabs === 'function') renderProfilesBranchTabs();
            if (typeof filterProfilesByBranch === 'function') filterProfilesByBranch('all');
        }, 100);
    }
    if (section === 'rating-summaries') {
        setTimeout(() => {
            if (typeof renderRatingSummariesBranchTabs === 'function') renderRatingSummariesBranchTabs();
            if (typeof filterRatingSummariesByBranch === 'function') filterRatingSummariesByBranch('all');
        }, 100);
    }
    if (section === 'posts') {
        setTimeout(() => {
            if (typeof renderPostsBranchTabs === 'function') renderPostsBranchTabs();
            if (typeof filterPostsByBranch === 'function') filterPostsByBranch('all');
            if (typeof filterPostsByStatus === 'function') filterPostsByStatus('all');
        }, 100);
    }
    if (section === 'post-categories' && typeof loadPostCategories === 'function') {
        setTimeout(() => loadPostCategories(), 100);
    }
    if (section === 'about-us') {
        setTimeout(() => { if (typeof renderAboutUs === 'function') renderAboutUs(); }, 100);
    }
    if (section === 'contact-us') {
        setTimeout(() => { if (typeof renderContactUs === 'function') renderContactUs(); }, 100);
    }
    if (section === 'articles') {
        setTimeout(() => {
            if (typeof renderArticleCategoryTabs === 'function') renderArticleCategoryTabs();
            if (typeof filterArticlesByCategory === 'function') filterArticlesByCategory('all');
        }, 100);
    }
    if (section === 'academies') {
        setTimeout(() => { if (typeof renderAcademies === 'function') renderAcademies(); }, 100);
    }
    if (section === 'academy-enroll') {
        setTimeout(() => { if (typeof renderEnrollPage === 'function') renderEnrollPage(); }, 100);
    }
    if (section === 'academy-requests') {
        setTimeout(() => { if (typeof renderAcademyRequestsTable === 'function') renderAcademyRequestsTable(); }, 100);
    }
    if (section === 'home') {
        setTimeout(() => {
            if (typeof renderHome === 'function') renderHome();
        }, 100);
    }
    if (section === 'login' || section === 'register') {
        // صفحات احراز هویت معمولاً بدون سایدبار ادمین نمایش داده می‌شوند
        // اگر سایدبار داری می‌توانی اینجا مخفی‌اش کنی:
        // document.getElementById('sidebar')?.classList.add('hidden');
    }
    if (section === 'forgot-password') {
        setTimeout(() => {
            if (typeof setFpMethod === 'function') setFpMethod('email');
            if (typeof fpGoStep === 'function') fpGoStep(1);
        }, 50);
    }
}

function showNotifications() {
    alert('نوتیفیکیشن‌ها:\n- پرداخت جدید\n- غیبت ۲ هنرجو\n- درخواست رزرو اتاق تمرین');
}

function toggleSidebar() {

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (!sidebar) return;

    // ---------- Mobile ----------
    if (window.innerWidth < 1024) {

        const isClosed = sidebar.classList.contains('-translate-x-full');

        if (isClosed) {
            sidebar.classList.remove('-translate-x-full');
            overlay?.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        return;
    }

    // ---------- Desktop ----------
    sidebar.classList.toggle('w-72');
    sidebar.classList.toggle('w-20');

    localStorage.setItem(
        'sidebarCollapsed',
        sidebar.classList.contains('w-20')
    );
}

document.addEventListener('DOMContentLoaded', () => {
    const requestedSection = window.location.hash.replace('#', '');
    if (requestedSection && document.getElementById(requestedSection)) showSection(requestedSection);

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (!sidebar) return;

    // وضعیت ذخیره شده دسکتاپ
    if (window.innerWidth >= 1024) {

        const collapsed =
            localStorage.getItem('sidebarCollapsed') === 'true';

        if (collapsed) {
            sidebar.classList.remove('w-72');
            sidebar.classList.add('w-20');
        }
    }

    // بستن با کلیک روی Overlay
    overlay?.addEventListener('click', () => {

        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');

    });

    // بستن با ESC
    document.addEventListener('keydown', e => {

        if (e.key !== 'Escape') return;

        if (window.innerWidth >= 1024) return;

        sidebar.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');

    });

});

// این تابع را در main.js یا یک فایل مشترک بگذار
window.closeModal = function() {
    const container = document.getElementById('modalContainer');
    if (container) {
        container.innerHTML = '';
    } else {
        console.warn('modalContainer پیدا نشد');
    }
};
