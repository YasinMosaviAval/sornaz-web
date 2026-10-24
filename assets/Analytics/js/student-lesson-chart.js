(function () {
  'use strict';
  window.renderStudentLessonChart = function (students) {
    if (!window.renderLessonModeChart || !document.getElementById('studentLessonChart')) return;
    const groups = new Map(), known = new Set(), unknown = new Set();
    for (const student of students) {
      const person = String(student.user_id || student.id);
      const modes = [...new Set(student.deliveryModes || [])].filter(mode => ['in_person','online'].includes(mode));
      if (!modes.length) { unknown.add(person); continue; }
      const key = String(student.lessonId || 'unknown');
      if (!groups.has(key)) groups.set(key, {
        name: student.lessonName && student.lessonName !== '—' ? student.lessonName : (document.documentElement.lang.startsWith('en') ? 'Unknown lesson' : 'درس نامشخص'),
        in_person: new Set(), online: new Set()
      });
      modes.forEach(mode => groups.get(key)[mode].add(person));
      known.add(person);
    }
    const rows = [...groups.values()].map(group => ({name:group.name,in_person:group.in_person.size,online:group.online.size}));
    rows.sort((a,b)=>a.name.localeCompare(b.name,document.documentElement.lang.startsWith('en')?'en':'fa'));
    window.renderLessonModeChart('studentLessonChart', {rows,counted:known.size,unknown:unknown.size}, {
      unit:['هنرجو','students'],
      unknown:['هنرجو با ثبت‌نام دارای شیوهٔ نامشخص','students with registrations of unknown mode'],
      empty:['هنرجویی با این فیلترها و شیوهٔ مشخص یافت نشد','No matching students with a known attendance mode']
    });
  };
  window.downloadStudentLessonChart = function () {
    if (!window.SornazCharts?.get('#studentLessonChart')) return;
    const link=document.createElement('a');link.download='students-by-lesson.png';link.href=SornazCharts.exportPng('#studentLessonChart');link.click();
  };
})();
