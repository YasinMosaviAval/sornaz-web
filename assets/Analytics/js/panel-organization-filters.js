window.matchesOrganizationFilter = function (item, filter) {
  if (filter === 'all' || filter === '' || filter == null) return true;
  if (filter === 'academy')
    return (
      item?.organizationKind === 'academy' || Number(item?.branchId ?? item?.branch_id ?? -1) === 0
    );
  const branchId = item?.branchId ?? item?.branch_id ?? item?.branchID ?? null;
  if (branchId != null && String(branchId) === String(filter)) return true;
  if (item?.organizationUserId != null || item?.user_id != null)
    return String(item.organizationUserId ?? item.user_id) === String(filter);
  return (
    String(branchId) === String(filter) ||
    String(item?.branch || item?.branchName || '') === String(filter)
  );
};
window.academyOrganizationTabHandlers = {
  dashboardBranchTabs: 'filterDashboardByBranch',
  reportsBranchTabs: 'filterReportsByBranch',
  messagesBranchTabs: 'filterMessagesByBranch',
  notificationsBranchTabs: 'filterNotificationsByBranch',
  studentBranchTabs: 'filterStudentsByBranch',
  accessUsersBranchTabs: 'filterAccessUsersByBranch',
  classroomTypesBranchTabs: 'filterClassroomTypesByBranch',
  galleryBranchTabs: 'filterGalleryByBranch',
  classroomBranchTabs: 'filterClassroomsByBranch',
  staffBranchTabs: 'filterStaffByBranch',
  coursesBranchTabs: 'filterCoursesByBranch',
  financeBranchTabs: 'filterFinanceByBranch',
  rulesBranchTabs: 'filterRulesByBranch',
  schedulesBranchTabs: 'filterSchedulesByBranch',
  instrumentsBranchTabs: 'filterInstrumentsByBranch',
  lessonsBranchTabs: 'filterLessonsByBranch',
  memberSchedulesBranchTabs: 'filterMemberSchedulesByBranch',
  branchSchedulesBranchTabs: 'filterBranchSchedulesByBranch',
  holidayLeavesBranchTabs: 'filterHolidayLeavesByBranch',
  pointsBranchTabs: 'filterPointsByBranch',
  termsBranchTabs: 'filterTermsByBranch',
  termDiscountsBranchTabs: 'filterTermDiscountsByBranch',
  approvalsBranchTabs: 'filterApprovalsByBranch',
  badgesBranchTabs: 'filterBadgesByBranch',
  eventsBranchTabs: 'filterEventsByBranch',
  educationsBranchTabs: 'filterEducationsByBranch',
  favoritesBranchTabs: 'filterFavoritesByBranch',
  profilesBranchTabs: 'filterProfilesByBranch',
  publicationsBranchTabs: 'filterPublicationsByBranch',
  ratingsBranchTabs: 'filterRatingsByBranch',
  ratingSummariesBranchTabs: 'filterRatingSummariesByBranch',
  pollsBranchTabs: 'filterPollsByBranch',
  awardsBranchTabs: 'filterAwardsByBranch',
  certificatesBranchTabs: 'filterCertificatesByBranch',
  experiencesBranchTabs: 'filterExperiencesByBranch',
  contractsBranchTabs: 'filterContractsByBranch',
};
(function () {
  'use strict';
  const filters = new Map();
  // Organization controls remain in the form for its existing submission contract.
  // The trusted page capability also covers employees whose branch bootstrap is delayed.
  window.applyPanelOrganizationFormScope = function (root = document) {
    const fixed = !window.branchSiteAdmin && (window.panelBranchContext === true || window.branchAccount === true);
    const fields = root.matches?.('[data-panel-organization-field]') ? [root] : [];
    fields.push(...root.querySelectorAll('[data-panel-organization-field]'));
    fields.forEach(select => {
      const shell = select.closest('label') || select.parentElement;
      if (!shell) return;
      if (!fixed) {
        if (shell.dataset.panelFixedOrganization === '1') {
          shell.hidden = false;
          shell.style.removeProperty('display');
          shell.removeAttribute('aria-hidden');
          delete shell.dataset.panelFixedOrganization;
        }
        return;
      }
      shell.dataset.panelFixedOrganization = '1';
      shell.hidden = true;
      shell.style.setProperty('display', 'none', 'important');
      shell.setAttribute('aria-hidden', 'true');
      const choices = [...select.options].filter(option => option.value && !['all', 'academy'].includes(option.value) && !option.disabled);
      const branch = window.branchAccount && window.academyPanelBranches?.length === 1 ? window.academyPanelBranches[0] : null;
      const ownValue = select.dataset.panelOrganizationValue === 'branch' ? branch?.id : branch?.user_id;
      const ownChoice = ownValue != null ? choices.find(option => option.value === String(ownValue)) : null;
      // Keep branch IDs distinct from organization account IDs; never submit a placeholder.
      const fixedChoice = ownChoice || (choices.length === 1 ? choices[0] : null);
      if (fixedChoice && select.value !== fixedChoice.value) {
        select.value = fixedChoice.value;
        select.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
  };
  const observeOrganizationForms = () => {
    window.applyPanelOrganizationFormScope();
    new MutationObserver(records => {
      records.forEach(record => {
        if (record.target.closest?.('[data-panel-organization-field]')) {
          window.applyPanelOrganizationFormScope(record.target.closest('[data-panel-organization-field]'));
        }
        record.addedNodes.forEach(node => {
          if (node.nodeType === 1) window.applyPanelOrganizationFormScope(node);
        });
      });
    }).observe(document.body, { childList: true, subtree: true });
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', observeOrganizationForms);
  else observeOrganizationForms();
  window.addEventListener('academy-data-loaded', () => window.applyPanelOrganizationFormScope());

  window.syncPanelOrganizationSelect = (select, rows, valueKey = 'id') => {
    if (!select || !Array.isArray(rows)) return;
    const selected = select.value;
    const first = select.options[0];
    const placeholder = first && ['', 'all'].includes(first.value) ? new Option(first.textContent, first.value) : null;
    select.replaceChildren(...(placeholder ? [placeholder] : []), ...rows.map(row => new Option(row.name, row[valueKey])));
    select.value = [...select.options].some(option => option.value === selected) ? selected : '';
    if (select.value !== selected) select.dispatchEvent(new Event('change', { bubbles: true }));
  };
  window.panelBranchFilterShell = (container) =>
    container.closest('[data-panel-organization-shell]') ||
    container.closest('.admin-ui-organization-tabs') ||
    (container.parentElement?.children.length === 1 ? container.parentElement : container);

  window.renderPanelOrganizationTabs = function (id, selected, onSelect, config = {}) {
    const container = document.getElementById(id);
    if (!container) return;
    filters.set(id, { selected, onSelect, config });
    container.dataset.selectedValue = String(selected);
    const source = typeof config.branches === 'function' ? config.branches() :
      (config.branches || window.academyPanelBranches || []);
    const branches = source.filter((branch) => branch.kind !== 'academy').sort((a, b) => {
      const first = a.createdAt || a.created_at, second = b.createdAt || b.created_at;
      return first && second && first !== second ? String(first).localeCompare(String(second)) : Number(a.branch_id ?? a.branchId ?? a.id) - Number(b.branch_id ?? b.branchId ?? b.id);
    });
    const english = document.documentElement.lang === 'en';
    const options = [{ value: 'all', label: english ? 'All' : 'همه' },
      ...((config.showAcademy ?? !window.branchSiteAdmin) ? [{ value: 'academy', label: english ? 'Academy' : 'آموزشگاه' }] : []),
      ...(config.options || branches.map((branch) => ({ value: String(config.branchValue ? config.branchValue(branch) : branch.id), label: branch.name })))];
    if (!options.some(option => String(option.value) === String(selected))) {
      selected = 'all';
      filters.set(id, { selected, onSelect, config });
      container.dataset.selectedValue = selected;
      Promise.resolve(onSelect(selected)).catch(console.error);
    }
    container.classList.add('organization-filter-tabs', 'flex', 'gap-2');
    container.replaceChildren();
    new Map(options.map((option) => [option.value, option])).forEach(({ value, label }) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.dataset.value = value;
      button.dataset.staffOrganization = value;
      button.textContent = label;
      button.className = (config.tabClass || '') + ' rounded-2xl border px-5 py-2.5 text-sm font-medium transition ' +
        (String(selected) === value ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200');
      button.onclick = async () => {
        window.renderPanelOrganizationTabs(id, value, onSelect, config);
        await onSelect(value);
        if (container.dataset.selectedValue === value) {
          window.renderPanelOrganizationTabs(id, value, onSelect, config);
        }
      };
      container.appendChild(button);
    });
    window.applyAcademyOrganizationTabs();
  };

  window.applyAcademyOrganizationTabs = function () {
    document.querySelectorAll('[id$="BranchTabs"], [data-profile-organization-tabs], .gallery-owner-tabs').forEach((container) => {
      if (typeof window.academyHasNoBranches !== 'boolean') return;
      const shell = window.panelBranchFilterShell(container);
      if (window.academyHasNoBranches || window.branchAccount) {
        shell.classList.add('hidden');
        shell.setAttribute('aria-hidden', 'true');
        shell.dataset.academyBranchlessTabs = '1';
      } else if (shell.dataset.academyBranchlessTabs === '1') {
        shell.classList.remove('hidden');
        shell.removeAttribute('aria-hidden');
        delete shell.dataset.academyBranchlessTabs;
      }
    });
    document.getElementById('accessUserBranch')?.classList.add('hidden');
  };
  window.addEventListener('academy-data-loaded', () => {
    filters.forEach(({ selected, onSelect, config }, id) => {
      window.renderPanelOrganizationTabs(id, selected, onSelect, config);
    });
    window.renderAllGallerySections?.();
    window.applyAcademyOrganizationTabs();
  });
  let previousOrganizations;
  let refreshSequence = 0;
  window.addEventListener('academy-data-loaded', async event => {
    const signature = JSON.stringify([event.detail.branches, event.detail.academies, event.detail.organization_revision]);
    const changed = previousOrganizations !== undefined && signature !== previousOrganizations;
    previousOrganizations = signature;
    if (!changed) return;
    const sequence = ++refreshSequence;
    const jobs = [
      () => window.loadTerms?.(),
      () => window.loadCourses?.(),
      () => typeof loadClassrooms === 'function' && loadClassrooms(),
      () => window.loadBranchOfferings?.(true),
      () => window.ensureStudentFormOptions?.(true),
      () => (document.getElementById('termDiscountsTable') || document.getElementById('newTermDiscountOrganization')) && window.loadTermDiscountCatalog?.(),
      () => document.getElementById('financeTable') && typeof loadFinanceInvoices === 'function' && loadFinanceInvoices(),
      () => window.loadMemberSchedules?.(),
      () => window.loadHolidayLeaves?.(),
      () => window.loadAdminNotifications?.(),
      () => window.refreshDashboard?.(),
    ];
    // Independent panels must not wait for an unrelated slow request.
    window.dispatchEvent(new CustomEvent('panel-organizations-changed'));
    const syncOpenSelectors = () => {
      if (sequence !== refreshSequence) return;
      const sources = [
        [/staff.*Organization/i, window.staffCatalog?.organizations],
        [/^(?:stu|editStu|inlineStu|student).*Organization/i, window.studentOrganizations],
        [/classroom.*Organization/i, window.classroomOrganizations],
        [/course.*Organization/i, window.courseOrganizations],
        [/^(term|editTerm|inlineTerm).*Branch$/, window.termBranches],
        [/^(ms|editMs|inlineMs).*Branch$/, window.getMemberScheduleBranches?.()],
        [/^(hl|editHl|inlineHl).*Branch$/, window.getHolidayLeaveBranches?.()],
      ];
      document.querySelectorAll('select').forEach(select => {
        const catalogs = {
          branches: () => window.academyPanelBranches,
          workingHours: () => window.getBranchScheduleOrganizations?.(),
          lessonOrganizations: () => window.branchOfferingData?.organizations?.filter(row => !row.read_only),
          roles: () => window.getRoleBranches?.(),
          permissions: () => window.getPermissionBranches?.(),
          users: () => window.getUserBranches?.(),
          academies: () => typeof allBranchAcademies !== 'undefined' ? allBranchAcademies : [],
          discountOrganizations: () => window.termDiscountOrganizations,
        };
        const explicit = catalogs[select.dataset.panelOrganizationSource]?.();
        if (Array.isArray(explicit)) {
          window.syncPanelOrganizationSelect(select, explicit.map(row => ({id: ['lessonOrganizations', 'discountOrganizations'].includes(select.dataset.panelOrganizationSource) ? row.user_id : row.id, name: row.name})));
          return;
        }
        const source = sources.find(([pattern, rows]) => pattern.test(select.id) && Array.isArray(rows))?.[1];
        if (!source) return;
        window.syncPanelOrganizationSelect(select, source.map(row => ({ id: row.user_id ?? row.id, name: row.name })));
      });
      document.querySelectorAll('#modalContainer select[name="organizationUserId"]:not([data-panel-organization-source])').forEach(select => window.syncPanelOrganizationSelect(select, window.termBranches || []));
    };
    syncOpenSelectors();
    const results = await Promise.allSettled(jobs.map(async job => {
      await job();
      syncOpenSelectors();
    }));
    results.forEach(result => { if (result.status === 'rejected') console.error(result.reason); });
  });
})();
