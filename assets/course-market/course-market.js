(() => {
    'use strict';
    const notice = document.getElementById('notice');
    const notify = (message, error = false) => {
        notice.textContent = message;
        notice.classList.toggle('error', error);
        notice.hidden = false;
    };
    const token = document.querySelector('meta[name="csrf-token"]').content;
    async function post(url, data) {
        data.set('_token', token);
        const response = await fetch(url, {method: 'POST', body: data, headers: {'Accept': 'application/json'}});
        let result;
        try { result = await response.json(); if (result.status && result.data) result = result.data; } catch { throw new Error('پاسخ سرور معتبر نیست. اتصال یا محدودیت حجم آپلود را بررسی کنید.'); }
        if (!response.ok || !result.success) throw new Error(result.message || 'عملیات انجام نشد.');
        return result.data;
    }
    document.querySelector('[data-buy]')?.addEventListener('click', async event => {
        const button = event.currentTarget;
        button.disabled = true;
        try {
            const data = await post(`/course-market/courses/${button.dataset.buy}/buy`, new FormData());
            location.assign(data.redirectUrl);
        } catch (error) { notify(error.message, true); button.disabled = false; }
    });
    document.querySelectorAll('[data-unlock-lesson]').forEach(form => form.addEventListener('submit', async event => {
        event.preventDefault(); const button = form.querySelector('button'); button.disabled = true;
        try { await post(form.action, new FormData(form)); location.reload(); }
        catch (error) { notify(error.message, true); button.disabled = false; }
    }));
    const form = document.getElementById('course-editor');
    if (!form) return;
    let course = JSON.parse(document.getElementById('course-data').textContent) || {
        id: 0, title: '', description: '', price: 0, status: 'draft', cover_id: null, curriculum: [], files: [], version: 0
    };
    let dirty = false;
    let busy = false;
    const state = document.getElementById('save-state');
    const chapters = document.getElementById('chapters');
    const markDirty = () => { dirty = true; state.textContent = 'تغییرات ذخیره نشده'; };
    const setBusy = value => {
        busy = value;
        form.querySelectorAll('button,input,textarea').forEach(element => { element.disabled = value; });
    };
    const node = (tag, text, className) => {
        const element = document.createElement(tag);
        if (text) element.textContent = text;
        if (className) element.className = className;
        return element;
    };
    const button = (text, action, className = 'secondary') => {
        const element = node('button', text, className);
        element.type = 'button';
        element.addEventListener('click', action);
        return element;
    };
    function field(label, value, change, multiline = false) {
        const wrapper = node('label', label);
        const input = node(multiline ? 'textarea' : 'input');
        input.value = value;
        input.maxLength = multiline ? 100000 : 180;
        if (multiline) input.rows = 4;
        else input.required = true;
        input.addEventListener('input', () => { change(input.value); markDirty(); });
        wrapper.append(input);
        return wrapper;
    }
    function move(array, index, direction) {
        const target = index + direction;
        if (target < 0 || target >= array.length) return;
        [array[index], array[target]] = [array[target], array[index]];
        markDirty(); render();
    }
    function controls(array, index, kind) {
        const tools = node('div', '', 'tools');
        if (index > 0) tools.append(button('↑ بالاتر', () => move(array, index, -1)));
        if (index < array.length - 1) tools.append(button('↓ پایین‌تر', () => move(array, index, 1)));
        tools.append(button(`حذف ${kind}`, () => {
            if (!confirm(`این ${kind} و محتوای آن از دوره حذف شود؟`)) return;
            array.splice(index, 1); markDirty(); render();
        }, 'danger'));
        return tools;
    }
    function render() {
        chapters.replaceChildren();
        document.getElementById('curriculum-empty').hidden = course.curriculum.length > 0;
        course.curriculum.forEach((chapter, ci) => {
            const section = node('section', '', 'chapter');
            section.append(node('h3', `فصل ${ci + 1}`), controls(course.curriculum, ci, 'فصل'));
            section.append(field('عنوان فصل', chapter.title, value => { chapter.title = value; }));
            chapter.lessons.forEach((lesson, li) => {
                const article = node('section', '', 'lesson');
                article.append(node('h3', `درس ${li + 1}`), controls(chapter.lessons, li, 'درس'));
                article.append(field('عنوان درس', lesson.title, value => { lesson.title = value; }));
                article.append(field('متن و توضیحات درس', lesson.text, value => { lesson.text = value; }, true));
                const passwordLabel = node('label', lesson.has_password ? 'رمز جدید درس (خالی = بدون تغییر)' : 'رمز اختصاصی درس (اختیاری، حداقل ۸ نویسه)');
                const password = node('input'); password.type = 'password'; password.autocomplete = 'new-password'; password.maxLength = 72;
                password.value = lesson.password || '';
                password.addEventListener('input', () => { lesson.password = password.value; markDirty(); });
                passwordLabel.append(password); article.append(passwordLabel);
                if (lesson.has_password) article.append(button(lesson.clear_password ? 'حذف رمز پس از ذخیره' : 'حذف رمز اختصاصی', () => { lesson.clear_password = true; lesson.password = ''; markDirty(); render(); }));
                lesson.media.forEach(mid => {
                    const file = course.files.find(item => Number(item.id) === Number(mid));
                    const attachment = node('div', '', 'attachment');
                    const video = file?.mime.startsWith('video/');
                    const preview = node(video ? 'video' : 'img');
                    preview.src = `/course-market/media/${mid}`;
                    if (video) { preview.controls = true; preview.preload = 'none'; }
                    else { preview.alt = 'تصویر درس'; preview.loading = 'lazy'; }
                    const link = node('a', video ? 'مشاهده ویدیو' : 'مشاهده تصویر');
                    link.href = preview.src; link.target = '_blank'; link.rel = 'noopener';
                    attachment.append(preview, link, button('حذف از درس', () => {
                        lesson.media = lesson.media.filter(item => item !== mid); markDirty(); render();
                    }, 'danger'));
                    article.append(attachment);
                });
                const uploadLabel = node('label', 'افزودن تصویر یا ویدیو');
                const upload = node('input'); upload.type = 'file'; upload.multiple = true;
                upload.accept = 'image/jpeg,image/png,image/webp,video/mp4,video/webm';
                const progress = node('progress'); progress.max = 100; progress.value = 0; progress.hidden = true;
                upload.addEventListener('change', () => uploadFiles([...upload.files], lesson, progress));
                uploadLabel.append(upload, node('small', 'تصویر تا ۱۰ مگابایت؛ MP4 یا WebM تا ۲۵۰ مگابایت. پس از آپلود، دوره را ذخیره کنید.'), progress);
                article.append(uploadLabel); section.append(article);
            });
            section.append(button('＋ افزودن درس', () => {
                chapter.lessons.push({title: '', text: '', media: []}); markDirty(); render();
                section.scrollIntoView({block: 'nearest'});
            }));
            chapters.append(section);
        });
    }
    function syncInfo() {
        course.title = form.elements.title.value;
        course.description = form.elements.description.value;
        course.price = form.elements.price.value;
    }
    function updateSavedInfo() {
        state.textContent = course.status === 'published' ? 'منتشرشده' : 'پیش‌نویس ذخیره شد';
        const link = document.getElementById('view-course');
        link.href = `/course-market/courses/${course.id}`; link.hidden = false;
        history.replaceState(null, '', `/course-market/courses/${course.id}/edit`);
    }
    async function save(status) {
        syncInfo();
        const payload = new FormData();
        payload.set('payload', JSON.stringify({...course, status}));
        const saved = await post(`/course-market/courses${course.id ? '/' + course.id : ''}`, payload);
        course = saved;
        dirty = false;
        updateSavedInfo();
    }
    function uploadFile(file, progress) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `/course-market/courses/${course.id}/media`);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.upload.onprogress = event => { if (event.lengthComputable) progress.value = event.loaded / event.total * 100; };
            xhr.onerror = () => reject(new Error('ارتباط هنگام آپلود قطع شد. دوباره تلاش کنید.'));
            xhr.onload = () => {
                try {
                    let result = JSON.parse(xhr.responseText);
                    if (result.status && result.data) result = result.data;
                    if (xhr.status < 200 || xhr.status >= 300 || !result.success) throw new Error(result.message || 'آپلود ناموفق بود.');
                    resolve(result.data);
                } catch (error) { reject(new Error(error instanceof SyntaxError ? 'آپلود انجام نشد؛ محدودیت حجم فایل در سرور را بررسی کنید.' : error.message)); }
            };
            const data = new FormData(); data.set('_token', token); data.set('file', file); xhr.send(data);
        });
    }
    async function uploadFiles(files, lesson, progress) {
        if (!files.length || busy) return;
        if (lesson && lesson.media.length + files.length > 20) { notify('حداکثر ۲۰ فایل برای هر درس مجاز است.', true); return; }
        for (const file of files) {
            const image = ['image/jpeg','image/png','image/webp'].includes(file.type);
            if ((!image && !['video/mp4','video/webm'].includes(file.type)) || (!lesson && !image) || file.size > (image ? 10 : 250) * 1024 * 1024 || !file.size) {
                notify('نوع یا حجم فایل مجاز نیست. تصویر تا ۱۰ و ویدیو تا ۲۵۰ مگابایت مجاز است.', true); return;
            }
        }
        // Save a minimal draft first; retain the in-progress curriculum and references.
        setBusy(true); progress.hidden = false;
        try {
            if (!course.id) {
                syncInfo();
                if (!course.title.trim()) throw new Error('ابتدا عنوان دوره را وارد کنید.');
                const payload = new FormData();
                payload.set('payload', JSON.stringify({...course, status: 'draft', curriculum: []}));
                const saved = await post('/course-market/courses', payload);
                course.id = saved.id; course.version = saved.version;
                updateSavedInfo();
            }
            for (const file of files) {
                progress.value = 0;
                notify(`در حال آپلود ${file.name}…`);
                const media = await uploadFile(file, progress);
                course.files.push(media);
                if (lesson) lesson.media.push(media.id);
                else {
                    course.cover_id = media.id;
                    const preview = document.getElementById('cover-preview');
                    preview.src = media.url; preview.hidden = false;
                }
                markDirty();
            }
            notify('آپلود انجام شد. برای ثبت فایل‌ها در دوره، دکمه ذخیره را بزنید.');
        } catch (error) { notify(error.message, true); }
        finally { progress.hidden = true; render(); setBusy(false); }
    }
    ['title','description','price'].forEach(name => {
        form.elements[name].value = course[name];
        form.elements[name].addEventListener('input', markDirty);
    });
    if (course.cover_id) {
        const preview = document.getElementById('cover-preview');
        preview.src = `/course-market/media/${course.cover_id}`; preview.hidden = false;
    }
    if (course.id) updateSavedInfo();
    document.getElementById('add-chapter').addEventListener('click', () => {
        course.curriculum.push({title: '', lessons: []}); markDirty(); render();
        chapters.lastElementChild.querySelector('input').focus();
    });
    document.getElementById('cover-upload').addEventListener('change', event => {
        const progress = node('progress'); progress.max = 100; event.target.parentElement.append(progress);
        uploadFiles([...event.target.files], null, progress).finally(() => progress.remove());
    });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (busy) return;
        setBusy(true);
        try {
            await save(event.submitter?.dataset.status || 'draft');
            render(); notify(course.status === 'published' ? 'دوره منتشر شد و در فروشگاه قابل تهیه است.' : 'پیش‌نویس ذخیره شد.');
        } catch (error) { notify(error.message, true); }
        finally { setBusy(false); }
    });
    window.addEventListener('beforeunload', event => {
        if (dirty || busy) { event.preventDefault(); event.returnValue = ''; }
    });
    render();
})();
