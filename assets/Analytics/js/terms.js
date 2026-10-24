// ==================== لیست‌های قابل گسترش ====================
let allTermCurrencies = [
  { id: 1, name: 'تومان' },
  { id: 2, name: 'دلار' },
  { id: 3, name: 'یورو' },
];
let allTermDiscounts = [
  { id: 1, name: 'بدون تخفیف' },
  { id: 2, name: '۱۰٪' },
  { id: 3, name: '۲۰٪' },
  { id: 4, name: '۳۰٪' },
  { id: 5, name: 'ویژه' },
];
let allTermCourseOptions = [
  { id: 1, name: 'دوره پیانو مبتدی' },
  { id: 2, name: 'دوره گیتار متوسط' },
  { id: 3, name: 'دوره ویولن پیشرفته' },
  { id: 4, name: 'دوره آواز کلاسیک' },
  { id: 5, name: 'دوره درام کودکان' },
  { id: 6, name: 'دوره سنتور' },
  { id: 7, name: 'دوره تئوری موسیقی' },
  { id: 8, name: 'دوره کمانچه' },
];
let allTermClassroomOptions = [
  { id: 1, name: 'کلاس پیانو ۱' },
  { id: 2, name: 'کلاس گیتار A' },
  { id: 3, name: 'سالن تمرین گروهی' },
  { id: 4, name: 'کلاس ویولن' },
  { id: 5, name: 'کلاس آواز' },
  { id: 6, name: 'کلاس عمومی' },
];
let allTermTeacherOptions = [
  { id: 1, name: 'علی رضایی' },
  { id: 2, name: 'سارا موسوی' },
  { id: 3, name: 'رضا کریمی' },
  { id: 4, name: 'مینا احمدی' },
  { id: 5, name: 'حسین مهدوی' },
];
let allTermStudentOptions = [
  { id: 1, name: 'محمد نوری' },
  { id: 2, name: 'زهرا حسینی' },
  { id: 3, name: 'امیر جعفری' },
  { id: 4, name: 'نگار کاظمی' },
  { id: 5, name: 'پارسا محمدی' },
  { id: 6, name: 'هستی بهرامی' },
  { id: 7, name: 'کیان احمدی' },
  { id: 8, name: 'آوا موسوی' },
];

const termStatuses = ['در حال برگزاری', 'در انتظار', 'پایان‌یافته', 'تعلیق‌شده'];
const termNameTemplates = [
  'ترم پاییز',
  'ترم زمستان',
  'ترم بهار',
  'ترم تابستان',
  'ترم فشرده',
  'ترم ویژه نوروز',
  'ترم پیشرفته',
  'ترم مبتدی',
];

function getTermBranches() {
  return typeof allBranches !== 'undefined' ? allBranches.filter((branch) => branch.kind !== 'academy') : [];
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function buildOptionMarkup(options, selectedValue) {
  return (options || [])
    .map(function (option) {
      const value = option.value ?? option.id ?? option.name ?? option;
      const label = option.label ?? option.name ?? option;
      const isSelected = String(value) === String(selectedValue) ? 'selected' : '';
      return (
        '<option value="' +
        escapeHtml(value) +
        '" ' +
        isSelected +
        '>' +
        escapeHtml(label) +
        '</option>'
      );
    })
    .join('');
}

function getTermPrefixFromContainer(containerId) {
  if (!containerId) return '';
  return containerId.replace(/(TeachersContainer|StudentsContainer|InstallmentsContainer)$/, '');
}

function getTermCourseCapacityFromContext(prefix) {
  const courseField = document.getElementById(prefix ? prefix + 'Course' : 'termCourse');
  const courseId = courseField ? courseField.value : '';
  return window.getTermCourseCapacity(courseId);
}

window.getTermCourseOptions = function () {
  if (typeof allCourses !== 'undefined' && allCourses.length) {
    return allCourses.map(function (c) {
      return { value: c.id, label: c.name, id: c.id, name: c.name };
    });
  }
  return allTermCourseOptions.map(function (c) {
    return { value: c.id, label: c.name, id: c.id, name: c.name };
  });
};

window.getTermClassroomOptions = function () {
  if (typeof allClassrooms !== 'undefined' && allClassrooms.length) {
    return allClassrooms.map(function (c) {
      return { value: c.id, label: c.name, id: c.id, name: c.name };
    });
  }
  return allTermClassroomOptions.map(function (c) {
    return { value: c.id, label: c.name, id: c.id, name: c.name };
  });
};

window.getTermTeacherOptions = function () {
  if (typeof allStaff !== 'undefined' && allStaff.length) {
    return allStaff
      .filter(function (s) {
        return s.type === 'teacher' || !s.type;
      })
      .map(function (s) {
        return { value: s.id, label: s.name, id: s.id, name: s.name };
      });
  }
  return allTermTeacherOptions.map(function (t) {
    return { value: t.id, label: t.name, id: t.id, name: t.name };
  });
};

window.getTermStudentOptions = function () {
  if (typeof allStudents !== 'undefined' && allStudents.length) {
    return allStudents.map(function (s) {
      return { value: s.id, label: s.name, id: s.id, name: s.name };
    });
  }
  return allTermStudentOptions.map(function (s) {
    return { value: s.id, label: s.name, id: s.id, name: s.name };
  });
};

function randomDateISO(startYear, endYear) {
  const start = new Date(startYear, 0, 1).getTime();
  const end = new Date(endYear, 11, 31).getTime();
  const d = new Date(start + Math.random() * (end - start));
  return d.toISOString().split('T')[0];
}

function buildSessions(count) {
  const sessions = [];
  let base = new Date(2024, Math.floor(Math.random() * 8), 1 + Math.floor(Math.random() * 20));
  for (let i = 0; i < count; i++) {
    const d = new Date(base.getTime() + i * 7 * 24 * 60 * 60 * 1000);
    sessions.push({ date: d.toISOString().split('T')[0] });
  }
  return sessions;
}

function pickN(arr, n) {
  const shuffled = arr.slice().sort(function () {
    return Math.random() - 0.5;
  });
  return shuffled.slice(0, n).map(function (x) {
    return { id: x.id, name: x.name };
  });
}

// ==================== ۴۰ ترم نمونه ====================
const termCourseCapacities = { 1: 8, 2: 10, 3: 12, 4: 6, 5: 7, 6: 9, 7: 8, 8: 10 };
window.getTermCourseCapacity = function (courseId) {
  if (!courseId && courseId !== 0) return 8;
  const normalized = Number(courseId);
  return termCourseCapacities[normalized] || 8;
};

window.updateTermCourseCapacityHint = async function (prefix) {
  const courseField = document.getElementById(prefix ? prefix + 'Course' : 'termCourse');
  const hint = document.getElementById(
    prefix ? prefix + 'CourseCapacityHint' : 'termCourseCapacityHint'
  );
  if (!hint) return;
  const courseId = courseField ? courseField.value : '';
  const capacity = window.getTermCourseCapacity(courseId);
  hint.textContent = `ظرفیت هنرجویان این دوره ${capacity} نفر است`;
  window.refreshTermStudentFieldLimit(prefix);
  window.refreshTermStudentSelectionOptions(
    prefix ? prefix + 'StudentsContainer' : 'termStudentsContainer'
  );
};

window.syncTermInstallments = async function (prefix) {
  const costField = document.getElementById(prefix ? prefix + 'Cost' : 'termCost');
  const container = document.getElementById(
    prefix ? prefix + 'InstallmentsContainer' : 'termInstallmentsContainer'
  );
  if (!costField || !container) return;
  const cost = Number(costField.value || 0);
  const items = container.querySelectorAll('.term-installment-item');
  if (!items.length) return;
};

window.refreshTermSelectionOptionsForInput = async function (selectEl) {
  if (!selectEl) return;
  const containerEl = selectEl.closest('[id$="TeachersContainer"], [id$="StudentsContainer"]');
  if (!containerEl) return;
  if (containerEl.id.endsWith('TeachersContainer')) {
    window.refreshTermTeacherSelectionOptions(containerEl.id);
  } else if (containerEl.id.endsWith('StudentsContainer')) {
    window.refreshTermStudentSelectionOptions(containerEl.id);
  }
};

window.refreshTermTeacherSelectionOptions = async function (containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const selects = Array.from(container.querySelectorAll('.term-teacher-select'));
  const options =
    typeof window.getTermTeacherOptions === 'function' ? window.getTermTeacherOptions() : [];
  selects.forEach(function (selectEl) {
    const currentValue = selectEl.value;
    const usedValues = selects
      .filter(function (item) {
        return item !== selectEl && item.value;
      })
      .map(function (item) {
        return String(item.value);
      });
    const filtered = options.filter(function (option) {
      const optionValue = String(option.value ?? option.id ?? option.name ?? option);
      return !usedValues.includes(optionValue) || optionValue === String(currentValue);
    });
    selectEl.innerHTML =
      '<option value="">انتخاب استاد</option>' + buildOptionMarkup(filtered, currentValue);
    if (
      !filtered.some(function (option) {
        return String(option.value ?? option.id ?? option.name ?? option) === String(currentValue);
      })
    ) {
      selectEl.value = '';
    }
  });
};

window.refreshTermStudentSelectionOptions = async function (containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const selects = Array.from(container.querySelectorAll('.term-student-select'));
  const options =
    typeof window.getTermStudentOptions === 'function' ? window.getTermStudentOptions() : [];
  selects.forEach(function (selectEl) {
    const currentValue = selectEl.value;
    const usedValues = selects
      .filter(function (item) {
        return item !== selectEl && item.value;
      })
      .map(function (item) {
        return String(item.value);
      });
    const filtered = options.filter(function (option) {
      const optionValue = String(option.value ?? option.id ?? option.name ?? option);
      return !usedValues.includes(optionValue) || optionValue === String(currentValue);
    });
    selectEl.innerHTML =
      '<option value="">انتخاب هنرجو</option>' + buildOptionMarkup(filtered, currentValue);
    if (
      !filtered.some(function (option) {
        return String(option.value ?? option.id ?? option.name ?? option) === String(currentValue);
      })
    ) {
      selectEl.value = '';
    }
  });
};

window.refreshTermStudentFieldLimit = async function (prefix) {
  const containerId = prefix ? prefix + 'StudentsContainer' : 'termStudentsContainer';
  const container = document.getElementById(containerId);
  if (!container) return;
  const capacity = getTermCourseCapacityFromContext(prefix);
  const currentCount = container.querySelectorAll('.term-student-item').length;
  const buttons = Array.from(document.querySelectorAll('button')).filter(function (btn) {
    return (
      btn.getAttribute('onclick') &&
      btn.getAttribute('onclick').indexOf("addTermStudentField('" + containerId + "'") !== -1
    );
  });
  buttons.forEach(function (btn) {
    btn.disabled = currentCount >= capacity;
  });
};

function buildInstallmentsFromCount(totalCost, count) {
  const safeCount = Math.max(1, parseInt(count || '1', 10) || 1);
  const safeCost = Number(totalCost || 0);
  const base = Math.floor(safeCost / safeCount);
  const remainder = safeCost % safeCount;
  return Array.from({ length: safeCount }, function (_, index) {
    return { amount: index === safeCount - 1 ? base + remainder : base };
  });
}

function validateTermData(data) {
  if (!data.name) {
    alert('نام ترم الزامی است');
    return false;
  }
  if (data.undatedTemplate && !data.sessions?.[0]?.startTime) {
    alert('ابتدا روز برگزاری و ساعت شروع معتبر را انتخاب کنید.');
    return false;
  }
  if (data.undatedTemplate && !data.teachers?.length) {
    alert('برای ترم باز، دست‌کم یک مدرس از فهرست مدرسان همان شعبه و درس انتخاب کنید.');
    return false;
  }
  if (data.undatedTemplate && !['week', '2-week', '3-week', '4-week', 'month', 'year', 'no-period'].includes(data.repeatType)) {
    alert('دورهٔ تکرار را دوباره انتخاب کنید.');
    return false;
  }
  if (data.undatedTemplate && data.repeatType === 'no-period' && data.sessionCount !== 1) {
    alert('برای «سایر (تاریخ دقیق)» تعداد جلسات باید یک باشد.');
    return false;
  }
  const teacherIds = (data.teachers || [])
    .map(function (teacher) {
      return String(teacher && teacher.id ? teacher.id : '');
    })
    .filter(Boolean);
  if (teacherIds.length !== new Set(teacherIds).size) {
    alert('هر استاد فقط یک بار قابل انتخاب است');
    return false;
  }
  const studentIds = (data.students || [])
    .map(function (student) {
      return String(student && student.id ? student.id : '');
    })
    .filter(Boolean);
  if (studentIds.length !== new Set(studentIds).size) {
    alert('هر هنرجو فقط یک بار قابل انتخاب است');
    return false;
  }
  const courseCapacity = window.getTermCourseCapacity(data.courseId);
  if (data.students.length > courseCapacity) {
    alert('تعداد هنرجویان برای این دوره بیش از ظرفیت مجاز است');
    return false;
  }
  return true;
}

let allTerms = [];

// ==================== state ====================
let termsCurrentPage = 1;
const termsPerPage = 10;
let filteredTerms = allTerms.slice();
let currentTermBranch = 'all';
let editingTermRowId = null;
let attendanceTermRowId = null;
let termsRealtimeVersion = null;
let termsRealtimeBusy = false;
let termSortField = '';
let termSortDirection = 'asc';

function closeTermInlineEdit(render = true) {
  if (editingTermRowId === null) return false;
  editingTermRowId = null;
  if (render) renderTermsTable(filteredTerms);
  return true;
}
function closeTermInlineAttendance(render = true) {
  if (attendanceTermRowId === null) return false;
  attendanceTermRowId = null;
  if (render) renderTermsTable(filteredTerms);
  return true;
}
async function pollTermsRealtime() {
  if (
    termsRealtimeBusy ||
    document.hidden ||
    (!document.getElementById('termsTable') && !document.getElementById('financeTable'))
  )
    return;
  termsRealtimeBusy = true;
  try {
    const state = await termApi('/academy/admin/terms/realtime-version');
    if (termsRealtimeVersion === null) {
      termsRealtimeVersion = state.version;
      return;
    }
    if (state.version !== termsRealtimeVersion) {
      termsRealtimeVersion = state.version;
      editingTermRowId = null;
      attendanceTermRowId = null;
      if (document.getElementById('termsTable')) await loadTerms();
      if (document.getElementById('financeTable') && typeof loadFinanceInvoices === 'function')
        await loadFinanceInvoices();
    }
  } catch (error) {
  } finally {
    termsRealtimeBusy = false;
  }
}
if (!window.termInlineOutsideCloseBound) {
  window.termInlineOutsideCloseBound = true;
  document.addEventListener(
    'click',
    function (event) {
      if (
        (editingTermRowId === null && attendanceTermRowId === null) ||
        event.target.closest('.term-inline-expand')
      )
        return;
      const editButton = event.target.closest('[data-term-action="inline-edit"]');
      if (editButton && Number(editButton.dataset.termId) === editingTermRowId) {
        event.preventDefault();
        event.stopImmediatePropagation();
        closeTermInlineEdit();
        return;
      }
      const attendanceButton = event.target.closest('[data-term-action="inline-attendance"]');
      if (attendanceButton && Number(attendanceButton.dataset.termId) === attendanceTermRowId) {
        event.preventDefault();
        event.stopImmediatePropagation();
        closeTermInlineAttendance();
        return;
      }
      if (editingTermRowId !== null) closeTermInlineEdit();
      else closeTermInlineAttendance();
    },
    true
  );
}

const termPdfColumns = [
  { field: 'index', label: 'ردیف' },
  { field: 'name', label: 'نام ترم' },
  { field: 'branchName', label: 'شعبه' },
  { field: 'course', label: 'دوره مرتبط' },
  { field: 'start', label: 'تاریخ شروع' },
  { field: 'end', label: 'تاریخ پایان' },
  { field: 'status', label: 'وضعیت' },
  { field: 'cost', label: 'هزینه' },
];

function deriveStartEnd(sessions) {
  const dates = (sessions || [])
    .map(function (s) {
      return s.date;
    })
    .filter(Boolean)
    .sort();
  return {
    start: dates[0] || '',
    end: dates.length ? dates[dates.length - 1] : '',
  };
}

window.getTermAttendanceStats = function (item) {
  const att = item.attendance || {};
  let present = 0,
    total = 0;
  Object.keys(att).forEach(function (k) {
    const row = att[k] || {};
    Object.keys(row.teachers || {}).forEach(function (id) {
      total++;
      if (row.teachers[id]) present++;
    });
    Object.keys(row.students || {}).forEach(function (id) {
      total++;
      if (row.students[id]) present++;
    });
  });
  const rate = total ? Math.round((present / total) * 100) : 0;
  return { present: present, absent: total - present, total: total, rate: rate };
};

// ==================== sort ====================
function sortTermItems() {
  if (!termSortField) return;
  const scheduleKey = (term) => {
    const sessions = term.sessions || [], draft = term.scheduleDraft || {};
    const values = termSortField === 'days'
      ? sessions.filter(session => session.date).map(session => (new Date(session.date + 'T12:00:00Z').getUTCDay() + 1) % 7)
      : sessions.filter(session => session.startTime).map(session => session.startTime.slice(0, 5));
    if (!values.length) {
      if (termSortField === 'days' && draft.recurrence?.weekday != null) values.push((Number(draft.recurrence.weekday) + 1) % 7);
      if (termSortField === 'times' && draft.startTime) values.push(draft.startTime.slice(0, 5));
    }
    return [...new Set(values)].sort().join(',');
  };
  filteredTerms.sort(function (a, b) {
    if (termSortField === 'days' || termSortField === 'times') {
      const av = scheduleKey(a), bv = scheduleKey(b);
      if (!av || !bv) return !av === !bv ? 0 : !av ? 1 : -1;
      return av.localeCompare(bv, 'en', { numeric: true }) * (termSortDirection === 'asc' ? 1 : -1);
    }
    let aValue = a[termSortField];
    let bValue = b[termSortField];
    if (termSortField === 'cost') {
      aValue = Number(aValue) || 0;
      bValue = Number(bValue) || 0;
    } else if (termSortField === 'start' || termSortField === 'end') {
      aValue = new Date(aValue || 0);
      bValue = new Date(bValue || 0);
    } else {
      aValue = String(aValue || '').toLowerCase();
      bValue = String(bValue || '').toLowerCase();
    }
    if (aValue < bValue) return termSortDirection === 'asc' ? -1 : 1;
    if (aValue > bValue) return termSortDirection === 'asc' ? 1 : -1;
    return 0;
  });
}

window.updateTermSortIcons = async function () {
  ['name', 'branchName', 'course', 'start', 'end', 'days', 'times', 'status'].forEach(function (field) {
    const icon = document.getElementById('termSortIcon-' + field);
    if (!icon) return;
    icon.textContent = termSortField === field ? (termSortDirection === 'asc' ? '↑' : '↓') : '↕';
  });
};

window.sortTermsBy = async function (field) {
  if (termSortField === field) {
    termSortDirection = termSortDirection === 'asc' ? 'desc' : 'asc';
  } else {
    termSortField = field;
    termSortDirection = 'asc';
  }
  sortTermItems();
  renderTermsTable(filteredTerms);
  updateTermSortIcons();
};

// ==================== branch tabs ====================
window.renderTermsBranchTabs = function () {
  window.renderPanelOrganizationTabs?.('termsBranchTabs', currentTermBranch, window.filterTermsByBranch, { tabClass: 'term-branch-tab' });
};

window.filterTermsByBranch = async function (branchId) {
  currentTermBranch = branchId;
  document.querySelectorAll('.term-branch-tab').forEach(function (tab) {
    tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
    tab.classList.add('border', 'border-gray-200');
  });
  const tabs = document.querySelectorAll('.term-branch-tab');
  if (branchId === 'all' && tabs[0]) {
    tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
    tabs[0].classList.remove('border-gray-200');
  } else {
    const name = getTermBranches().find(function (b) {
      return b.id == branchId;
    });
    tabs.forEach(function (tab) {
      if (name && tab.textContent === name.name) {
        tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.remove('border-gray-200');
      }
    });
  }
  filterTerms();
};

window.renderTermCourseFilter = async function () {
  const select = document.getElementById('filterTermCourse');
  if (!select) return;
  const names = new Set(
    allTerms
      .map(function (t) {
        return t.course;
      })
      .filter(Boolean)
  );
  const current = select.value;
  select.innerHTML =
    '<option value="">همه دوره‌ها</option>' +
    Array.from(names)
      .sort()
      .map(function (n) {
        return (
          '<option value="' + n + '"' + (n === current ? ' selected' : '') + '>' + n + '</option>'
        );
      })
      .join('');
};

window.renderTermCurrencyFilter = async function () {
  const select = document.getElementById('filterTermCurrency');
  if (!select) return;
  const values = new Set(
    allTerms
      .map(function (t) {
        return t.currency;
      })
      .filter(Boolean)
  );
  const current = select.value;
  select.innerHTML =
    '<option value="">همه نوع پول</option>' +
    Array.from(values)
      .sort()
      .map(function (value) {
        return (
          '<option value="' +
          value +
          '"' +
          (value === current ? ' selected' : '') +
          '>' +
          value +
          '</option>'
        );
      })
      .join('');
};

window.renderTermDiscountFilter = async function () {
  const select = document.getElementById('filterTermDiscount');
  if (!select) return;
  const values = new Set(
    allTerms
      .map(function (t) {
        return t.discount;
      })
      .filter(Boolean)
  );
  const current = select.value;
  select.innerHTML =
    '<option value="">همه تخفیف‌ها</option>' +
    Array.from(values)
      .sort()
      .map(function (value) {
        return (
          '<option value="' +
          value +
          '"' +
          (value === current ? ' selected' : '') +
          '>' +
          value +
          '</option>'
        );
      })
      .join('');
};

window.renderTermClassroomFilter = async function () {
  const select = document.getElementById('filterTermClassroom');
  if (!select) return;
  const values = new Set(
    allTerms
      .map(function (t) {
        return t.classroom;
      })
      .filter(Boolean)
  );
  const current = select.value;
  select.innerHTML =
    '<option value="">همه کلاس‌ها</option>' +
    Array.from(values)
      .sort()
      .map(function (value) {
        return (
          '<option value="' +
          value +
          '"' +
          (value === current ? ' selected' : '') +
          '>' +
          value +
          '</option>'
        );
      })
      .join('');
};

window.renderTermInstallmentCountFilter = async function () {
  const select = document.getElementById('filterTermInstallmentCount');
  if (!select) return;
  const values = new Set(
    allTerms
      .map(function (t) {
        return t.installments && t.installments.length ? String(t.installments.length) : '1';
      })
      .filter(Boolean)
  );
  const current = select.value;
  select.innerHTML =
    '<option value="">همه تعداد اقساط</option>' +
    Array.from(values)
      .sort(function (a, b) {
        return Number(a) - Number(b);
      })
      .map(function (value) {
        return (
          '<option value="' +
          value +
          '"' +
          (value === current ? ' selected' : '') +
          '>' +
          value +
          ' قسط</option>'
        );
      })
      .join('');
};

window.renderTermFilters = async function () {
  window.renderTermCourseFilter();
  window.renderTermCurrencyFilter();
  window.renderTermDiscountFilter();
  window.renderTermClassroomFilter();
  window.renderTermInstallmentCountFilter();
};

window.filterTerms = async function () {
  const search = (
    (document.getElementById('termSearch') && document.getElementById('termSearch').value) ||
    ''
  )
    .trim()
    .toLowerCase();
  const status =
    (document.getElementById('filterTermStatus') &&
      document.getElementById('filterTermStatus').value) ||
    '';
  const course =
    (document.getElementById('filterTermCourse') &&
      document.getElementById('filterTermCourse').value) ||
    '';
  const currency =
    (document.getElementById('filterTermCurrency') &&
      document.getElementById('filterTermCurrency').value) ||
    '';
  const discount =
    (document.getElementById('filterTermDiscount') &&
      document.getElementById('filterTermDiscount').value) ||
    '';
  const classroom =
    (document.getElementById('filterTermClassroom') &&
      document.getElementById('filterTermClassroom').value) ||
    '';
  const installmentCount =
    (document.getElementById('filterTermInstallmentCount') &&
      document.getElementById('filterTermInstallmentCount').value) ||
    '';
  const weekday =
    (document.getElementById('filterTermWeekday') &&
      document.getElementById('filterTermWeekday').value) ||
    '';

  filteredTerms = allTerms.filter(function (item) {
    const matchBranch = window.matchesOrganizationFilter(item, currentTermBranch);
    const matchSearch = !search || (item.name || '').toLowerCase().includes(search);
    const matchStatus = !status || item.status === status;
    const matchCourse = !course || item.course === course;
    const matchCurrency = !currency || item.currency === currency;
    const matchDiscount = !discount || item.discount === discount;
    const matchClassroom = !classroom || item.classroom === classroom;
    const matchInstallmentCount =
      !installmentCount ||
      (item.installments && item.installments.length === Number(installmentCount));
    const matchWeekday =
      weekday === '' ||
      (item.sessions || []).some(function (session) {
        return session.date && String(new Date(session.date + 'T12:00:00').getDay()) === weekday;
      });
    return (
      matchBranch &&
      matchSearch &&
      matchStatus &&
      matchCourse &&
      matchCurrency &&
      matchDiscount &&
      matchClassroom &&
      matchInstallmentCount &&
      matchWeekday
    );
  });

  termsCurrentPage = 1;
  sortTermItems();
  renderTermsTable(filteredTerms);
};

// ==================== table ====================
window.renderTermsTable = async function (list) {
  list = list || filteredTerms;
  window.renderTermLessonChart?.(list);
  const tbody = document.querySelector('#termsTable tbody');
  if (!tbody) return;

  const totalPages = Math.ceil(list.length / termsPerPage) || 1;
  if (termsCurrentPage > totalPages) termsCurrentPage = totalPages;

  const start = (termsCurrentPage - 1) * termsPerPage;
  const end = start + termsPerPage;
  const pageItems = window.AdminPanelTable.page?.('#termsTable', list, start, end, () => window.renderTermsTable(list), undefined, row => window.getTermRowHTML(row)) ?? list.slice(start, end);

  tbody.innerHTML = '';

  if (!pageItems.length) {
    tbody.innerHTML = window.getTermEmptyRowHTML ? window.getTermEmptyRowHTML() : window.AdminPanelTable.rowHTML([{ html: "هیچ ترمی یافت نشد", attributes: " colspan=\"9\" class=\"py-12 text-center text-gray-400\"" }], "");
  } else {
    pageItems.forEach(function (item) {
      const statusClass =
        {
          باز: 'bg-green-100 text-green-700',
          'در حال برگزاری': 'bg-gray-100 text-gray-700',
          'پایان یافته': 'bg-red-100 text-red-700',
          'در انتظار تأیید': 'bg-yellow-100 text-yellow-700',
          'لغو شده': 'bg-red-100 text-red-700',
          'تعلیق شده': 'bg-amber-100 text-amber-700',
        }[item.status] || 'bg-gray-100 text-gray-600';

      const tr = window.AdminPanelTable.createRow();
      tr.className = 'hover:bg-gray-50 transition';
      tr.innerHTML = window.getTermRowHTML ? window.getTermRowHTML(item, statusClass) : '';
      tbody.appendChild(tr);

      if (editingTermRowId === item.id) {
        const expandRow = window.AdminPanelTable.createRow();
        expandRow.className = 'bg-gray-50 term-inline-expand';
        expandRow.innerHTML = window.getTermInlineExpandRowHTML
          ? window.getTermInlineExpandRowHTML(item)
          : '';
        tbody.appendChild(expandRow);
        restrictRecordedTermEditor('inlineTerm' + item.id);
      } else if (attendanceTermRowId === item.id) {
        const expandRow = window.AdminPanelTable.createRow();
        expandRow.className = 'bg-gray-50 term-inline-expand';
        expandRow.innerHTML = window.getTermInlineAttendanceRowHTML
          ? window.getTermInlineAttendanceRowHTML(item)
          : '';
        tbody.appendChild(expandRow);
      }
    });
  }

  updateTermsPagination(list.length, start, end, totalPages);
  updateTermSortIcons();
};

function updateTermsPagination(total, start, end, totalPages) {
  const info = document.getElementById('termsPaginationInfo');
  const shell = document.getElementById('termsPagination') || info?.closest('[data-slot="pagination"]') || info?.parentElement;
  if (shell) {
    shell.hidden = total <= termsPerPage;
    shell.classList.toggle('hidden', total <= termsPerPage);
    shell.style.display = total <= termsPerPage ? 'none' : '';
  }
  if (info) {
    const from = total === 0 ? 0 : start + 1;
    const to = Math.min(end, total);
    info.textContent = 'نمایش ' + from + ' تا ' + to + ' از ' + total + ' ترم';
  }

  const pagination = document.getElementById('termsPaginationButtons');
  if (!pagination) return;

  let html =
    '' +
    '<button onclick="changeTermsPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' +
    (termsCurrentPage === 1 ? 'disabled' : '') +
    '>اول</button>' +
    '<button onclick="changeTermsPage(' +
    (termsCurrentPage - 1) +
    ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' +
    (termsCurrentPage === 1 ? 'disabled' : '') +
    '>قبلی</button>';

  let startPage = Math.max(1, termsCurrentPage - 2);
  let endPage = Math.min(totalPages, startPage + 4);
  if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

  for (let i = startPage; i <= endPage; i++) {
    html +=
      '<button onclick="changeTermsPage(' +
      i +
      ')" class="px-3 py-1.5 rounded-lg ' +
      (i === termsCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50') +
      '">' +
      i +
      '</button>';
  }

  html +=
    '' +
    '<button onclick="changeTermsPage(' +
    (termsCurrentPage + 1) +
    ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' +
    (termsCurrentPage === totalPages ? 'disabled' : '') +
    '>بعدی</button>' +
    '<button onclick="changeTermsPage(' +
    totalPages +
    ')" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ' +
    (termsCurrentPage === totalPages ? 'disabled' : '') +
    '>آخر</button>';

  pagination.innerHTML = html;
}

window.changeTermsPage = async function (page) {
  const totalPages = Math.ceil(filteredTerms.length / termsPerPage) || 1;
  if (page < 1 || page > totalPages) return;
  termsCurrentPage = page;
  renderTermsTable(filteredTerms);
};

// ==================== prompts for new types ====================
async function promptAddNamed(list, label, selectIds, inlineSuffix) {
  const name = ((await AppDialog.prompt('نام ' + label + ' جدید را وارد کنید:')) || '').trim();
  if (!name) return;
  if (
    list.some(function (x) {
      return x.name === name;
    })
  )
    return alert('این مورد قبلاً وجود دارد');
  list.push({ id: Date.now(), name: name });
  selectIds.forEach(function (id) {
    const sel = document.getElementById(id);
    if (!sel) return;
    const opt = document.createElement('option');
    opt.value = list[list.length - 1].id || name;
    opt.textContent = name;
    opt.selected = true;
    sel.appendChild(opt);
  });
}

window.promptAddTermCourse = async function () {
  const name = ((await AppDialog.prompt('نام دوره جدید را وارد کنید:')) || '').trim();
  if (!name) return;
  if (
    allTermCourseOptions.some(function (c) {
      return c.name === name;
    })
  )
    return alert('این دوره قبلاً وجود دارد');
  const item = { id: Date.now(), name: name };
  allTermCourseOptions.push(item);
  document.querySelectorAll('select[id$="Course"], select[id*="Course"]').forEach(function (sel) {
    if (!sel.id.includes('Course')) return;
    const opt = document.createElement('option');
    opt.value = item.id;
    opt.textContent = name;
    opt.selected = true;
    sel.appendChild(opt);
  });
  renderTermCourseFilter();
};

window.promptAddTermCurrency = async function () {
  const name = ((await AppDialog.prompt('نام واحد پول جدید را وارد کنید:')) || '').trim();
  if (!name) return;
  if (
    allTermCurrencies.some(function (c) {
      return c.name === name;
    })
  )
    return alert('این واحد قبلاً وجود دارد');
  allTermCurrencies.push({ id: Date.now(), name: name });
  ['termCurrency', 'editTermCurrency'].forEach(function (id) {
    const sel = document.getElementById(id);
    if (!sel) return;
    const opt = document.createElement('option');
    opt.value = name;
    opt.textContent = name;
    opt.selected = true;
    sel.appendChild(opt);
  });
};

window.promptAddTermDiscount = async function () {
  const name = ((await AppDialog.prompt('عنوان تخفیف جدید را وارد کنید:')) || '').trim();
  if (!name) return;
  if (
    allTermDiscounts.some(function (d) {
      return d.name === name;
    })
  )
    return alert('این تخفیف قبلاً وجود دارد');
  allTermDiscounts.push({ id: Date.now(), name: name });
  ['termDiscount', 'editTermDiscount'].forEach(function (id) {
    const sel = document.getElementById(id);
    if (!sel) return;
    const opt = document.createElement('option');
    opt.value = name;
    opt.textContent = name;
    opt.selected = true;
    sel.appendChild(opt);
  });
};

window.promptAddTermClassroom = async function () {
  window.closeModal?.();
  window.showSection?.('classrooms');
  await window.loadClassrooms?.();
  window.openAddClassroomModal?.();
};

// ==================== multi fields helpers ====================
window.addTermTeacherField = async function (containerId) {
  const el = document.getElementById(containerId);
  if (el && window.getTermTeacherFieldHTML) {
    el.insertAdjacentHTML('beforeend', window.getTermTeacherFieldHTML({}));
    window.refreshTermTeacherSelectionOptions(containerId);
  }
};
window.addTermStudentField = async function (containerId) {
  const el = document.getElementById(containerId);
  if (!el) return;
  const prefix = getTermPrefixFromContainer(containerId);
  const capacity = getTermCourseCapacityFromContext(prefix);
  const currentCount = el.querySelectorAll('.term-student-item').length;
  if (currentCount >= capacity) {
    alert('حداکثر ' + capacity + ' هنرجو برای این دوره قابل انتخاب است');
    return;
  }
  if (window.getTermStudentFieldHTML)
    el.insertAdjacentHTML('beforeend', window.getTermStudentFieldHTML({}));
  window.refreshTermStudentSelectionOptions(containerId);
  window.refreshTermStudentFieldLimit(prefix);
};
window.addTermInstallmentField = async function (containerId) {
  const el = document.getElementById(containerId);
  if (el && window.getTermInstallmentFieldHTML)
    el.insertAdjacentHTML('beforeend', window.getTermInstallmentFieldHTML({}));
};

window.rebuildTermSessions = async function (prefix) {
  const countId = prefix ? prefix + 'SessionCount' : 'termSessionCount';
  const containerId = prefix ? prefix + 'SessionsContainer' : 'termSessionsContainer';
  const countEl = document.getElementById(countId);
  const container = document.getElementById(containerId);
  if (!countEl || !container) return;
  const count = Math.max(1, Math.min(40, parseInt(countEl.value || '8', 10) || 8));
  const existing = Array.from(container.querySelectorAll('.term-session-date')).map(function (inp) {
    return { date: inp.value };
  });
  const sessions = [];
  for (let i = 0; i < count; i++) {
    sessions.push(existing[i] || { date: '' });
  }
  container.innerHTML = window.getTermSessionFieldsHTML(sessions);
};

function readCollection(containerId, selector, mapper) {
  const container = document.getElementById(containerId);
  if (!container) return [];
  return Array.from(container.querySelectorAll(selector)).map(mapper).filter(Boolean);
}

function readTermForm(prefix) {
  if (window.termCourses && window.termCourses.length) {
    const value = (name) => termField(prefix, name),
      course = value('Course'),
      room = value('Classroom'),
      currency = value('Currency'),
      discount = value('Discount'),
      repeat = value('RepeatType'),
      status = value('Status'),
      teachers = value('TeachersContainer'),
      students = value('StudentsContainer'),
      sessions = value('SessionsContainer'),
      installmentCount = Math.max(1, Number(value('InstallmentCount')?.value || 1)),
      classroom = termClassrooms.find((x) => x.id === Number(room?.value || 0)),
      endTime = (start, duration) => {
        const [a, b] = (start || '00:00').split(':').map(Number),
          total = a * 60 + b + Number(duration || 0);
        return (
          String(Math.floor(total / 60) % 24).padStart(2, '0') +
          ':' +
          String(total % 60).padStart(2, '0')
        );
      };
    return {
      name: value('Name')?.value.trim() || '',
      organizationUserId: Number(value('Branch')?.value || 0),
      branchId: Number(classroom?.branchId || window.termCourses.find(x => x.id === Number(course?.value))?.branchId || 0),
      courseId: Number(course?.value || 0),
      classroomId: Number(room?.value || 0),
      currencyId: Number(currency?.value || 0),
      cost: Number(value('Cost')?.value || 0),
      installmentCount,
      installments: Array.from({ length: installmentCount }, () => ({ amount: 1 })),
      discountId: Number(discount?.value || 0),
      repeatType: repeat?.value || '',
      recurrence: !prefix && ['open', 'pending'].includes(status?.value) ? {
        weekday: value('RecurrenceWeekday')?.value ?? '',
        day: value('RecurrenceDay')?.value ?? '',
        month: value('RecurrenceMonth')?.value ?? '',
        exactDate: value('RecurrenceExactDate')?.value ?? '',
      } : null,
      sessionCount: Math.max(1, Number(value('SessionCount')?.value || 1)),
      undatedTemplate: !prefix && ['open', 'pending'].includes(status?.value) && sessions?.dataset.undatedTemplate === '1',
      status: status?.value || 'pending',
      summary: value('Summary')?.value.trim() || '',
      description: value('Description')?.value.trim() || '',
      teachers: teachers
        ? [...teachers.querySelectorAll('select')]
            .filter((x) => x.value)
            .map((x) => ({ id: Number(x.value), name: x.selectedOptions[0]?.textContent || '' }))
        : [],
      students: status?.value === 'open' ? [] : students
        ? [...students.querySelectorAll('select')]
            .filter((x) => x.value)
            .map((x) => ({ id: Number(x.value), name: x.selectedOptions[0]?.textContent || '' }))
        : [],
      sessions: sessions
        ? [...sessions.querySelectorAll('.term-session-date')].map((x, i) => {
            const startTime = sessions.querySelectorAll('.term-session-start')[i]?.value || '',
              durationMinutes = Number(
                sessions.querySelectorAll('.term-session-duration')[i]?.value || 0
              );
            return {
              date: x.value,
              startTime,
              endTime: endTime(startTime, durationMinutes),
              durationMinutes,
              timezoneId: Number(sessions.querySelectorAll('.term-session-timezone')[i]?.value || 0),
            };
          })
        : [],
    };
  }
  const field = function (suffix) {
    return document.getElementById(prefix ? prefix + suffix : 'term' + suffix);
  };
  const name = field('Name') && field('Name').value.trim();
  const branchId = parseInt(field('Branch') && field('Branch').value, 10);
  const courseVal = field('Course') && field('Course').value;
  const courseSelect = field('Course');
  const courseLabel =
    courseSelect && courseSelect.selectedOptions[0]
      ? courseSelect.selectedOptions[0].textContent
      : '';
  const currency = field('Currency') && field('Currency').value;
  const discount = field('Discount') && field('Discount').value;
  const classroomVal = field('Classroom') && field('Classroom').value;
  const classroomSelect = field('Classroom');
  const classroomLabel =
    classroomSelect && classroomSelect.selectedOptions[0]
      ? classroomSelect.selectedOptions[0].textContent
      : '';
  const cost = parseFloat((field('Cost') && field('Cost').value) || 0) || 0;
  const installmentCount =
    parseInt((field('InstallmentCount') && field('InstallmentCount').value) || 1, 10) || 1;
  const status = (field('Status') && field('Status').value) || 'در حال برگزاری';
  const summary = (field('Summary') && field('Summary').value.trim()) || '';
  const description = (field('Description') && field('Description').value.trim()) || '';

  const tContainer = prefix ? prefix + 'TeachersContainer' : 'termTeachersContainer';
  const sContainer = prefix ? prefix + 'StudentsContainer' : 'termStudentsContainer';
  const sessContainer = prefix ? prefix + 'SessionsContainer' : 'termSessionsContainer';

  const teachers = readCollection(tContainer, '.term-teacher-item', function (div) {
    const sel = div.querySelector('.term-teacher-select');
    if (!sel || !sel.value) return null;
    return {
      id: sel.value,
      name: sel.selectedOptions[0] ? sel.selectedOptions[0].textContent : sel.value,
    };
  }).filter(function (item, index, arr) {
    return (
      item &&
      arr.findIndex(function (candidate) {
        return candidate && String(candidate.id) === String(item.id);
      }) === index
    );
  });
  const students = readCollection(sContainer, '.term-student-item', function (div) {
    const sel = div.querySelector('.term-student-select');
    if (!sel || !sel.value) return null;
    return {
      id: sel.value,
      name: sel.selectedOptions[0] ? sel.selectedOptions[0].textContent : sel.value,
    };
  }).filter(function (item, index, arr) {
    return (
      item &&
      arr.findIndex(function (candidate) {
        return candidate && String(candidate.id) === String(item.id);
      }) === index
    );
  });
  const installments = buildInstallmentsFromCount(cost, installmentCount);
  const sessions = readCollection(sessContainer, '.term-session-date', function (inp) {
    return { date: inp.value || '' };
  });

  const branch = getTermBranches().find(function (b) {
    return b.id === branchId;
  });
  const se = deriveStartEnd(sessions);

  return {
    name: name,
    branchId: branchId,
    branchName: branch ? branch.name : 'نامشخص',
    courseId: courseVal,
    course: courseLabel || courseVal || '—',
    classroomId: classroomVal,
    classroom:
      classroomLabel && classroomLabel !== 'انتخاب کلاس' ? classroomLabel : classroomVal || '—',
    currency: currency,
    discount: discount,
    cost: cost,
    status: status,
    summary: summary,
    description: description,
    teachers: teachers,
    students: students,
    installments: installments,
    sessions: sessions,
    start: se.start,
    end: se.end,
  };
}

function termEncode(data) {
  const bytes = new TextEncoder().encode(JSON.stringify(data));
  let value = '';
  bytes.forEach((b) => (value += String.fromCharCode(b)));
  return btoa(value).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}
async function termApi(url, data = null) {
  const token = window.adminCsrfToken || '',
    options = {
      method: data ? 'POST' : 'GET',
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    };
  if (data) {
    options.headers['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';
    options.headers['X-CSRF-TOKEN'] = token;
    options.body = new URLSearchParams({ _token: token, payload_b64: termEncode(data) }).toString();
  }
  const response = await fetch(url, options),
    raw = await response.text();
  let body;
  try {
    body = JSON.parse(raw);
  } catch (e) {
    throw new Error('پاسخ معتبر JSON از سرور دریافت نشد.');
  }
  const envelope = body.data ?? body;
  if (!response.ok || envelope.success === false)
    throw new Error(envelope.message || 'عملیات ترم ناموفق بود.');
  return envelope.data ?? envelope;
}
window.loadTerms = async function () {
  const [data, courseData] = await Promise.all([
    termApi('/academy/admin/terms'),
    termApi('/academy/admin/courses'),
  ]);
  window.termBranches = (courseData.organizations || []).map((x) => ({
    id: x.user_id,
    name: x.name,
    kind: x.kind,
    organizationId: x.id,
    academyId: x.kind === 'academy' ? x.id : (data.branchAcademies || {})[x.id] || null,
  }));
  window.termCourses = (courseData.courses || []).map((x) => ({ ...x, lessonId: x.lesson_id }));
  window.termTimezones = data.timezones || [];
  window.termClassrooms = (data.classrooms || []).map((room) => ({ ...room, name: room.deliveryMode === 'online' ? 'آنلاین · ' + room.name : room.name }));
  window.termCurrencies = data.currencies || [];
  window.termDiscounts = data.discounts || [];
  window.termMembers = data.members || [];
  window.termPermissions = data.permissions || {};
  window.termStatusOptions = data.statusOptions || window.termStatusOptions;
  window.allBranches = termBranches;
  allTermCurrencies = termCurrencies;
  allTermCourseOptions = termCourses;
  allTermClassroomOptions = termClassrooms;
  allTerms = data.terms || [];
  filteredTerms = allTerms.slice();
  if (document.getElementById('termsTable')) {
    renderTermsBranchTabs();
    renderTermFilters();
    filterTerms();
  }
  return data;
};
const readTermFormWithoutTimezones = readTermForm;
readTermForm = function (prefix) {
  const data = readTermFormWithoutTimezones(prefix),
    box = termField(prefix, 'SessionsContainer');
  if (box) {
    const zones = [...box.querySelectorAll('.term-session-timezone')],
      dates = [...box.querySelectorAll('.term-session-date')];
    data.sessions.forEach(
      (session, index) => (session.timezoneId = Number(zones[index]?.value || 0))
    );
    data.skippedDates = dates.flatMap((input) => {
      try {
        return JSON.parse(input.dataset.skippedDates || '[]');
      } catch (_) {
        return [];
      }
    });
  }
  return data;
};
function termField(prefix, name) {
  return document.getElementById(prefix ? prefix + name : 'term' + name);
}
window.availableTermDiscounts=function(organizationUserId){
 const organization=(window.termBranches||[]).find(row=>Number(row.id)===Number(organizationUserId));
 const academy=(window.termBranches||[]).find(row=>row.kind==='academy'&&Number(row.organizationId)===Number(organization?.academyId));
 return (window.termDiscounts||[]).filter(row=>(!row.status||row.status==='active')&&(!row.organizationUserId||Number(row.organizationUserId)===Number(organizationUserId)||Number(row.organizationUserId)===Number(academy?.id)));
};
window.refreshTermDependencies = function (prefix) {
  const organizationUserId = Number(termField(prefix, 'Branch')?.value || 0),
    organization = termBranches.find((x) => x.id === organizationUserId),
    course = termField(prefix, 'Course'),
    room = termField(prefix, 'Classroom');
  if (course) {
    course.disabled = !organizationUserId;
    course.innerHTML =
      '<option value="">انتخاب دوره</option>' +
      termCourses
        .filter((x) => x.organizationUserId === organizationUserId)
        .map((x) => `<option value="${x.id}">${x.name}</option>`)
        .join('');
  }
  if (room) {
    const branchIds =
      organization?.kind === 'branch'
        ? [organization.organizationId]
        : termBranches
            .filter((x) => x.kind === 'branch' && x.academyId === organization?.organizationId)
            .map((x) => x.organizationId);
    room.disabled = !organizationUserId;
    room.innerHTML =
      '<option value="">انتخاب کلاس</option>' +
      termClassrooms
        .filter((x) => branchIds.includes(x.branchId) || (organization?.kind === 'academy' && Number(x.branchId) === 0 && Number(x.academyId) === Number(organization.organizationId)))
        .map((x) => `<option value="${x.id}">${x.name}</option>`)
        .join('');
  }
  const discount=termField(prefix,'Discount');
  if(discount){const selected=discount.value;discount.replaceChildren(new Option(document.documentElement.lang.startsWith('en')?'No discount':'بدون تخفیف',''));window.availableTermDiscounts(organizationUserId).forEach(row=>discount.add(new Option(row.name,row.id)));discount.value=[...discount.options].some(option=>option.value===selected)?selected:'';}
  syncTermDateAvailability(prefix);
  refreshAllTermSessionAvailability(prefix);
};
window.refreshTermCourse = function (prefix) {
  const course = termCourses.find((x) => x.id === Number(termField(prefix, 'Course')?.value || 0));
  syncTermDateAvailability(prefix);
  if (!course) return;
  const render = (type, capacity) => {
    const box = termField(prefix, type === 'teacher' ? 'TeachersContainer' : 'StudentsContainer');
    if (!box) return;
    box.innerHTML = Array.from(
      { length: capacity },
      (_, i) =>
        `<div class="mb-3"><label class="mb-1 block text-xs">${type === 'teacher' ? 'استاد' : 'هنرجو'} ${i + 1}</label><select class="term-${type}-select w-full rounded-2xl border px-4 py-3" data-person-index="${i}" onchange="refreshTermPeople('${prefix}','${type}')" ${i ? 'disabled' : ''}><option value="">انتخاب کنید</option></select></div>`
    ).join('');
    refreshTermPeople(prefix, type);
  };
  render('teacher', document.querySelector('[data-term-batch]') && !prefix ? 1 : course.teacher_capacity);
  render('student', course.student_capacity);
  syncTermPeopleVisibility(prefix);
};
window.refreshTermPeople = function (prefix, type) {
  const classroom = Number(termField(prefix, 'Classroom')?.value || 0),
    branch = Number(termClassrooms.find((x) => x.id === classroom)?.branchId || 0),
    course = termCourses.find((x) => x.id === Number(termField(prefix, 'Course')?.value || 0)),
    box = termField(prefix, type === 'teacher' ? 'TeachersContainer' : 'StudentsContainer');
  if (!box || !course) return;
  const selects = [...box.querySelectorAll('select')],
    selected = selects.map((x) => x.value).filter(Boolean),
    courseBranch = type === 'teacher' ? Number(course.branchId || (course.organizationKind === 'branch' ? course.organizationId : 0)) : branch,
    available = termMembers.filter(
      (x) =>
        (courseBranch > 0
          ? Number(x.branchId) === courseBranch
          : Number(x.academyId) === Number(course.organizationId) &&
            (Number(x.branchId || 0) === branch || (type === 'teacher' && Number(x.branchId || 0) === 0))) &&
        x.type === type &&
        (type !== 'teacher' || (x.lessonIds || [x.lessonId]).some(id => Number(id) === Number(course.lessonId)))
    );
  if (type === 'teacher') {
    const note = termField(prefix, 'TeacherAvailabilityNote');
    if (note) {
      note.classList.toggle('hidden', available.length > 0);
      note.textContent = document.documentElement.lang.startsWith('en') ? 'No eligible teacher is registered for this course. Check employee contracts and teaching lessons.' : 'برای این دوره مدرس واجد شرایط ثبت نشده است. قرارداد کارمندها و درس‌های تدریس آن‌ها را بررسی کنید.';
    }
  }
  selects.forEach((select, i) => {
    const value = select.value;
    select.disabled = i > 0 && !selects[i - 1].value;
    select.replaceChildren(
      new Option(document.documentElement.lang.startsWith('en') ? 'Select' : 'انتخاب کنید', ''),
      ...available
        .filter((x) => !selected.includes(String(x.id)) || String(x.id) === value)
        .map((x) => new Option(x.name, x.id, false, String(x.id) === value))
    );
  });
};
window.rebuildDbTermSessions = function (prefix) {
  const count = Math.max(1, Number(termField(prefix, 'SessionCount')?.value || 1)),
    box = termField(prefix, 'SessionsContainer'),
    dates = box ? [...box.querySelectorAll('.term-session-date')].map((x) => x.value) : [],
    starts = box ? [...box.querySelectorAll('.term-session-start')].map((x) => x.value) : [],
    durations = box ? [...box.querySelectorAll('.term-session-duration')].map((x) => x.value) : [],
    firstDuration = durations[0] || '90',
    termId = box?.dataset.termId || 0,
    repeat = termField(prefix, 'RepeatType'),
    repeatField = termField(prefix, 'RepeatTypeField');
  if (count === 1 && repeat && (prefix || termField(prefix, 'Status')?.value !== 'open')) repeat.value = 'no-period';
  if (count > 1 && repeat?.value === 'no-period') repeat.value = 'week';
  repeatField?.classList.toggle('hidden', !!prefix && count === 1);
  if (!prefix) window.syncNewTermRecurrence?.(prefix);
  if (!prefix && ['open', 'pending'].includes(termField(prefix, 'Status')?.value) && box) {
    const previous = { startTime: starts[0], durationMinutes: Number(durations[0] || firstDuration), timezoneId: Number(box.querySelector('.term-session-timezone')?.value || 0) };
    box.innerHTML = window.getTermOpenSessionHTML(prefix, previous);
    box.dataset.undatedTemplate = '1';
    window.refreshTermOpenAvailability?.(prefix);
    syncTermInstallmentLimit(prefix);
    return;
  }
  if (box) box.dataset.undatedTemplate = '0';
  if (box)
    box.innerHTML = Array.from(
      { length: count },
      (_, i) =>
        `<div class="w-full border-b border-gray-100 py-2 last:border-b-0"><label class="mb-1 block text-xs font-medium text-gray-500">جلسه ${i + 1}</label><div class="grid grid-cols-1 gap-2 sm:grid-cols-4">${window.getTermDateInputHTML(prefix, i, dates[i] || '', true)}<select disabled class="term-session-start w-full rounded-xl border px-3 py-2.5 disabled:bg-gray-100" ${i === 0 ? `onchange="syncTermSessionTimes('${prefix}')"` : ''}>${window.getTermTimeOptions(starts[i] ? [starts[i]] : [], starts[i] || '')}</select><div class="relative"><input disabled type="number" min="5" max="1440" step="5" value="${durations[i] || firstDuration}" class="term-session-duration w-full rounded-xl border px-3 py-2.5 pl-14 disabled:bg-gray-100" oninput="termDurationChanged('${prefix}',${i})" onchange="termDurationChanged('${prefix}',${i})"><span class="pointer-events-none absolute left-3 top-3 text-xs text-gray-400">دقیقه</span></div></div><p class="term-session-note mt-2 hidden text-xs text-amber-700"></p></div>`
    ).join('');
  box.dataset.termId = termId;
  syncTermInstallmentLimit(prefix);
  syncTermDateAvailability(prefix);
  refreshTermSessionDates(prefix);
  refreshAllTermSessionAvailability(prefix);
};
const rebuildDbTermSessionsWithoutTimezones = window.rebuildDbTermSessions;
window.rebuildDbTermSessions = function (prefix) {
  if (!prefix && termField(prefix, 'Status')?.value === 'open') {
    rebuildDbTermSessionsWithoutTimezones(prefix);
    return;
  }
  const oldBox = termField(prefix, 'SessionsContainer'),
    oldZones = oldBox
      ? [...oldBox.querySelectorAll('.term-session-timezone')].map((x) => x.value)
      : [],
    firstZone =
      oldZones[0] ||
      termTimezones.find((x) => x.name === 'Asia/Tehran')?.id ||
      termTimezones[0]?.id ||
      0;
  rebuildDbTermSessionsWithoutTimezones(prefix);
  const box = termField(prefix, 'SessionsContainer'),
    count = box?.querySelectorAll('.term-session-date').length || 1,
    repeat = termField(prefix, 'RepeatType');
  if (count > 1 && repeat?.value === 'no-period') repeat.value = 'week';
  box?.querySelectorAll('.term-session-start').forEach(function (start, index) {
    const selected = oldZones[index] || firstZone,
      select = document.createElement('select');
    select.className = 'term-session-timezone w-full rounded-xl border px-3 py-2.5';
    select.setAttribute('onchange', `termTimezoneChanged('${prefix}',${index},this)`);
    select.innerHTML = termTimezones
      .map(
        (x) =>
          `<option value="${x.id}" ${String(x.id) === String(selected) ? 'selected' : ''}>${x.name}</option>`
      )
      .join('');
    start.insertAdjacentElement('beforebegin', select);
  });
  refreshTermSessionDates(prefix, true);
  syncTermSessionTimes(prefix);
};
window.syncTermSessionTimes = async function (prefix) {
  const box = termField(prefix, 'SessionsContainer');
  if (!box) return;
  const starts = [...box.querySelectorAll('.term-session-start')],
    durations = [...box.querySelectorAll('.term-session-duration')],
    first = starts[0]?.value || '',
    firstDuration = durations[0]?.value || '90';
  starts.slice(1).forEach((x) => (x.dataset.preferredStart = first));
  durations.slice(1).forEach((x) => (x.value = firstDuration));
  await Promise.all(starts.slice(1).map((_, i) => refreshTermSessionAvailability(prefix, i + 1)));
  starts.slice(1).forEach((x) => {
    if ([...x.options].some((o) => o.value === first)) x.value = first;
  });
};
window.syncTermFinancialFields = function (prefix) {
  const free = Number(termField(prefix, 'Cost')?.value || 0) === 0;
  const installments = termField(prefix, 'InstallmentCount'),
    discount = termField(prefix, 'Discount'),
    addDiscount = termField(prefix, 'AddDiscount');
  if (installments) {
    installments.disabled = free;
    if (free) installments.value = '1';
  }
  if (discount) {
    discount.disabled = free;
    if (free) discount.value = '';
  }
  if (addDiscount) addDiscount.disabled = free;
};
window.syncTermPeopleVisibility = function (prefix) {
  if (!prefix && document.querySelector('[data-term-batch]')) {
    termField(prefix, 'TeachersField')?.classList.remove('hidden');
    return;
  }
  const status =
      termField(prefix, 'Status')?.value ||
      (window.termPermissions?.isReceptionist ? 'pending' : 'open'),
    teachers = termField(prefix, 'TeachersField'),
    students = termField(prefix, 'StudentsField');
  teachers?.classList.toggle('hidden', !['open', 'pending', 'ongoing'].includes(status));
  students?.classList.toggle('hidden', status !== 'ongoing');
};
window.syncNewTermStatus = function (prefix) {
  if (prefix) return;
  const box = termField(prefix, 'SessionsContainer');
  const undated = ['open', 'pending'].includes(termField(prefix, 'Status')?.value);
  if (!box || (box.dataset.undatedTemplate === '1') === undated) return;
  rebuildDbTermSessions(prefix);
  window.syncNewTermRecurrence?.(prefix);
  const label = box.previousElementSibling;
  if (label) label.textContent = undated ? 'منطقه زمانی، ساعت شروع و مدت جلسه؛ تاریخ هنگام تأیید هنرجو تعیین می‌شود' : 'تاریخ، منطقه زمانی، ساعت شروع و مدت جلسه';
};
window.syncNewTermRecurrence = function (prefix) {
  if (prefix) return;
  const field = termField(prefix, 'RecurrenceField');
  if (!field) return;
  const weekdaySelect = termField(prefix, 'RecurrenceWeekday');
  if (weekdaySelect && !weekdaySelect.dataset.availabilityBound) {
    weekdaySelect.addEventListener('change', () => window.refreshTermOpenAvailability(prefix));
    weekdaySelect.dataset.availabilityBound = '1';
  }
  const period = termField(prefix, 'RepeatType')?.value || 'no-period';
  const count = Number(termField(prefix, 'SessionCount')?.value || 1);
  const exactOption = termField(prefix, 'RepeatType')?.querySelector('option[value="no-period"]');
  if (exactOption) exactOption.disabled = count > 1;
  const open = termField(prefix, 'Status')?.value === 'open';
  field.classList.toggle('hidden', !open);
  field.querySelectorAll('[data-recurrence-kind]').forEach((node) => {
    const kind = node.dataset.recurrenceKind;
    node.classList.toggle('hidden', !open || !(kind === 'week' && ['week', '2-week', '3-week', '4-week'].includes(period) || kind === 'month' && ['month', 'year'].includes(period) || kind === 'year' && period === 'year' || kind === 'exact' && period === 'no-period'));
  });
  window.refreshTermOpenAvailability?.(prefix);
};
window.refreshTermOpenAvailability = async function (prefix) {
  const box = termField(prefix, 'SessionsContainer');
  if (!box || box.dataset.undatedTemplate !== '1') return;
  const start = box.querySelector('.term-session-start');
  const duration = Math.max(5, Number(box.querySelector('.term-session-duration')?.value || 90));
  const zone = box.querySelector('.term-session-timezone');
  if (!start) return;
  const preferred = start.value || start.dataset.preferredStart || '10:00';
  const period = termField(prefix, 'RepeatType')?.value || 'no-period';
  const weekly = ['week', '2-week', '3-week', '4-week'].includes(period);
  const weekday = termField(prefix, 'RecurrenceWeekday')?.value ?? '';
  const classroom = Number(termField(prefix, 'Classroom')?.value || 0);
  const organization = termBranches.find(row => Number(row.id) === Number(termField(prefix, 'Branch')?.value));
  const branch = Number(termClassrooms.find((room) => room.id === classroom)?.branchId || (organization?.kind === 'branch' ? organization.organizationId : 0));
  const serial = String(Number(box.dataset.availabilitySerial || 0) + 1);
  box.dataset.availabilitySerial = serial;
  start.disabled = true;
  start.innerHTML = `<option value="">${weekly ? 'در حال دریافت ساعت‌های کاری...' : 'در حال آماده‌سازی ساعت‌ها...'}</option>`;
  if (!weekly) {
    const limit = Math.max(0, Math.floor((1440 - duration) / 5));
    const times = Array.from({ length: limit + 1 }, (_, i) => String(Math.floor(i * 5 / 60)).padStart(2, '0') + ':' + String(i * 5 % 60).padStart(2, '0'));
    start.innerHTML = window.getTermTimeOptions(times, preferred);
    start.value = times.includes(preferred) ? preferred : times.includes('10:00') ? '10:00' : times[0];
    start.disabled = !times.length;
    return;
  }
  if (weekday === '' || !organization) {
    start.innerHTML = '<option value="">ابتدا سازمان و روز هفته را انتخاب کنید</option>';
    return;
  }
  try {
    const academy = Number(termClassrooms.find((room) => room.id === classroom)?.academyId || (organization.kind === 'academy' ? organization.organizationId : organization.academyId));
    const query = new URLSearchParams({ branch, classroom, weekday, duration, academy });
    const data = await termApi('/academy/admin/term-available-times?' + query);
    if (box.dataset.availabilitySerial !== serial || !box.isConnected) return;
    const times = data.times || [];
    if (zone && data.timezoneId) zone.value = String(data.timezoneId);
    start.innerHTML = times.length ? window.getTermTimeOptions(times, preferred) : '<option value="">در این روز ساعت کاری کافی وجود ندارد</option>';
    start.value = times.includes(preferred) ? preferred : times[0] || '';
    start.disabled = !times.length;
    start.dataset.preferredStart = start.value;
  } catch (error) {
    if (box.dataset.availabilitySerial !== serial || !box.isConnected) return;
    start.innerHTML = '<option value="">دریافت ساعت‌های کاری ناموفق بود</option>';
    start.title = error.message || 'خطا در دریافت ساعت‌های کاری';
  }
};
let termDurationTimer;
window.termDurationChanged = function (prefix, index) {
  const box = termField(prefix, 'SessionsContainer');
  if (!box) return;
  if (index === 0) {
    const durations = [...box.querySelectorAll('.term-session-duration')],
      value = durations[0]?.value || '90';
    durations.slice(1).forEach((x) => (x.value = value));
  }
  clearTimeout(termDurationTimer);
  termDurationTimer = setTimeout(() => refreshAllTermSessionAvailability(prefix), 150);
};
window.syncTermInstallmentLimit = function (prefix) {
  const count = Math.max(1, Number(termField(prefix, 'SessionCount')?.value || 1)),
    field = termField(prefix, 'InstallmentCount'),
    max = Math.max(2, count);
  if (field) {
    field.max = String(max);
    if (Number(field.value) > max) field.value = String(max);
    const hint = field.nextElementSibling;
    if (hint) hint.textContent = 'حداکثر ' + max + ' قسط';
  }
};
window.syncTermDateAvailability = function (prefix) {
  const ready = !!termField(prefix, 'Course')?.value && !!termField(prefix, 'Classroom')?.value,
    box = termField(prefix, 'SessionsContainer');
  if (!box || box.dataset.undatedTemplate === '1') return;
  [...box.querySelectorAll('.term-session-date')].forEach((input) => {
    input.disabled = !ready;
    window.syncLocalizedDateInput?.(input);
  });
};
window.refreshTermSessionAvailability = async function (prefix, index) {
  const box = termField(prefix, 'SessionsContainer'),
    classroom = Number(termField(prefix, 'Classroom')?.value || 0),
    branch = Number(termClassrooms.find((x) => x.id === classroom)?.branchId || 0),
    organizationUserId = Number(termField(prefix, 'Branch')?.value || 0),
    organization = termBranches.find((x) => x.id === organizationUserId);
  if (!box) return;
  const dates = [...box.querySelectorAll('.term-session-date')],
    starts = [...box.querySelectorAll('.term-session-start')],
    durations = [...box.querySelectorAll('.term-session-duration')],
    zones = [...box.querySelectorAll('.term-session-timezone')],
    date = dates[index]?.value || '',
    select = starts[index],
    duration = durations[index],
    zone = zones[index];
  if (!select || !duration) return;
  const previous = select.dataset.preferredStart || select.value,
    requestedTimezone = Number(zone?.dataset.userChanged === '1' ? zone.value : 0),
    sourceTimezoneId = Number(zone?.dataset.sourceTimezone || zone?.dataset.previousTimezone || 0);
  select.disabled = true;
  duration.disabled = true;
  select.innerHTML = '<option value="">ابتدا تاریخ را انتخاب کنید</option>';
  if (!date || !branch || !classroom) return;
  try {
    const query = new URLSearchParams({
        branch,
        classroom,
        date,
        duration: Math.max(5, Number(duration.value || 90)),
        excludeTerm: Number(box.dataset.termId || 0),
        organizationKind: organization?.kind || 'branch',
        organizationUserId,
        timezoneId: requestedTimezone,
        selectedStart: previous,
        sourceTimezoneId,
      }),
      data = await termApi('/academy/admin/term-available-times?' + query),
      times = data.times || [],
      label = organization?.kind === 'academy' ? 'آموزشگاه' : 'شعبه',
      desired = data.selectedStart || previous;
    if (zone && data.timezoneId) {
      zone.value = String(data.timezoneId);
      zone.dataset.previousTimezone = String(data.timezoneId);
      delete zone.dataset.sourceTimezone;
    }
    select.innerHTML = times.length
      ? window.getTermTimeOptions(times, desired)
      : `<option value="">${data.closed ? label + ' در این روز تعطیل است' : 'کلاس در این روز ظرفیت زمانی خالی ندارد'}</option>`;
    select.disabled = !times.length;
    duration.disabled = !times.length;
    if (times.length) select.value = times.includes(desired) ? desired : times[0];
    delete select.dataset.preferredStart;
    if (!times.length && dates[index] === document.activeElement) {
      alert(
        data.closed
          ? label + ' در تاریخ انتخاب‌شده تعطیل است.'
          : 'تمام ساعت‌های قابل برگزاری این کلاس در تاریخ انتخاب‌شده پر است.'
      );
      dates[index].value = '';
    }
  } catch (e) {
    console.error('Term availability request failed:', e);
    const fallback = Array.from(
      { length: 48 },
      (_, i) => String(Math.floor(i / 2)).padStart(2, '0') + ':' + (i % 2 ? '30' : '00')
    );
    select.innerHTML = window.getTermTimeOptions(fallback, previous || '10:00');
    select.value = fallback.includes(previous) ? previous : '10:00';
    select.disabled = false;
    duration.disabled = false;
    select.title = e.message || 'خطا در دریافت ساعت کاری';
  }
};
window.termTimezoneChanged = function (prefix, index, select) {
  const box = termField(prefix, 'SessionsContainer'),
    zones = box ? [...box.querySelectorAll('.term-session-timezone')] : [];
  select.dataset.sourceTimezone = select.dataset.previousTimezone || '';
  select.dataset.userChanged = '1';
  if (index === 0)
    zones.slice(1).forEach((zone) => {
      zone.dataset.sourceTimezone = zone.dataset.previousTimezone || zone.value;
      zone.value = select.value;
      zone.dataset.userChanged = '1';
    });
  if (index === 0) refreshAllTermSessionAvailability(prefix);
  else refreshTermSessionAvailability(prefix, index);
};
window.refreshAllTermSessionAvailability = async function (prefix) {
  const box = termField(prefix, 'SessionsContainer');
  if (!box) return;
  if (box.dataset.undatedTemplate === '1') return window.refreshTermOpenAvailability(prefix);
  await Promise.all(
    [...box.querySelectorAll('.term-session-date')].map((_, i) =>
      refreshTermSessionAvailability(prefix, i)
    )
  );
};
function advanceTermSessionDate(value, repeat) {
  const days = { week: 7, '2-week': 14, '3-week': 21, '4-week': 28 },
    date = new Date(value + 'T12:00:00');
  if (days[repeat]) date.setDate(date.getDate() + days[repeat]);
  else if (repeat === 'month') date.setMonth(date.getMonth() + 1);
  else if (repeat === 'year') date.setFullYear(date.getFullYear() + 1);
  else date.setDate(date.getDate() + 1);
  return date.toISOString().slice(0, 10);
}
function setTermSessionNote(box, index, message) {
  const note = box?.querySelectorAll('.term-session-note')[index];
  if (!note) return;
  note.textContent = message || '';
  note.classList.toggle('hidden', !message);
}
async function resolveAutomaticTermSessionDates(prefix) {
  const repeat = termField(prefix, 'RepeatType')?.value || 'no-period',
    box = termField(prefix, 'SessionsContainer');
  if (!box || repeat === 'no-period') return;
  const inputs = [...box.querySelectorAll('.term-session-date')],
    durations = [...box.querySelectorAll('.term-session-duration')],
    zones = [...box.querySelectorAll('.term-session-timezone')];
  const classroom = Number(termField(prefix, 'Classroom')?.value || 0),
    branch = Number(termClassrooms.find((x) => x.id === classroom)?.branchId || 0),
    organizationUserId = Number(termField(prefix, 'Branch')?.value || 0),
    organization = termBranches.find((x) => x.id === organizationUserId);
  if (!inputs[0]?.value || !classroom || !branch) return;
  setTermSessionNote(box, 0, '');
  let previousDate = inputs[0].value;
  for (let index = 1; index < inputs.length; index++) {
    let candidate = advanceTermSessionDate(previousDate, repeat),
      rejected = [];
    for (let attempt = 0; attempt < 100; attempt++) {
      const query = new URLSearchParams({
        branch,
        classroom,
        date: candidate,
        duration: Math.max(5, Number(durations[index]?.value || durations[0]?.value || 90)),
        excludeTerm: Number(box.dataset.termId || 0),
        organizationKind: organization?.kind || 'branch',
        organizationUserId,
        timezoneId: Number(zones[index]?.value || 0),
      });
      const result = await termApi('/academy/admin/term-available-times?' + query);
      if (!result.closed) break;
      rejected.push({
        date: candidate,
        reasonType: result.closureType || 'organization_holiday',
        reason: result.closureReason || 'تعطیلی آموزشگاه یا شعبه',
      });
      candidate = advanceTermSessionDate(candidate, repeat);
    }
    inputs[index].value = candidate;
    inputs[index].dataset.manual = '0';
    inputs[index].dataset.skippedDates = JSON.stringify(
      rejected.map((item) => ({
        sessionNumber: index + 1,
        skippedDate: item.date,
        replacementDate: candidate,
        reasonType: item.reasonType,
        reason: item.reason,
      }))
    );
    window.syncLocalizedDateInput?.(inputs[index]);
    const skipped = rejected
        .map((item) => `${window.localizedDateValue?.(item.date) || item.date} (${item.reason})`)
        .join('، '),
      replacement = window.localizedDateValue?.(candidate) || candidate;
    setTermSessionNote(
      box,
      index,
      rejected.length ? `${skipped}؛ تاریخ جلسه به ${replacement} منتقل شد.` : ''
    );
    previousDate = candidate;
  }
}
window.refreshTermSessionDates = async function (prefix, force = false) {
  const repeat = termField(prefix, 'RepeatType')?.value || 'no-period',
    box = termField(prefix, 'SessionsContainer');
  if (!box) return;
  const inputs = [...box.querySelectorAll('.term-session-date')],
    activeIndex = inputs.indexOf(document.activeElement),
    first = inputs[0]?.value;
  if (!force && activeIndex > 0) {
    inputs[activeIndex].dataset.manual = '1';
    return;
  }
  if (repeat === 'no-period' || !first) return;
  await resolveAutomaticTermSessionDates(prefix);
  await refreshAllTermSessionAvailability(prefix);
  await syncTermSessionTimes(prefix);
};
window.termSessionDateChanged = async function (prefix, index, input) {
  const signature = input?.value || '';
  if (!signature) return refreshTermSessionAvailability(prefix, index);
  if (index === 0) {
    await refreshTermSessionDates(prefix, true);
  } else {
    input.dataset.manual = '1';
    delete input.dataset.skippedDates;
    setTermSessionNote(termField(prefix, 'SessionsContainer'), index, '');
    await refreshTermSessionAvailability(prefix, index);
  }
};
window.termJalaliDateChanged = function (prefix, index) {
  setTimeout(function () {
    const box = termField(prefix, 'SessionsContainer'),
      input = box?.querySelectorAll('.term-session-date')[index];
    if (input) termSessionDateChanged(prefix, index, input);
  }, 0);
};
let pendingTermSessionCancellation = null;
function termSessionDurationMinutes(session) {
  const [a, b] = (session.startTime || '00:00').split(':').map(Number),
    [c, d] = (session.endTime || '00:00').split(':').map(Number);
  return Math.max(5, c * 60 + d - a * 60 - b);
}
window.openTermSessionCancellation = async function (termId, sessionId) {
  const item = allTerms.find((x) => x.id === termId),
    session = item?.sessions?.find((x) => x.id === sessionId);
  if (!item || !session) return;
  const active = (item.sessions || [])
      .filter(
        (x) =>
          !['canceled', 'rejected'].includes(x.bookingStatus) && x.cancellationStatus !== 'approved'
      )
      .map((x) => x.date)
      .filter(Boolean)
      .sort(),
    last = active[active.length - 1] || session.date,
    suggested = advanceTermSessionDate(last, item.repeatType || 'no-period'),
    duration = termSessionDurationMinutes(session);
  pendingTermSessionCancellation = { item, session, duration };
  const display = window.localizedDateValue?.(suggested) || suggested;
  document.getElementById('modalContainer').innerHTML =
    `<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"><div class="w-full max-w-xl rounded-3xl bg-white p-7"><div class="mb-5 flex items-center justify-between"><div><h2 class="text-xl font-bold">لغو جلسه ${session.number || ''}</h2><p class="mt-1 text-sm text-gray-500">${item.name}</p></div><button onclick="closeModal()" class="text-3xl text-gray-300">×</button></div><div class="space-y-4"><div><label class="mb-2 block text-sm font-medium">دلیل لغو *</label><textarea id="termCancellationReason" rows="3" class="w-full rounded-2xl border p-3" placeholder="دلیل برگزار نشدن جلسه را بنویسید"></textarea></div><div><label class="mb-2 block text-sm font-medium">تاریخ جلسه جبرانی *</label><div class="localized-date-field"><input type="text" value="${display}" data-jdp data-jdp-target-value-input="#termMakeupDate" data-jdp-target-value-type="gregorian" onchange="termMakeupDateChanged()" class="w-full rounded-2xl border p-3"><button type="button" class="localized-date-trigger" onclick="this.previousElementSibling.focus();window.openLocalizedDatePicker?.(this.previousElementSibling)"><i class="far fa-calendar-alt"></i></button><input id="termMakeupDate" type="hidden" value="${suggested}"></div><p id="termMakeupAvailabilityMessage" class="mt-2 text-xs text-gray-500">در حال بررسی تاریخ پیشنهادی...</p></div><div class="grid grid-cols-2 gap-3"><div><label class="mb-2 block text-sm font-medium">ساعت شروع</label><select id="termMakeupStart" class="w-full rounded-2xl border p-3"><option value="">در حال دریافت...</option></select></div><div><label class="mb-2 block text-sm font-medium">مدت جلسه</label><div class="rounded-2xl border bg-gray-50 p-3">${duration} دقیقه</div></div></div><div class="flex gap-3"><button id="saveTermCancellationButton" disabled onclick="saveTermSessionCancellation()" class="flex-1 rounded-2xl bg-red-600 p-3 text-white disabled:opacity-40">ثبت لغو و جلسه جبرانی</button><button onclick="viewTerm(${termId})" class="flex-1 rounded-2xl border p-3">انصراف</button></div></div></div></div>`;
  await refreshTermMakeupAvailability(true);
};
window.termMakeupDateChanged = function () {
  setTimeout(() => refreshTermMakeupAvailability(false), 0);
};
window.refreshTermMakeupAvailability = async function (autoAdvance = false) {
  const state = pendingTermSessionCancellation,
    dateInput = document.getElementById('termMakeupDate'),
    startSelect = document.getElementById('termMakeupStart'),
    message = document.getElementById('termMakeupAvailabilityMessage'),
    save = document.getElementById('saveTermCancellationButton');
  if (!state || !dateInput || !startSelect) return;
  let date = dateInput.value,
    attempt = 0;
  save.disabled = true;
  startSelect.disabled = true;
  message.textContent = 'در حال بررسی زمان‌های قابل برگزاری...';
  try {
    const course = termCourses.find((x) => x.id === state.item.courseId),
      organization = termBranches.find((x) => x.id === course?.organizationUserId);
    let result;
    do {
      const query = new URLSearchParams({
        branch: state.item.branchId,
        classroom: state.item.classroomId,
        date,
        duration: state.duration,
        excludeTerm: 0,
        organizationKind: organization?.kind || 'branch',
        organizationUserId: course?.organizationUserId || 0,
        timezoneId: state.session.timezoneId || 0,
        selectedStart: state.session.startTime || '',
      });
      result = await termApi('/academy/admin/term-available-times?' + query);
      const times = result.times || [],
        sameTime = times.includes(state.session.startTime);
      if (!autoAdvance || (!result.closed && sameTime)) break;
      date = advanceTermSessionDate(date, state.item.repeatType || 'no-period');
      attempt++;
    } while (attempt < 60);
    if (date !== dateInput.value) {
      dateInput.value = date;
      window.syncLocalizedDateInput?.(dateInput);
    }
    const times = result?.times || [];
    startSelect.innerHTML = window.getTermTimeOptions(times, state.session.startTime);
    if (times.includes(state.session.startTime)) startSelect.value = state.session.startTime;
    else if (times.length) startSelect.value = times[0];
    startSelect.disabled = !times.length;
    save.disabled = !times.length;
    message.textContent = attempt
      ? `تاریخ پیشنهادی اولیه قابل برگزاری نبود؛ جلسه جبرانی به ${window.localizedDateValue?.(date) || date} منتقل شد.`
      : times.length
        ? 'تاریخ و ساعت جلسه جبرانی قابل برگزاری است.'
        : result?.closed
          ? 'شعبه یا آموزشگاه در این تاریخ تعطیل است.'
          : 'در این تاریخ ساعت خالی وجود ندارد.';
    message.className = 'mt-2 text-xs ' + (times.length ? 'text-emerald-600' : 'text-red-600');
  } catch (e) {
    message.textContent = e.message;
    message.className = 'mt-2 text-xs text-red-600';
  }
};
window.saveTermSessionCancellation = async function () {
  const state = pendingTermSessionCancellation,
    reason = document.getElementById('termCancellationReason')?.value.trim() || '',
    date = document.getElementById('termMakeupDate')?.value || '',
    start = document.getElementById('termMakeupStart')?.value || '';
  if (!state) return;
  if (!reason) return alert('دلیل لغو جلسه الزامی است.');
  if (!date || !start) return alert('تاریخ و ساعت معتبر جلسه جبرانی را انتخاب کنید.');
  const [a, b] = start.split(':').map(Number),
    total = a * 60 + b + state.duration,
    end =
      String(Math.floor(total / 60) % 24).padStart(2, '0') +
      ':' +
      String(total % 60).padStart(2, '0');
  try {
    await termApi(`/academy/admin/terms/${state.item.id}/sessions/${state.session.id}/cancel`, {
      reason,
      makeupDate: date,
      startTime: start,
      endTime: end,
    });
    const termId = state.item.id;
    pendingTermSessionCancellation = null;
    await loadTerms();
    viewTerm(termId);
    alert(
      window.termPermissions?.isReceptionist
        ? 'درخواست لغو برای تأیید مدیر ثبت شد.'
        : 'جلسه لغو و جلسه جبرانی ثبت شد.'
    );
  } catch (e) {
    alert(e.message);
  }
};
window.decideTermSessionCancellation = async function (termId, sessionId, approve) {
  const action = approve ? 'تأیید' : 'رد';
  if (!confirm(`${action} درخواست لغو این جلسه؟`)) return;
  try {
    await termApi(
      `/academy/admin/terms/${termId}/sessions/${sessionId}/cancel/${approve ? 'approve' : 'reject'}`,
      {}
    );
    await loadTerms();
    viewTerm(termId);
  } catch (e) {
    alert(e.message);
  }
};
window.restoreTermSession = async function (termId, sessionId) {
  if (!(await AppDialog.confirm('جلسه بازگردانی و همه جلسات جبرانی وابسته به آن حذف شوند؟')))
    return;
  try {
    const result = await termApi(
      `/academy/admin/terms/${termId}/sessions/${sessionId}/restore`,
      {}
    );
    await loadTerms();
    await window.refreshDashboard?.();
    viewTerm(termId);
    AppDialog.alert(
      `جلسه بازگردانی شد و ${Number(result?.removedMakeupSessions || 0).toLocaleString('fa-IR')} جلسه جبرانی حذف شد.`
    );
  } catch (e) {
    AppDialog.alert(e.message);
  }
};
let editingTermDiscount = null;
let termDiscountCatalog = null, termDiscountPage = 1, termDiscountOrganization = 'all', savingTermDiscount = false;
const discountText = (fa,en) => document.documentElement.lang.startsWith('en') ? en : fa;
window.showTermTab = function (selected) {
  if (!['terms','discounts'].includes(selected)) return;
  document.querySelectorAll('#terms [data-term-panel]').forEach(panel=>panel.classList.toggle('hidden',panel.dataset.termPanel!==selected));
  document.querySelectorAll('#terms [data-term-tab]').forEach(button=>{
    const active=button.dataset.termTab===selected;
    if(active)button.setAttribute('aria-current','page');else button.removeAttribute('aria-current');
    ['bg-indigo-600','text-white'].forEach(name=>button.classList.toggle(name,active));
    ['bg-white','text-gray-700','hover:bg-indigo-50','dark:bg-slate-800','dark:text-white'].forEach(name=>button.classList.toggle(name,!active));
  });
  if(selected==='discounts')loadTermDiscountCatalog().catch(error=>alert(error.message));
};
async function loadTermDiscountCatalog() {
  termDiscountCatalog=await termApi('/academy/admin/term-discounts');
  window.termDiscountOrganizations = termDiscountCatalog.organizations || [];
  const editor = document.querySelector('[data-discount-editor] form');
  const entered = editor ? [...editor.elements].filter(field => field.name).map(field => [field.name, field.value]) : [];
  window.termDiscounts=(termDiscountCatalog.discounts||[]).filter(row=>row.status==='active');
  allTermDiscounts=window.termDiscounts;
  window.renderPanelOrganizationTabs?.('termDiscountsBranchTabs',termDiscountOrganization,window.filterTermDiscountsByBranch,{branches:()=>termDiscountCatalog.organizations||[],showAcademy:true});
  renderTermDiscounts();
  const updatedEditor = document.querySelector('[data-discount-editor] form');
  if (updatedEditor) entered.forEach(([name, value]) => { const field = updatedEditor.elements.namedItem(name); if (field && (field.tagName !== 'SELECT' || [...field.options].some(option => option.value === value))) field.value = value; });
}
window.filterTermDiscountsByBranch=function(value){termDiscountOrganization=String(value);termDiscountPage=1;loadTermDiscountCatalog().catch(error=>alert(error.message));};
window.filterTermDiscounts=function(){termDiscountPage=1;renderTermDiscounts();};
function discountCells(row){ return [row.name,row.type==='fixed'?discountText('مبلغ ثابت','Fixed amount'):discountText('درصدی','Percentage'),Number(row.value).toLocaleString(document.documentElement.lang.startsWith('en')?'en':'fa-IR')+(row.type==='percentage'?'%':''),row.organizationName].map(value=>({value,attributes:'class="px-5 py-4 whitespace-nowrap"'})); }
function renderTermDiscounts(){
  const table=document.getElementById('termDiscountsTable');if(!table||!termDiscountCatalog)return;
  const search=(document.getElementById('termDiscountSearch')?.value||'').trim().toLocaleLowerCase(),type=document.getElementById('termDiscountTypeFilter')?.value||'';
  const rows=(termDiscountCatalog.discounts||[]).filter(row=>(!type||row.type===type)&&(!search||String(row.name).toLocaleLowerCase().includes(search))&&(!window.matchesOrganizationFilter||window.matchesOrganizationFilter(row,termDiscountOrganization)));
  termDiscountPage=Math.min(termDiscountPage,Math.max(1,Math.ceil(rows.length/10)));
  const start=(termDiscountPage-1)*10,visible=window.AdminPanelTable.page(table,rows,start,start+10,renderTermDiscounts, undefined, discountCells);
  const cells=visible.map(row=>({attributes:'data-id="'+Number(row.id)+'"',cells:discountCells(row).concat([{html:discountRowActions(row),attributes:'class="px-5 py-4 whitespace-nowrap"'}])}));
  window.AdminPanelTable.setRows(table,cells.length?cells:[{cells:[{value:discountText('هیچ تخفیفی یافت نشد','No discounts found'),attributes:'colspan="5" class="py-12 text-center text-gray-400"'}]}],{pageOffset:start});
  renderDiscountInlineEditor(table);
  window.AdminPanelTable.renderPagination(table.closest('.admin-ui-table')?.querySelector('[data-panel-pagination]'),{total:rows.length,perPage:10,page:termDiscountPage,onChange:page=>{termDiscountPage=page;renderTermDiscounts();}});
}
function discountRowActions(row){
 const id=Number(row.id);
 return '<div class="inline-flex gap-3"><button type="button" class="text-indigo-600" onclick="viewTermDiscount('+id+')">'+discountText('جزئیات','Details')+'</button>'+(row.canManage?'<button type="button" class="text-indigo-600" onclick="toggleTermDiscountEdit('+id+')">'+discountText('ویرایش','Edit')+'</button><button type="button" class="text-red-600" onclick="deleteTermDiscount('+id+')">'+discountText('حذف','Delete')+'</button>':'')+'</div>';
}
window.viewTermDiscount=function(id){
 const row=termDiscountCatalog?.discounts.find(row=>row.id===id);if(!row)return;
 const host=document.getElementById('modalContainer');host.innerHTML='<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"><div role="dialog" aria-modal="true" class="w-full max-w-lg rounded-3xl bg-white p-6 dark:bg-slate-900"><h2 class="mb-5 text-xl font-bold"></h2><dl class="space-y-4"></dl><button type="button" onclick="closeModal()" class="mt-5 rounded-xl border px-5 py-2"></button></div></div>';
 const dialog=host.querySelector('[role=dialog]');dialog.querySelector('h2').textContent=discountText('جزئیات تخفیف','Discount details');dialog.querySelector('button').textContent=discountText('بستن','Close');
 for(const [label,value]of [[discountText('عنوان','Title'),row.name],[discountText('نوع','Type'),row.type==='fixed'?discountText('مبلغ ثابت','Fixed amount'):discountText('درصدی','Percentage')],[discountText('مقدار','Value'),row.value],[discountText('سازمان','Organization'),row.organizationName],[discountText('تاریخ شروع','Start date'),row.startDate],[discountText('تاریخ پایان','End date'),row.endDate]]){const wrapper=document.createElement('div');wrapper.className='flex justify-between gap-4';const dt=document.createElement('dt'),dd=document.createElement('dd');dt.textContent=label;dd.textContent=value??'?';wrapper.append(dt,dd);dialog.querySelector('dl').append(wrapper);}
};
window.toggleTermDiscountEdit=function(id){editingTermDiscount=editingTermDiscount===id?null:id;renderTermDiscounts();};
function renderDiscountInlineEditor(table){
 const row=termDiscountCatalog?.discounts.find(row=>row.id===editingTermDiscount);if(!row?.canManage)return;
 const target=table.querySelector('tbody tr[data-id="'+Number(row.id)+'"]');if(!target)return;
 const tr=document.createElement('tr');tr.dataset.discountEditor=String(row.id);const td=document.createElement('td');td.colSpan=table.tHead.rows[0].cells.length;td.className='border-t p-5';
 td.innerHTML='<form class="grid gap-4 sm:grid-cols-3"><label><span data-label="title"></span><input required maxlength="190" name="title" class="mt-2 w-full rounded-2xl border p-3"></label><label><span data-label="type"></span><select name="type" class="mt-2 w-full rounded-2xl border p-3"></select></label><label><span data-label="value"></span><input required name="value" type="number" min="0.01" step="0.01" class="mt-2 w-full rounded-2xl border p-3"></label><label><span data-label="organization"></span><select data-panel-organization-field data-panel-organization-source="discountOrganizations" required name="organizationUserId" class="mt-2 w-full rounded-2xl border p-3"></select></label><div class="flex items-end gap-3"><button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 text-white"></button><button type="button" class="rounded-xl border px-5 py-3" onclick="toggleTermDiscountEdit('+Number(row.id)+')"></button></div></form>';
 const form=td.querySelector('form');for(const[key,fa,en]of [['title','عنوان','Title'],['type','نوع','Type'],['value','مقدار','Value'],['organization','سازمان','Organization']])form.querySelector('[data-label='+key+']').textContent=discountText(fa,en);
 form.elements.title.value=row.name;form.elements.value.value=row.value;form.elements.type.add(new Option(discountText('درصدی','Percentage'),'percentage'));form.elements.type.add(new Option(discountText('مبلغ ثابت','Fixed amount'),'fixed'));form.elements.type.value=row.type;
 (termDiscountCatalog.organizations||[]).forEach(org=>form.elements.organizationUserId.add(new Option(org.name,org.user_id)));if(row.organizationUserId){form.elements.organizationUserId.value=String(row.organizationUserId);form.elements.organizationUserId.disabled=true;}
 form.querySelector('[type=submit]').textContent=discountText('ذخیره','Save');form.querySelector('[type=button]').textContent=discountText('انصراف','Cancel');
 form.onsubmit=async event=>{event.preventDefault();const button=form.querySelector('[type=submit]');if(button.disabled)return;button.disabled=true;try{await termApi('/academy/admin/term-discounts/'+row.id+'/update',{title:form.elements.title.value.trim(),type:form.elements.type.value,value:Number(form.elements.value.value),organizationUserId:Number(form.elements.organizationUserId.value)});editingTermDiscount=null;await loadTermDiscountCatalog();}catch(error){alert(error.message);}finally{if(button.isConnected)button.disabled=false;}};
 tr.append(td);target.after(tr);
}
window.deleteTermDiscount=async function(id){
 const row=termDiscountCatalog?.discounts.find(row=>row.id===id);if(!row?.canManage)return;
 if(!(await AppDialog.confirmDelete(termDiscountCatalog.discounts,id,discountText('تخفیف','discount'))))return;
 try{await termApi('/academy/admin/term-discounts/'+id+'/delete',{});if(editingTermDiscount===id)editingTermDiscount=null;await loadTermDiscountCatalog();}catch(error){alert(error.message);}
};
window.openAddTermDiscountModal=async function(prefix){
 try{
  if(!termDiscountCatalog)await loadTermDiscountCatalog();
  if(!termDiscountCatalog.ready)throw Error(discountText('ابتدا به‌روزرسانی دیتابیس تخفیف‌ها را اجرا کنید.','Run the discount organization migration first.'));
  const host=document.getElementById('modalContainer');window.pendingTermModal=prefix?{html:host.innerHTML,prefix}:null;
  host.innerHTML='<div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4"><div role="dialog" aria-modal="true" aria-labelledby="newTermDiscountHeading" class="w-full max-w-lg rounded-3xl bg-white p-7 dark:bg-slate-900"><h2 id="newTermDiscountHeading" class="mb-5 text-xl font-bold"></h2><form id="newTermDiscountForm" class="space-y-4"><label class="block" data-discount-label="organization"><select data-panel-organization-field data-panel-organization-source="discountOrganizations" id="newTermDiscountOrganization" required class="mt-2 w-full rounded-2xl border p-4"></select></label><label class="block" data-discount-label="title"><input id="newTermDiscountTitle" required maxlength="190" class="mt-2 w-full rounded-2xl border p-4"></label><label class="block" data-discount-label="type"><select id="newTermDiscountType" class="mt-2 w-full rounded-2xl border p-4"></select></label><label class="block" data-discount-label="value"><input id="newTermDiscountValue" type="number" required min="0.01" step="0.01" class="mt-2 w-full rounded-2xl border p-4"></label><div class="flex gap-3"><button id="saveTermDiscountButton" type="submit" class="flex-1 rounded-2xl bg-indigo-600 p-3 text-white disabled:opacity-50"></button><button id="cancelTermDiscountButton" type="button" onclick="restoreTermModal()" class="flex-1 rounded-2xl border p-3"></button></div></form></div></div>';
  document.getElementById('newTermDiscountHeading').textContent=discountText('افزودن تخفیف جدید','Add discount');
  for(const[key,fa,en]of [['organization','سازمان','Organization'],['title','عنوان تخفیف','Title'],['type','نوع','Type'],['value','مقدار','Value']])document.querySelector('[data-discount-label="'+key+'"]').prepend(document.createTextNode(discountText(fa,en)));
  const org=document.getElementById('newTermDiscountOrganization');(termDiscountCatalog.organizations||[]).forEach(row=>org.add(new Option(row.name,row.user_id)));
  const selectedOrganization=(termDiscountCatalog.organizations||[]).find(row=>termDiscountOrganization==='academy'?row.kind==='academy':row.kind==='branch'&&String(row.id)===termDiscountOrganization);
  if(prefix&&termField(prefix,'Branch')?.value)org.value=termField(prefix,'Branch').value;else if(selectedOrganization)org.value=String(selectedOrganization.user_id);
  const type=document.getElementById('newTermDiscountType');type.add(new Option(discountText('درصدی','Percentage'),'percentage'));type.add(new Option(discountText('مبلغ ثابت','Fixed amount'),'fixed'));
  document.getElementById('saveTermDiscountButton').textContent=discountText('ذخیره','Save');document.getElementById('cancelTermDiscountButton').textContent=discountText('انصراف','Cancel');
  document.getElementById('newTermDiscountForm').onsubmit=event=>{event.preventDefault();saveNewTermDiscount();};
 }catch(error){alert(error.message);}
};
window.restoreTermModal=function(){if(window.pendingTermModal){document.getElementById('modalContainer').innerHTML=window.pendingTermModal.html;window.pendingTermModal=null;}else closeModal();};
window.saveNewTermDiscount=async function(){
 if(savingTermDiscount)return;const form=document.getElementById('newTermDiscountForm');if(!form?.reportValidity())return;
 const button=document.getElementById('saveTermDiscountButton');savingTermDiscount=true;button.disabled=true;
 try{
  const data={title:document.getElementById('newTermDiscountTitle').value.trim(),type:document.getElementById('newTermDiscountType').value,value:Number(document.getElementById('newTermDiscountValue').value),organizationUserId:Number(document.getElementById('newTermDiscountOrganization').value)};
  const row=await termApi('/academy/admin/term-discounts',data),state=window.pendingTermModal;
  restoreTermModal();await loadTermDiscountCatalog();
  if(state){const select=termField(state.prefix,'Discount');if(select){select.add(new Option(row.name,row.id));select.value=String(row.id);}}
 }catch(error){alert(error.message);}finally{savingTermDiscount=false;if(button.isConnected)button.disabled=false;}
};

window.refreshTermBatchRangeOptions = async function () {
  const form = document.querySelector('[data-term-batch]');
  if (!form) return;
  const start = termField('', 'RangeStart'), end = termField('', 'RangeEnd');
  const organization = termBranches.find(row => Number(row.id) === Number(termField('', 'Branch')?.value));
  const room = termClassrooms.find(row => Number(row.id) === Number(termField('', 'Classroom')?.value));
  const serial = String(Number(form.dataset.rangeSerial || 0) + 1);
  form.dataset.rangeSerial = serial;
  const preferredStart = start.value || start.dataset.preferredStart, preferredEnd = end.value || end.dataset.preferredStart;
  start.disabled = end.disabled = true;
  window.refreshTermBatchPreview();
  if (!organization) { start.replaceChildren(); end.replaceChildren(); window.refreshTermBatchPreview(); return; }
  try {
    const branch = Number(room?.branchId || (organization.kind === 'branch' ? organization.organizationId : 0));
    const academy = Number(organization.kind === 'academy' ? organization.organizationId : organization.academyId);
    const query = new URLSearchParams({branch, academy, weekday: termField('', 'RecurrenceWeekday').value, duration:5});
    const data = await termApi('/academy/admin/term-available-times?' + query);
    if (!form.isConnected || form.dataset.rangeSerial !== serial) return;
    const times = data.times || [];
    const ends = [...new Set(times.map(time => { const minute = Number(time.slice(0,2))*60+Number(time.slice(3))+5; return minute < 1440 ? String(Math.floor(minute/60)).padStart(2,'0')+':'+String(minute%60).padStart(2,'0') : ''; }).filter(Boolean))];
    start.innerHTML = window.getTermTimeOptions(times, preferredStart);
    end.innerHTML = window.getTermTimeOptions(ends, preferredEnd);
    start.value = times.includes(preferredStart) ? preferredStart : times[0] || '';
    end.value = ends.includes(preferredEnd) ? preferredEnd : ends.at(-1) || '';
    start.disabled = !times.length; end.disabled = !ends.length;
    if (data.timezoneId) termField('', 'Timezone').value = String(data.timezoneId);
  } catch (error) {
    if (!form.isConnected || form.dataset.rangeSerial !== serial) return;
    start.replaceChildren(); end.replaceChildren();
    form.querySelector('[data-batch-error]').textContent = error.message;
  }
  window.refreshTermBatchPreview();
};

window.refreshTermBatchPreview = function () {
  const form = document.querySelector('[data-term-batch]');
  if (!form) return 0;
  const minutes = value => /^\d{2}:\d{2}$/.test(value) ? Number(value.slice(0, 2)) * 60 + Number(value.slice(3)) : NaN;
  const duration = Number(termField('', 'DurationMinutes')?.value), span = minutes(termField('', 'RangeEnd')?.value || '') - minutes(termField('', 'RangeStart')?.value || '');
  const valid = !termField('', 'RangeStart')?.disabled && !termField('', 'RangeEnd')?.disabled && Number.isInteger(duration) && duration >= 5 && duration % 5 === 0 && span > 0 && span % duration === 0;
  const count = valid ? span / duration : 0;
  const en = document.documentElement.lang.startsWith('en');
  form.querySelector('[data-batch-preview]').textContent = valid ? (en ? `${count} separate terms will be created.` : `${count.toLocaleString('fa-IR')} ترم مستقل ایجاد می‌شود.`) : (en ? 'Choose a range divisible by the session duration.' : 'بازه‌ای انتخاب کنید که دقیقاً بر مدت هر جلسه تقسیم شود.');
  if (form.dataset.busy !== '1') form.querySelector('[type=submit]').disabled = !valid;
  return count;
};

window.saveTermBatch = async function () {
  const form = document.querySelector('[data-term-batch]');
  if (!form || form.dataset.busy === '1' || !form.reportValidity() || !window.refreshTermBatchPreview()) return;
  const field = name => termField('', name)?.value || '';
  const teachers = [...form.querySelectorAll('.term-teacher-select')].map(select => ({id: Number(select.value)})).filter(row => row.id);
  const en = document.documentElement.lang.startsWith('en');
  if (teachers.length !== 1) { form.querySelector('[data-batch-error]').textContent = en ? 'Select one teacher.' : 'یک استاد انتخاب کنید.'; return; }
  const data = {name:field('Name').trim(), organizationUserId:Number(field('Branch')), courseId:Number(field('Course')), classroomId:Number(field('Classroom')), currencyId:Number(field('Currency')), cost:field('Cost'), sessionCount:Number(field('SessionCount')), repeatType:field('RepeatType'), weekday:field('RecurrenceWeekday'), timezoneId:Number(field('Timezone')), durationMinutes:Number(field('DurationMinutes')), rangeStart:field('RangeStart'), rangeEnd:field('RangeEnd'), teachers, summary:field('Summary').trim(), description:field('Description').trim(), requestKey:form.dataset.requestKey};
  const button = form.querySelector('[type=submit]');
  form.dataset.busy = '1'; button.disabled = true; form.querySelector('[data-batch-error]').textContent = '';
  try {
    const result = await termApi('/academy/admin/terms/batch', data);
    await loadTerms(); closeModal();
    alert(en ? `${result.count} terms created.` : `${Number(result.count).toLocaleString('fa-IR')} ترم ایجاد شد.`);
  } catch (error) { form.querySelector('[data-batch-error]').textContent = error.message; }
  finally { form.dataset.busy = ''; if (button.isConnected) window.refreshTermBatchPreview(); }
};

window.canAssignTermStudent = function (item) {
  if (!item || !['open', 'pending'].includes(item.status_code)) return false;
  if (typeof item.canAssignStudent === 'boolean') return item.canAssignStudent;
  const course = (window.termCourses || []).find(row => row.id === item.courseId);
  return (item.students || []).length < Number(item.studentCapacity || course?.student_capacity || 1);
};
window.openTermStudentAssignment = async function (id) {
  const item = allTerms.find(row => row.id === id), course = termCourses.find(row => row.id === item?.courseId);
  if (!course || !window.canAssignTermStudent(item)) return;
  try {
    await window.ensureStudentFormOptions();
    document.getElementById('modalContainer').innerHTML = window.getStudentAddModalHTML({assignmentTermId: id, organizationUserId: course.organizationUserId});
    window.initTermStudentWizard(id);
    window.initLocalizedDateInputs?.(document.getElementById('modalContainer'));
  } catch (error) { alert(error.message); }
};

window.openDashboardTermClassroom = async function (id) {
  window.showSection?.('terms');
  await window.loadTerms?.();
  window.openTermClassroomAssignment(Number(id));
};
window.openTermClassroomAssignment = function (id) {
  const item = allTerms.find(row => row.id === id), course = termCourses.find(row => row.id === item?.courseId);
  if (!item || !item.students?.length || !['open', 'pending', 'ongoing'].includes(item.status_code) || !course) return;
  const en = document.documentElement.lang.startsWith('en');
  const host = document.getElementById('modalContainer');
  host.innerHTML = '<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"><form data-assign-term-classroom role="dialog" aria-modal="true" class="w-full max-w-lg space-y-4 rounded-3xl bg-white p-6 dark:bg-slate-900"><h2 class="text-xl font-bold"></h2><select required class="w-full rounded-2xl border p-3 dark:bg-slate-800"></select><p role="alert" class="text-sm text-red-600"></p><button type="submit" class="rounded-2xl bg-indigo-600 px-6 py-3 text-white"></button><button type="button" onclick="closeModal()" class="rounded-2xl border px-6 py-3"></button></form></div>';
  const form = host.querySelector('form'), select = form.querySelector('select');
  form.querySelector('h2').textContent = item.classroomId ? (en ? 'Change classroom' : 'تغییر کلاس') : (en ? 'Assign classroom' : 'تعیین کلاس برگزاری');
  select.add(new Option(en ? 'Select classroom' : 'انتخاب کلاس', ''));
  termClassrooms.filter(room => course.organizationKind === 'branch' ? Number(room.branchId) === Number(course.organizationId) : Number(room.academyId) === Number(course.organizationId) || termBranches.some(org => org.kind === 'branch' && Number(org.organizationId) === Number(room.branchId) && Number(org.academyId) === Number(course.organizationId))).forEach(room => select.add(new Option(room.name, room.id)));
  select.value = String(item.classroomId || '');
  form.querySelector('[type=submit]').textContent = en ? 'Save' : 'ذخیره'; form.querySelector('[type=button]').textContent = en ? 'Skip for now' : 'فعلاً رد کردن';
  form.onsubmit = async event => {
    event.preventDefault(); const button = form.querySelector('[type=submit]'); if (button.disabled) return; button.disabled = true;
    try { await termApi(`/academy/admin/terms/${id}/classroom`, {classroomId:Number(select.value)}); await loadTerms(); closeModal(); window.refreshDashboard?.(); }
    catch (error) { form.querySelector('[role=alert]').textContent = error.message; }
    finally { if (button.isConnected) button.disabled = false; }
  };
};

window.openTeacherTermBatchModal = function () {
  const host = document.getElementById('modalContainer');
  if (!host || !window.getTermBatchAddModalHTML) return;
  host.innerHTML = window.getTermBatchAddModalHTML();
  const form = host.querySelector('[data-term-batch]');
  form.dataset.requestKey = [...crypto.getRandomValues(new Uint8Array(16))].map(value => value.toString(16).padStart(2, '0')).join('');
  refreshTermDependencies('');
  window.refreshTermBatchRangeOptions();
  window.refreshTermBatchPreview();
};

window.openAddTermModal = async function () {
  if (!document.getElementById('modalContainer')) {
    alert('خطا: المان modalContainer در صفحه اصلی وجود ندارد!');
    return;
  }
  document.getElementById('modalContainer').innerHTML = window.getTermAddModalHTML ? window.getTermAddModalHTML()
    : '';
  window.initLocalizedDateInputs?.(document.getElementById('modalContainer'));
  window.syncNewTermRecurrence?.('');
  syncTermFinancialFields('');
  syncTermPeopleVisibility('');
  refreshTermDependencies('');
};

async function validateTermScheduleBeforeSave(data, excludeTerm) {
  if (data.undatedTemplate) return true;
  const maxInstallments = Math.max(2, data.sessions.length),
    organization = termBranches.find((x) => x.id === data.organizationUserId),
    label = organization?.kind === 'academy' ? 'آموزشگاه' : 'شعبه';
  if (data.installmentCount > maxInstallments)
    throw new Error(`تعداد اقساط نمی‌تواند بیشتر از ${maxInstallments} باشد.`);
  for (let i = 0; i < data.sessions.length; i++) {
    const session = data.sessions[i],
      query = new URLSearchParams({
        branch: data.branchId,
        classroom: data.classroomId,
        date: session.date,
        duration: session.durationMinutes,
        excludeTerm: excludeTerm || 0,
        organizationKind: organization?.kind || 'branch',
        organizationUserId: data.organizationUserId,
        timezoneId: session.timezoneId || 0,
      }),
      result = await termApi('/academy/admin/term-available-times?' + query);
    if (result.closed) throw new Error(`${label} در تاریخ جلسه ${i + 1} تعطیل است.`);
    if (result.full)
      throw new Error(`کلاس انتخاب‌شده در تاریخ جلسه ${i + 1} ظرفیت زمانی خالی ندارد.`);
    if (!(result.times || []).includes(session.startTime))
      throw new Error(`ساعت شروع جلسه ${i + 1} خارج از ساعات مجاز یا دارای تداخل است.`);
  }
  return true;
}

window.saveTerm = async function () {
  const data = readTermForm('');
  if (data.undatedTemplate) {
    const label = termField('', 'RepeatType')?.selectedOptions[0]?.textContent.trim() || '';
    const visiblePeriods = {
      'هفتگی': 'week', 'دو هفته یک‌بار': '2-week', 'سه هفته یک‌بار': '3-week',
      'چهار هفته یک‌بار': '4-week', 'ماهانه': 'month', 'سالانه': 'year',
    };
    data.repeatType = visiblePeriods[label] || data.repeatType;
    if (data.repeatType === 'no-period' && data.sessionCount > 1 && !data.recurrence?.exactDate && /^[0-6]$/.test(String(data.recurrence?.weekday ?? ''))) {
      data.repeatType = 'week';
    }
    if (data.recurrence) data.recurrence.period = data.repeatType;
  }
  if (!validateTermData(data)) return;
  try {
    await validateTermScheduleBeforeSave(data, 0);
    await termApi('/academy/admin/terms', data);
    await loadTerms();
    closeModal();
    alert('✅ ترم با موفقیت اضافه شد');
  } catch (e) {
    alert(e.message);
  }
};

window.viewTerm = async function (id) {
  const item = allTerms.find(function (x) {
    return x.id === id;
  });
  if (!item) return;
  document.getElementById('modalContainer').innerHTML = window.getTermDetailsModalHTML
    ? window.getTermDetailsModalHTML(item)
    : '';
};

function readOpenTermTeachers(prefix, id) {
  const container = termField(prefix, 'TeachersContainer');
  if (!container || allTerms.find(row => row.id === id)?.status_code !== 'open' || window.termPermissions?.isReceptionist) return {};
  return {teachers: [...container.querySelectorAll('select')].filter(select => select.value).map(select => ({id: Number(select.value)}))};
}
function restrictRecordedTermEditor(prefix) {
  const root = document.querySelector(`[data-term-record-editor="${prefix}"]`);
  if (!root) return;
  const editable = new Set([prefix + 'Name', prefix + 'Summary', prefix + 'Description']);
  const item = allTerms.find(row => row.id === Number(termField(prefix, 'SessionsContainer')?.dataset.termId));
  const teachers = item?.status_code === 'open' && !window.termPermissions?.isReceptionist ? termField(prefix, 'TeachersContainer') : null;
  root.querySelectorAll('input, select, textarea, button').forEach((control) => {
    if (!editable.has(control.id) && !teachers?.contains(control)) {
      control.disabled = true;
      control.classList.add('opacity-60');
    }
  });
}

function openTermMetadataEditor(item) {
  const en = document.documentElement.lang === 'en';
  const label = en
    ? [
        'Edit term details',
        'Use session and invoice management to change schedules or tuition. Existing records are preserved.',
        'Title',
        'Summary',
        'Description',
        'Save',
        'Cancel',
      ]
    : [
        'ویرایش توضیحات ترم',
        'برای تغییر برنامه یا شهریه از مدیریت جلسات و فاکتورها استفاده کنید. سوابق قبلی حفظ می‌شوند.',
        'عنوان',
        'خلاصه',
        'توضیحات',
        'ذخیره',
        'انصراف',
      ];
  document.getElementById('modalContainer').innerHTML =
    `<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"><div class="w-full max-w-xl rounded-3xl bg-white p-6 text-gray-900"><h2 class="text-xl font-bold">${label[0]}</h2><p class="my-4 text-sm">${label[1]}</p><label class="block">${label[2]}<input required id="editTermName" value="${escapeHtml(item.name)}" class="my-2 w-full rounded-xl border p-3"></label><label class="block">${label[3]}<textarea id="editTermSummary" class="my-2 w-full rounded-xl border p-3">${escapeHtml(item.summary || '')}</textarea></label><label class="block">${label[4]}<textarea id="editTermDescription" class="my-2 w-full rounded-xl border p-3">${escapeHtml(item.description || '')}</textarea></label><div class="mt-4 flex gap-3"><button onclick="saveEditedTerm(${Number(item.id)})" class="rounded-xl bg-indigo-600 px-5 py-3 text-white">${label[5]}</button><button onclick="closeModal()" class="rounded-xl border px-5 py-3">${label[6]}</button></div></div></div>`;
}

window.editTerm = async function (id) {
  const item = allTerms.find((x) => x.id === id);
  if (!item) return;
  if (typeof window.getTermEditModalHTML !== 'function') return openTermMetadataEditor(item);
  document.getElementById('modalContainer').innerHTML = window.getTermEditModalHTML(item);
  restrictRecordedTermEditor('editTerm');
};

window.saveEditedTerm = async function (id) {
  const data = {
    metadataOnly: true,
    name: termField('editTerm', 'Name').value.trim(),
    summary: termField('editTerm', 'Summary').value,
    description: termField('editTerm', 'Description').value,
    ...readOpenTermTeachers('editTerm', id),
  };
  if (!data.name) {
    termField('editTerm', 'Name').reportValidity();
    return;
  }
  const index = allTerms.findIndex(function (x) {
    return x.id === id;
  });
  if (index === -1) return;
  try {
    await termApi('/academy/admin/terms/' + id + '/update', data);
    await loadTerms();
    editingTermRowId = null;
    closeModal();
    alert('✅ تغییرات ذخیره شد');
  } catch (e) {
    alert(e.message);
  }
};

window.toggleTermInlineEdit = async function (id) {
  attendanceTermRowId = null;
  editingTermRowId = editingTermRowId === id ? null : id;
  renderTermsTable(filteredTerms);
};
window.cycleTermStatus = async function (id) {
  const item = allTerms.find(row => row.id === id);
  if (!item || window.termPermissions?.isReceptionist) return;
  closeTermInlineEdit();
  const en = document.documentElement.lang.startsWith('en');
  const labels = en ? {pending:'Pending approval',open:'Open',ongoing:'Ongoing',finished:'Finished',canceled:'Canceled',suspended:'Suspended'} : (window.termStatusOptions || {pending:'در انتظار تأیید',open:'باز',ongoing:'در حال برگزاری',finished:'پایان یافته',canceled:'لغو شده',suspended:'تعلیق شده'});
  const terminal = ['finished','canceled'].includes(item.status_code);
  const options = Object.entries(labels).filter(([status]) => !terminal || status === item.status_code).map(([value,label]) => `<option value="${value}" ${value === item.status_code ? 'selected' : ''}>${escapeHtml(label)}</option>`).join('');
  document.getElementById('modalContainer').innerHTML = `<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"><div role="dialog" aria-modal="true" class="w-full max-w-lg rounded-3xl bg-white p-6 dark:bg-slate-900"><h2 class="text-xl font-bold">${en ? 'Change term status' : 'تغییر وضعیت ترم'}</h2><p class="my-4 text-sm text-gray-500">${en ? 'Cancellation ends an unfinished term. Suspension pauses it until classes can resume. Existing records and invoices are preserved.' : 'لغو، ترم ناتمام را متوقف می‌کند. تعلیق برای توقف موقت تا زمان ادامهٔ برگزاری است. سوابق جلسات و فاکتورها حفظ می‌شوند.'}</p><select id="termStatusChoice" class="w-full rounded-xl border p-3">${options}</select><div class="mt-5 flex gap-3"><button type="button" onclick="saveTermStatus(${Number(id)},this)" class="rounded-xl bg-indigo-600 px-5 py-3 text-white">${en ? 'Save status' : 'ثبت وضعیت'}</button><button type="button" onclick="closeModal()" class="rounded-xl border px-5 py-3">${en ? 'Close' : 'بستن'}</button></div><p data-term-status-error role="alert" class="mt-3 text-sm text-red-600"></p></div></div>`;
};
window.saveTermStatus = async function (id, button) {
  const item = allTerms.find(row => row.id === id), status = document.getElementById('termStatusChoice')?.value;
  if (!item || !status || button.disabled) return;
  button.disabled = true;
  try {
    await termApi('/academy/admin/terms/' + id + '/set-status', {status, fromStatus:item.status_code});
    await loadTerms();
    closeModal();
  } catch (error) {
    document.querySelector('[data-term-status-error]').textContent = error.message;
  } finally { button.disabled = false; }
};

window.saveInlineTerm = async function (id) {
  const prefix = 'inlineTerm' + id;
  const data = {
    metadataOnly: true,
    name: termField(prefix, 'Name')?.value.trim() || '',
    summary: termField(prefix, 'Summary')?.value || '',
    description: termField(prefix, 'Description')?.value || '',
    ...readOpenTermTeachers(prefix, id),
  };
  if (!data.name) return termField(prefix, 'Name')?.reportValidity();
  const index = allTerms.findIndex(function (x) {
    return x.id === id;
  });
  if (index === -1) return;
  try {
    await termApi('/academy/admin/terms/' + id + '/update', data);
    editingTermRowId = null;
    await loadTerms();
    alert('✅ تغییرات با موفقیت ذخیره شد');
  } catch (e) {
    alert(e.message);
  }
};

window.deleteTerm = async function (id) {
  if (!(await AppDialog.confirmDelete(allTerms, id, 'ترم'))) return;
  await termApi('/academy/admin/terms/' + id + '/delete', {});
  allTerms = allTerms.filter(function (t) {
    return t.id !== id;
  });
  if (editingTermRowId === id) editingTermRowId = null;
  if (attendanceTermRowId === id) attendanceTermRowId = null;
  renderTermFilters();
  filterTerms();
};

// ==================== attendance ====================
window.toggleTermInlineAttendance = async function (id) {
  editingTermRowId = null;
  attendanceTermRowId = attendanceTermRowId === id ? null : id;
  renderTermsTable(filteredTerms);
  if (attendanceTermRowId === id) await loadTermAttendance(id);
};

window.openTermAttendanceModal = async function (id) {
  const item = allTerms.find(function (x) {
    return x.id === id;
  });
  if (!item) return;
  document.getElementById('modalContainer').innerHTML = window.getTermAttendanceModalHTML
    ? window.getTermAttendanceModalHTML(item)
    : '';
  await loadTermAttendance(id);
};

async function loadTermAttendance(id) {
  const item = allTerms.find(row => row.id === id);
  if (!item) return;
  const root = document.getElementById('termAttendancePanel-' + id);
  if (!root) return;
  try {
    const sessions = [];
    let page = 1, total;
    do {
      const result = await termApi(`/academy/admin/class-schedules?termId=${Number(id)}&perPage=100&page=${page++}`);
      const rows = result.schedules || [];
      sessions.push(...rows);
      total = Number(result.total || 0);
      if (!rows.length) break;
    } while (sessions.length < total);
    item.attendanceSessions = sessions.sort((a, b) => String(a.date).localeCompare(String(b.date)) || String(a.startTime).localeCompare(String(b.startTime)) || a.id - b.id);
    if (root.isConnected) root.outerHTML = window.getTermAttendancePanelHTML(item, true);
  } catch (error) {
    if (root.isConnected) root.textContent = error.message;
  }
}

window.saveTermSessionAttendance = async function (termId, sessionId, button) {
  if (button.disabled) return;
  const root = button.closest(`#scheduleAttendancePanel-${Number(sessionId)}`);
  if (!root) return;
  const attendance = [...root.querySelectorAll('.schedule-att-status')].map(select => ({
    memberId: Number(select.dataset.member), status: select.value,
    description: root.querySelector(`.schedule-att-description[data-member="${Number(select.dataset.member)}"]`)?.value || '',
  }));
  const note = root.querySelector('[data-teacher-note]');
  const result = root.querySelector('[data-attendance-result]');
  button.disabled = true;
  try {
    await termApi(`/academy/admin/class-schedules/${Number(sessionId)}/attendance`, {attendance, ...(note ? {teacherNote: note.value} : {})});
    const session = allTerms.find(row => row.id === termId)?.attendanceSessions?.find(row => row.id === sessionId);
    if (session) {
      session.attendance = session.attendance.map(row => ({...row, ...attendance.find(value => value.memberId === Number(row.id))}));
      if (note) session.teacherNote = note.value;
    }
    result.textContent = document.documentElement.lang.startsWith('en') ? 'Saved.' : 'ذخیره شد.';
    await window.refreshStudentsFromDatabase?.();
  } catch (error) { result.textContent = error.message; }
  finally { button.disabled = false; }
};

// ==================== excel / pdf ====================
window.exportTermsToExcel = async function () {
  const data = filteredTerms.length ? filteredTerms : allTerms;
  let csv = '\uFEFF';
  csv += 'ردیف,نام ترم,شعبه,دوره مرتبط,تاریخ شروع,تاریخ پایان,وضعیت,هزینه,واحد پول\n';
  data.forEach(function (item, index) {
    csv +=
      index +
      1 +
      ',"' +
      item.name +
      '","' +
      item.branchName +
      '","' +
      (item.course || '') +
      '",' +
      (item.start || '') +
      ',' +
      (item.end || '') +
      ',"' +
      item.status +
      '",' +
      (item.cost || 0) +
      ',"' +
      (item.currency || '') +
      '"\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'ترم‌ها_' + new Date().toLocaleDateString('fa-IR') + '.csv';
  link.click();
};

window.exportTermsToPDF = async function () {
  openTermsPDFOptionsModal();
};

window.openTermsPDFOptionsModal = async function () {
  document.getElementById('modalContainer').innerHTML = window.getTermPDFModalHTML
    ? window.getTermPDFModalHTML(termPdfColumns)
    : '';
};

window.generateTermsPDF = async function () {
  if (!window.html2canvas) {
    alert('ابزار تولید PDF بارگذاری نشده است. لطفاً صفحه را مجدداً بارگذاری کنید.');
    return;
  }

  const title =
    (document.getElementById('termPdfTitle') && document.getElementById('termPdfTitle').value) ||
    'گزارش ترم‌های آموزشگاه';
  const subtitle =
    (document.getElementById('termPdfSubtitle') &&
      document.getElementById('termPdfSubtitle').value) ||
    'لیست ترم‌ها، دوره‌ها و وضعیت برگزاری';
  const footer =
    (document.getElementById('termPdfFooter') && document.getElementById('termPdfFooter').value) ||
    '';
  const format =
    (document.getElementById('termPdfFormat') && document.getElementById('termPdfFormat').value) ||
    'a4';
  const orientation =
    (document.getElementById('termPdfOrientation') &&
      document.getElementById('termPdfOrientation').value) ||
    'landscape';
  const includeDate =
    document.getElementById('termPdfIncludeDate') &&
    document.getElementById('termPdfIncludeDate').checked;
  const headerColor =
    (document.getElementById('termPdfHeaderColor') &&
      document.getElementById('termPdfHeaderColor').value) ||
    '#eff6ff';
  const evenRowColor =
    (document.getElementById('termPdfEvenRowColor') &&
      document.getElementById('termPdfEvenRowColor').value) ||
    '#ffffff';
  const oddRowColor =
    (document.getElementById('termPdfOddRowColor') &&
      document.getElementById('termPdfOddRowColor').value) ||
    '#f8fafc';
  const selectedColumns = termPdfColumns.filter(function (col) {
    return (
      document.getElementById('termPdfCol-' + col.field) &&
      document.getElementById('termPdfCol-' + col.field).checked
    );
  });
  const date = new Date().toLocaleDateString('fa-IR');
  const data = (filteredTerms.length ? filteredTerms : allTerms).map(function (item) {
    return Object.assign({}, item, {
      cost:
        item.cost != null
          ? Number(item.cost).toLocaleString('fa-IR') + ' ' + (item.currency || '')
          : '—',
    });
  });

  if (!selectedColumns.length) {
    alert('لطفاً حداقل یک ستون برای خروجی PDF انتخاب کنید.');
    return;
  }

  const rowsPerPage = orientation === 'portrait' ? 18 : 15;
  const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
  const canvasPages = [];

  for (let pageIndex = 0; pageIndex < totalPages; pageIndex++) {
    const pageRows = data.slice(pageIndex * rowsPerPage, (pageIndex + 1) * rowsPerPage);
    const pageWrapper = document.createElement('div');
    pageWrapper.style.direction = 'rtl';
    pageWrapper.style.position = 'fixed';
    pageWrapper.style.top = '-9999px';
    pageWrapper.style.left = '-9999px';
    pageWrapper.style.width = orientation === 'portrait' ? '900px' : '1400px';
    pageWrapper.style.padding = pageIndex === 0 ? '20px 30px 30px' : '30px';
    pageWrapper.style.backgroundColor = '#ffffff';
    pageWrapper.style.fontFamily = 'Vazirmatn, Tahoma, sans-serif';
    pageWrapper.innerHTML = window.getTermPDFPageHTML
      ? window.getTermPDFPageHTML(pageIndex + 1, pageRows, pageIndex === 0, {
          title: title,
          subtitle: subtitle,
          footer: footer,
          includeDate: includeDate,
          date: date,
          headerColor: headerColor,
          evenRowColor: evenRowColor,
          oddRowColor: oddRowColor,
          selectedColumns: selectedColumns,
          rowsPerPage: rowsPerPage,
          totalPages: totalPages,
        })
      : '';
    document.body.appendChild(pageWrapper);
    const canvas = await html2canvas(pageWrapper, {
      scale: 2,
      useCORS: true,
      backgroundColor: '#ffffff',
      scrollY: -window.scrollY,
    });
    canvasPages.push(canvas);
    pageWrapper.remove();
  }

  const doc = new window.jspdf.jsPDF({ orientation: orientation, unit: 'pt', format: format });
  const pageWidth = doc.internal.pageSize.getWidth();
  const margin = 20;
  const imgWidth = pageWidth - margin * 2;

  canvasPages.forEach(function (canvas, index) {
    if (index > 0) doc.addPage();
    const imgHeight = (canvas.height * imgWidth) / canvas.width;
    doc.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, imgWidth, imgHeight);
  });

  doc.save('ترم‌ها_' + date + '.pdf');
  closeModal();
};

// ==================== Init ====================
const saveTermSessionCancellationBase = window.saveTermSessionCancellation;
window.saveTermSessionCancellation = async function () {
  await saveTermSessionCancellationBase();
  await window.refreshDashboard?.();
};
window.addEventListener('sornaz:data-changed', async function (event) {
  if (event.detail?.resource !== 'courses') return;
  try {
    const data = await termApi('/academy/admin/courses');
    window.termCourses = (data.courses || []).map((x) => ({ ...x, lessonId: x.lesson_id }));
    document.querySelectorAll('select[id$="Course"]').forEach(function (select) {
      const prefix = select.id === 'termCourse' ? '' : select.id.slice(0, -6),
        organizationUserId = Number(termField(prefix, 'Branch')?.value || 0),
        selected = select.value;
      select.innerHTML =
        '<option value="">انتخاب دوره</option>' +
        termCourses
          .filter((x) => x.organizationUserId === organizationUserId)
          .map(
            (x) =>
              `<option value="${x.id}" ${String(x.id) === selected ? 'selected' : ''}>${x.name}</option>`
          )
          .join('');
    });
  } catch (error) {
    console.error(error);
  }
});
(function initTerms() {
  setTimeout(function () {
    if (document.getElementById('termsTable')) {
      loadTerms().catch(function (e) {
        console.error(e);
        alert(e.message);
      });
      pollTermsRealtime();
      setInterval(pollTermsRealtime, 10000);
      document.addEventListener('visibilitychange', function () {
        if (!document.hidden) pollTermsRealtime();
      });
    }
  }, 200);
})();

// Assignment keeps all four steps mounted so identity and schedule selections survive navigation.
window.initTermStudentWizard = function (id) {
  const host = document.querySelector('[data-student-term-id]');
  if (!host) return;
  const en = document.documentElement.lang.startsWith('en');
  const content = host.querySelector('.max-h-\\[75vh\\]');
  const actions = content.lastElementChild;
  const panels = Array.from({length:4}, () => document.createElement('section'));
  const address = document.getElementById('stuAddresses')?.parentElement;
  const schedule = document.getElementById('stuTermSchedule');
  [...content.children].filter(node => node !== actions).forEach(node => panels[0].append(node));
  if (address) panels[1].append(address);
  if (schedule) panels[2].append(schedule);
  const roomLabel = schedule?.querySelector('[data-student-classroom]')?.closest('label');
  if (roomLabel) panels[3].append(roomLabel);
  else {
    const label = document.createElement('label'), select = document.createElement('select');
    label.textContent = en ? 'Classroom' : 'کلاس برگزاری';
    select.className = 'mt-2 w-full rounded-2xl border px-4 py-3';
    select.add(new Option(en ? 'Choose later' : 'تعیین در آینده', ''));
    const term = (window.studentTermGroups || []).find(row => Number(row.id) === id);
    (window.studentClassrooms || []).filter(room => Number(room.organizationUserId) === Number(term?.organizationUserId) || (room.organizationUserIds || []).map(Number).includes(Number(term?.organizationUserId))).forEach(room => select.add(new Option(room.name, room.id)));
    select.value = String(term?.scheduleDraft?.classroomId || '');
    label.append(select); panels[3].append(label);
  }
  const room = panels[3].querySelector('select');
  room.dataset.assignmentClassroom = '';
  room.onchange = () => window.previewStudentTermSchedule?.('stu', false);
  if (!schedule || schedule.classList.contains('hidden')) {
    const note = document.createElement('p'); note.textContent = en ? 'The existing session dates will be retained.' : 'تاریخ جلسات ثبت‌شده حفظ می‌شود.'; panels[2].append(note);
  }
  const progress = document.createElement('ol'); progress.className = 'grid grid-cols-4 gap-2';
  const labels = en ? ['Student', 'Address', 'Session dates', 'Classroom'] : ['هنرجو', 'آدرس', 'تاریخ جلسات', 'کلاس برگزاری'];
  labels.forEach((label,index) => {const item=document.createElement('li'); item.className='rounded-2xl border p-3 text-center text-sm'; item.textContent=(index+1).toLocaleString(en?'en':'fa-IR')+' · '+label; progress.append(item);});
  content.prepend(progress);
  panels.forEach(panel => {panel.className='space-y-6'; content.insertBefore(panel,actions);});
  const save = actions.querySelector('button'), back = document.createElement('button');
  back.type='button'; back.className='flex-1 rounded-2xl border px-4 py-3'; back.textContent=en?'Previous':'قبلی'; actions.prepend(back);
  let step=0;
  const render=()=>{panels.forEach((panel,index)=>panel.hidden=index!==step); [...progress.children].forEach((item,index)=>{item.classList.toggle('bg-indigo-600',index===step); item.classList.toggle('text-white',index===step); if(index===step)item.setAttribute('aria-current','step');else item.removeAttribute('aria-current');});back.hidden=step===0;save.textContent=step===3?(en?'Save student':'ذخیره هنرجو'):(en?'Next':'بعدی');};
  back.onclick=()=>{step--;render();};
  save.onclick=()=>{if(step===3)return window.saveStudent(); if(step<2 && !window.validateTermStudentStep(step))return; step++;render();};
  render();
};
