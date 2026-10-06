(function () {
  'use strict';

  let dashboardData = null;
  let currentDashboardBranch = 'all';
  let loading = false;
  let pendingDashboardBranch = null;

  function endpoint() {
    const params = new URLSearchParams();
    if (currentDashboardBranch !== 'all')
      params.set('branchId', currentDashboardBranch === 'academy' ? '0' : currentDashboardBranch);
    return '/analytics/admin-dashboard' + (params.size ? '?' + params : '');
  }

  async function requestDashboard() {
    const response = await fetch(endpoint(), {
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    const raw = await response.text();
    let payload;
    try {
      payload = JSON.parse(raw);
    } catch (_) {
      throw new Error(
        response.redirected
          ? 'نشست شما منقضی شده است. لطفاً دوباره وارد شوید.'
          : 'پاسخ نامعتبر از سرور دریافت شد. لطفاً صفحه را دوباره بارگذاری کنید.'
      );
    }
    const envelope = payload.data ?? payload;
    if (response.status === 401) {
      const loginUrl = envelope.loginUrl || '/system/login';
      window.location.assign(loginUrl);
      throw new Error(envelope.message || 'نشست شما منقضی شده است.');
    }
    if (!response.ok || envelope.success === false)
      throw new Error(envelope.message || 'بارگذاری داشبورد ناموفق بود.');
    return envelope.data ?? envelope;
  }

  window.goToSectionFromDashboard = function (sectionId) {
    if (typeof window.showSection === 'function') return window.showSection(sectionId);
    document.querySelectorAll('.section').forEach((el) => el.classList.add('hidden'));
    document.getElementById(sectionId)?.classList.remove('hidden');
  };

  window.decideDashboardUserMerge = async function (id, decision) {
    const verb = decision === 'approved' ? 'تأیید و منتقل' : 'رد';
    if (!(await AppDialog.confirm('این درخواست ' + verb + ' شود؟'))) return;
    const json = JSON.stringify({ decision, note: '' }),
      bytes = new TextEncoder().encode(json);
    let binary = '';
    bytes.forEach((byte) => (binary += String.fromCharCode(byte)));
    const body = new URLSearchParams();
    body.set('_token', window.adminCsrfToken || '');
    body.set(
      'payload_b64',
      btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '')
    );
    try {
      const response = await fetch('/analytics/admin-account/merges/' + Number(id) + '/decision', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.adminCsrfToken || '',
          },
          body,
        }),
        payload = await response.json(),
        result = payload.data ?? payload;
      if (!response.ok || result.success === false)
        throw new Error(result.message || 'ثبت تصمیم ناموفق بود.');
      await window.refreshDashboard();
    } catch (error) {
      alert(error.message);
    }
  };

  window.renderDashboardBranchTabs = function () {
    const container = document.getElementById('dashboardBranchTabs');
    if (!container || !dashboardData) return;
    container.dataset.selectedValue = String(currentDashboardBranch);
    container.innerHTML = '';
    [{ id: 'all', name: 'همه' }, ...(dashboardData.branches || [])].forEach((branch) => {
      const active = String(currentDashboardBranch) === String(branch.id);
      const button = document.createElement('button');
      button.type = 'button';
      button.className =
        'dashboard-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border transition ' +
        (active
          ? 'bg-indigo-600 text-white border-indigo-600'
          : 'border-gray-200 hover:bg-gray-50');
      button.dataset.value = branch.id;
      button.textContent = branch.name;
      button.onclick = () => window.filterDashboardByBranch(branch.id);
      container.appendChild(button);
    });
  };

  window.filterDashboardByBranch = async function (branchId) {
    if (String(branchId) === String(currentDashboardBranch)) return;
    currentDashboardBranch = branchId;
    if (loading) {
      pendingDashboardBranch = branchId;
      window.renderDashboardBranchTabs();
      window.applyAcademyOrganizationTabs?.();
      return;
    }
    await window.refreshDashboard();
  };

  window.renderDashboard = function () {
    if (!dashboardData) return;
    const pendingSelections = new Map();
    const pendingHours = new Map();
    document.querySelectorAll('#dashboardActionItems select[id^="waitingStart-"]').forEach((field) => {
      if (field.value) pendingHours.set(field.id, { html: field.innerHTML, value: field.value });
    });
    document.querySelectorAll('#dashboardActionItems select[id^="waitingTeacher-"], #dashboardActionItems select[id^="waitingDay-"], #dashboardActionItems input[id^="waitingFirstDate-"]').forEach((field) => {
      if (field.value) pendingSelections.set(field.id, field.value);
    });
    const setList = (id, html) => {
      const element = document.getElementById(id);
      if (element) element.innerHTML = html;
    };
    setList('dashboardStats', window.getDashboardStatsHTML?.(dashboardData.stats) || '');
    setList(
      'dashboardActionItems',
      window.getDashboardActionItemsHTML?.(dashboardData.actionItems) || ''
    );
    pendingSelections.forEach((value, id) => {
      const select = document.getElementById(id);
      if (select?.tagName === 'INPUT' || (select && [...select.options].some((option) => option.value === value))) select.value = value;
    });
    pendingHours.forEach((saved, id) => {
      const select = document.getElementById(id);
      if (!select || ![...saved.html.matchAll(/value="([^"]*)"/g)].some((match) => match[1] === saved.value)) return;
      select.innerHTML = saved.html;
      select.value = saved.value;
      select.disabled = false;
    });
    window.initLocalizedDateInputs?.(document.getElementById('dashboardActionItems'));
    window.dashboardActionItems = dashboardData.actionItems || [];
    const actionCount = document.getElementById('dashboardActionCount');
    if (actionCount) actionCount.textContent = String((dashboardData.actionItems || []).length);
    setList(
      'todayClassesList',
      window.getDashboardTodayClassesHTML?.(dashboardData.todayClasses) || ''
    );
    setList('urgentItemsList', window.getDashboardUrgentHTML?.(dashboardData.urgentItems) || '');
    setList(
      'recentPaymentsList',
      window.getDashboardPaymentsHTML?.(dashboardData.recentPayments) || ''
    );
    setList(
      'recentDepositsList',
      window.getDashboardDepositsHTML?.(dashboardData.recentDeposits) || ''
    );
    setList(
      'todayAbsencesList',
      window.getDashboardAbsencesHTML?.(dashboardData.todayAbsences) || ''
    );
    setList(
      'unreadMessagesList',
      window.getDashboardMessagesHTML?.(dashboardData.unreadMessages) || ''
    );
    setList(
      'recentRegistrationsList',
      window.getDashboardRegistrationsHTML?.(dashboardData.recentRegistrations) || ''
    );
    setList(
      'upcomingHolidaysList',
      window.getDashboardHolidaysHTML?.(dashboardData.upcomingHolidays) || ''
    );
    setList('dashboardQuickLinks', window.getDashboardQuickLinksHTML?.() || '');
  };

  window.refreshDashboard = async function () {
    if (loading) return;
    loading = true;
    const refreshIcon = document.querySelector('#dashboard button[onclick="refreshDashboard()"] i');
    refreshIcon?.classList.add('fa-spin');
    try {
      dashboardData = await requestDashboard();
      if (
        document.getElementById('terms') &&
        !window.termPermissions &&
        typeof window.loadTerms === 'function'
      )
        await window.loadTerms();
      if (currentDashboardBranch === 'academy') {
        [
          'todayClasses',
          'actionItems',
          'urgentItems',
          'recentPayments',
          'recentDeposits',
          'todayAbsences',
          'unreadMessages',
          'recentRegistrations',
          'upcomingHolidays',
        ].forEach((key) => {
          dashboardData[key] = (dashboardData[key] || []).filter((item) =>
            window.matchesOrganizationFilter(item, 'academy')
          );
        });
        dashboardData.stats = {
          ...(dashboardData.stats || {}),
          activeStudents: 0,
          todayClasses: 0,
          monthlyIncome: '0',
          attendanceRate: '۰٪',
          pendingPayments: 0,
          absencesToday: 0,
          newMessages: dashboardData.unreadMessages.length,
          urgentAlerts: dashboardData.urgentItems.length,
          newStudentsWeek: 0,
          pointsAwarded: 0,
        };
      }
      window.renderDashboardBranchTabs();
      window.renderDashboard();
    } catch (error) {
      alert(error.message || 'بارگذاری داشبورد ناموفق بود.');
    } finally {
      loading = false;
      refreshIcon?.classList.remove('fa-spin');
      if (pendingDashboardBranch !== null) {
        pendingDashboardBranch = null;
        window.refreshDashboard();
      }
    }
  };

  window.openDashboardHolidayConflict = async function (termId, sessionId) {
    if (typeof window.loadTerms === 'function') await window.loadTerms();
    await window.openTermSessionCancellation?.(termId, sessionId);
    const item = (window.dashboardActionItems || []).find(
        (row) =>
          row.type === 'national_holiday_conflict' &&
          row.termId === termId &&
          row.sessionId === sessionId
      ),
      reason = document.getElementById('termCancellationReason');
    if (reason && item) {
      const description = String(item.holidayDescription || '').trim();
      reason.value = `تعطیلی رسمی «${item.holidayTitle}» در تاریخ ${window.formatLocalizedDate?.(item.date) || item.date}${description ? ' — ' + description : ''}`;
      reason.dispatchEvent(new Event('input', { bubbles: true }));
    }
  };
  window.decideDashboardCancellation = async function (termId, sessionId, approve) {
    await window.decideTermSessionCancellation?.(termId, sessionId, approve);
    window.closeModal?.();
    await window.refreshDashboard();
  };

  window.openWaitingConversation = async function (id) {
    window.goToSectionFromDashboard('chat');
    await window.chatReady;
    await window.reloadChat?.();
    await window.openChat?.(Number(id));
  };

  window.startWaitingConversation = async function (requestId, conversationId) {
    try {
      if (!conversationId) {
        const body = new URLSearchParams({_token: window.adminCsrfToken || ''});
        const response = await fetch('/analytics/admin-dashboard/waiting/' + Number(requestId) + '/chat', {method:'POST', credentials:'same-origin', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':window.adminCsrfToken||''}, body});
        const payload = await response.json(), result = payload.data ?? payload;
        if (!response.ok || result.success === false) throw new Error(result.message || 'ساخت گفتگو ناموفق بود.');
        conversationId = Number((result.data ?? result).conversationId);
      }
      await window.openWaitingConversation(conversationId);
    } catch (error) { alert(error.message || 'گفتگو باز نشد.'); }
  };

  window.refreshWaitingTimes = async function (id) {
    const requestId = Number(id);
    const item = (dashboardData?.actionItems || []).find((row) => row.type === 'student_pending_schedule' && row.requestId === requestId);
    const teacher = Number(document.getElementById('waitingTeacher-' + requestId)?.value || 0);
    const date = document.getElementById(item?.needsFirstDate ? 'waitingFirstDate-' + requestId : 'waitingDay-' + requestId)?.value || '';
    const select = document.getElementById('waitingStart-' + requestId);
    if (!item || !select) return;
    const serial = String(Number(select.dataset.serial || 0) + 1);
    select.dataset.serial = serial;
    select.disabled = true;
    select.innerHTML = '<option value="">ابتدا مدرس و روز را انتخاب کنید</option>';
    const weekday = document.getElementById('waitingWeekday-' + requestId);
    if (weekday && date) {
      const names = ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'];
      weekday.textContent = `${names[new Date(date + 'T12:00:00').getDay()]} · ${Number(item.sessionCount)} جلسه · تاریخ‌های بعدی از دورهٔ تکرار ترم ساخته می‌شوند.`;
    }
    if (!teacher || !date) return;
    const daySessions = (item.sessions || []).filter((session) => session.date === date);
    const firstSession = daySessions[0];
    const duration = item.needsFirstDate ? Number(item.durationMinutes) : firstSession ? Math.max(5, (Number(firstSession.endTime?.slice(0, 2)) * 60 + Number(firstSession.endTime?.slice(3, 5))) - (Number(firstSession.startTime?.slice(0, 2)) * 60 + Number(firstSession.startTime?.slice(3, 5)))) : 0;
    const classroom = Number(firstSession?.classroomId || item.classroomId || 0);
    if (!duration || !classroom || !item.branchId) return;
    select.innerHTML = '<option value="">در حال دریافت ساعت‌های مدرس...</option>';
    try {
      const query = new URLSearchParams({ branch: item.branchId, classroom, date, duration, teacher, term: item.termId, excludeTerm: item.termId, timezoneId: item.draftTimezoneId || 0 });
      const response = await fetch('/academy/admin/term-available-times?' + query, { credentials: 'same-origin', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
      const payload = await response.json();
      const envelope = payload.data ?? payload;
      if (!response.ok || envelope.success === false) throw new Error(envelope.message || 'ساعت‌های مدرس بارگذاری نشد.');
      const available = (envelope.data ?? envelope).times || [];
      if (!select.isConnected || select.dataset.serial !== serial) return;
      const options = item.needsFirstDate
        ? available.map((time) => ({ value: time, label: time }))
        : daySessions.filter((session) => available.includes(session.startTime)).map((session) => ({ value: String(session.id), label: `${session.startTime} تا ${session.endTime}` }));
      select.innerHTML = '<option value="">انتخاب ساعت</option>' + options.map((option) => `<option value="${option.value}">${option.label}</option>`).join('');
      if (!options.length) select.innerHTML = '<option value="">در این روز ساعت آزاد برای مدرس وجود ندارد</option>';
      select.disabled = !options.length;
    } catch (error) {
      if (!select.isConnected || select.dataset.serial !== serial) return;
      select.innerHTML = '<option value="">دریافت ساعت‌های مدرس ناموفق بود</option>';
      select.title = error.message || 'خطا در دریافت ساعت‌ها';
    }
  };

  window.finalizeWaiting = async function (id) {
    const teacherId = Number(document.getElementById('waitingTeacher-' + Number(id))?.value || 0);
    const item = (dashboardData?.actionItems || []).find((row) => row.type === 'student_pending_schedule' && row.requestId === Number(id));
    const chosen = document.getElementById('waitingStart-' + Number(id))?.value || '';
    const sessionId = item?.needsFirstDate ? 0 : Number(chosen || 0);
    const firstDate = item?.needsFirstDate ? document.getElementById('waitingFirstDate-' + Number(id))?.value || '' : '';
    const startTime = item?.needsFirstDate ? chosen : '';
    if (!teacherId || !chosen || (item?.needsFirstDate && !firstDate)) return alert('مدرس، روز و ساعت برگزاری را انتخاب کنید.');
    if (!(await AppDialog.confirm('پس از توافق با هنرجو، کلاس فعال و فاکتور شهریه صادر شود؟'))) return;
    try {
      const body = new URLSearchParams({_token: window.adminCsrfToken || '', teacher_id: String(teacherId), session_id: String(sessionId), first_date: firstDate, start_time: startTime});
      const response = await fetch('/analytics/admin-dashboard/waiting/' + Number(id) + '/finalize', {method:'POST', credentials:'same-origin', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':window.adminCsrfToken||''}, body});
      const payload = await response.json(), result = payload.data ?? payload;
      if (!response.ok || result.success === false) throw new Error(result.message || 'فعال‌سازی انجام نشد.');
      await window.refreshDashboard();
      alert('کلاس فعال شد و فاکتور شهریه صادر شد.');
    } catch (error) { alert(error.message || 'فعال‌سازی انجام نشد.'); }
  };

  window.openWaitingApproval = function (id) {
    const item = (window.dashboardActionItems || []).find((row) => row.type === 'student_waiting' && row.requestId === Number(id));
    if (!item) return alert('درخواست را دوباره بارگذاری کنید.');
    const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const field = (key, label, value, type = 'text') => `<label class="block text-sm"><span class="mb-1 block font-medium">${label} *</span><input name="${key}" type="${type}" value="${esc(value)}" required class="w-full rounded-xl border p-3"></label>`;
    const container = document.getElementById('modalContainer');
    if (!container) return;
    container.innerHTML = `<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" onclick="if(event.target===this) closeModal()"><div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 shadow-xl" onclick="event.stopPropagation()"><div class="mb-4 flex items-center justify-between"><h2 class="text-xl font-bold">تکمیل ثبت‌نام ${esc(item.studentName)}</h2><button type="button" onclick="closeModal()" class="text-2xl">×</button></div><p class="mb-5 text-sm text-gray-600">${esc(item.termName)} · پس از تأیید، عضویت هنرجو ثبت می‌شود و زمان و مدرس باید در گفتگوی هماهنگی نهایی شود.</p><form id="waitingApprovalForm" onsubmit="submitWaitingApproval(event,${Number(id)})" class="grid gap-4 sm:grid-cols-2">${field('name','نام و نام خانوادگی',item.studentName)}${field('national_id','کد ملی',item.nationalId)}${field('father_name','نام پدر',item.fatherName)}${field('birth_date','تاریخ تولد',item.birthDate,'date')}${field('phone','شماره تماس',item.phone,'tel')}<label class="block text-sm sm:col-span-2"><span class="mb-1 block font-medium">آدرس *</span><textarea name="address" required class="w-full rounded-xl border p-3">${esc(item.address)}</textarea></label><div class="flex flex-wrap gap-2 sm:col-span-2"><button type="submit" class="rounded-xl bg-emerald-600 px-5 py-3 text-white">تأیید و ثبت هنرجو</button><button type="button" onclick="closeModal()" class="rounded-xl border px-5 py-3">انصراف</button></div></form></div></div>`;
  };

  window.submitWaitingApproval = async function (event, id) {
    event.preventDefault();
    const form = event.currentTarget;
    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;
    try {
      const body = new URLSearchParams(new FormData(form));
      body.set('_token', window.adminCsrfToken || '');
      const response = await fetch('/analytics/admin-dashboard/waiting/' + Number(id) + '/approve', { method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': window.adminCsrfToken || '' }, body });
      const payload = await response.json();
      const result = payload.data ?? payload;
      if (!response.ok || result.success === false) throw new Error(result.message || 'ثبت درخواست ناموفق بود.');
      window.closeModal?.();
      await window.refreshDashboard();
      alert('هنرجو ثبت شد. تا زمان نهایی‌شدن مدرس و ساعت، وضعیت آموزشی او در انتظار برنامه‌ریزی است.');
    } catch (error) {
      alert(error.message || 'ثبت درخواست ناموفق بود.');
      button.disabled = false;
    }
  };

  setTimeout(() => {
    if (document.getElementById('dashboard')?.dataset.dashboardKind !== 'student' && document.getElementById('dashboard')) window.refreshDashboard();
  }, 200);
  const dashboardHolidayChannel =
    'BroadcastChannel' in window ? new BroadcastChannel('sornaz-national-holidays') : null;
  const refreshForHolidayChange = () => {
    if (
      document.getElementById('dashboard')?.dataset.dashboardKind !== 'student' &&
      document.getElementById('dashboard') &&
      !document.getElementById('dashboard').classList.contains('hidden')
    )
      window.refreshDashboard();
  };
  window.addEventListener('sornaz:data-changed', (event) => {
    if (event.detail?.resource === 'national_holidays') refreshForHolidayChange();
  });
  if (dashboardHolidayChannel)
    dashboardHolidayChannel.onmessage = (event) => {
      if (event.data?.resource === 'national_holidays') refreshForHolidayChange();
    };
  setInterval(refreshForHolidayChange, 15000);
})();
