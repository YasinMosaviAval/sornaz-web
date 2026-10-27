(function () {
  'use strict';
  const categories = {
    cover: { label: 'کاور', accept: 'image/jpeg,image/png,image/webp' },
    logo: { label: 'لوگو', accept: 'image/jpeg,image/png,image/webp' },
    intro_video: { label: 'ویدیو معرفی', accept: 'video/mp4,video/webm,video/quicktime' },
    gallery: {
      label: 'گالری',
      accept: 'image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime',
    },
  };
  let owners = [],
    items = [],
    hideFilters = false,
    pendingFile = null,
    modalItem = null,
    viewerRows = [],
    viewerIndex = 0;
  const selected = {};
  let overviewOwner = 'all';
  window.filterGalleryByBranch = (owner) => {
    overviewOwner = String(owner);
    Object.keys(categories).forEach((category) => { selected[category] = overviewOwner; });
    render();
  };
  let galleryRealtimeVersion = null,
    galleryRealtimeBusy = false;
  const galleryRealtimeChannel =
    'BroadcastChannel' in window ? new BroadcastChannel('sornaz-admin-data') : null;
  const esc = (v) =>
    String(v ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  window.getGalleryCategory = (k) => categories[k] || { label: k, accept: '' };
  window.getGalleryAcceptForCategory = (k) => window.getGalleryCategory(k).accept;
  window.getGalleryAllowedTypeLabel = (k) =>
    k === 'intro_video' ? 'ویدیو' : k === 'gallery' ? 'تصویر یا ویدیو' : 'تصویر';
  window.getGalleryOwnerOptions = (s) =>
    owners
      .map(
        (o) =>
          `<option value="${o.userId}" ${String(o.userId) === String(s) ? 'selected' : ''}>${esc(o.name)}</option>`
      )
      .join('');
  function category() {
    return (
      document.querySelector('.section:not(.hidden)[data-gallery-category]')?.dataset
        .galleryCategory || 'gallery'
    );
  }
  async function request(url, options = {}) {
    options.credentials = 'same-origin';
    options.headers = Object.assign(
      {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': window.adminCsrfToken || '',
      },
      options.headers || {}
    );
    const response = await fetch(url, options),
      payload = await response.json().catch(() => ({})),
      body = payload.data ?? payload;
    if (!response.ok || body.success === false)
      throw new Error(body.message || 'انجام عملیات ناموفق بود.');
    return body.data ?? body;
  }
  async function load() {
    if (!document.querySelector('[data-gallery-category]')) return;
    try {
      const data = await request('/analytics/admin-gallery');
      owners = data.owners || [];
      window.syncPanelOrganizationSelect?.(document.getElementById('galleryOwner'), owners, 'userId');
      items = data.items || [];
      hideFilters = !!data.hideOwnerFilters;
      Object.keys(categories).forEach((k) => {
        if (!(k in selected))
          selected[k] = hideFilters && owners[0] ? String(owners[0].userId) : 'all';
      });
      render();
    } catch (e) {
      console.error(e);
      alert(e.message);
    }
  }
  window.reloadAdminGallery = load;
  window.addEventListener('admin-media-changed', (event) => {
    if (event.detail?.source !== 'gallery' && document.querySelector('[data-gallery-category]')) load();
  });
  function visibleRows(k) {
    const o = selected[k] || 'all';
    return items.filter(
      (i) => i.category === k && (o === 'all' || (o === 'academy' ? owners.some((owner) => owner.kind === 'academy' && String(owner.userId) === String(i.ownerId)) : String(i.ownerId) === String(o)))
    );
  }
  function render() {
    const filterConfig = {
      branches: () => owners.filter((owner) => owner.kind !== 'academy').map((owner) => ({ id: owner.userId, name: owner.name })),
      showAcademy: !window.branchSiteAdmin && owners.some((owner) => owner.kind === 'academy'),
      options: window.branchSiteAdmin ? owners.map((owner) => ({ value: String(owner.userId), label: owner.name })) : undefined,
    };
    if (document.getElementById('galleryBranchTabs')) {
      window.renderPanelOrganizationTabs?.('galleryBranchTabs', overviewOwner, window.filterGalleryByBranch, filterConfig);
      const shell = window.panelBranchFilterShell?.(document.getElementById('galleryBranchTabs'));
      if (hideFilters && shell) shell.classList.add('hidden');
    }
    document.querySelectorAll('.gallery-owner-tabs').forEach((tabs) => {
      const k = document.getElementById(tabs.dataset.gallerySection)?.dataset.galleryCategory || 'gallery';
      tabs.id ||= 'gallery-' + k + '-owner-tabs';
      window.renderPanelOrganizationTabs?.(tabs.id, selected[k] || 'all', (value) => {
        selected[k] = value;
        render();
      }, filterConfig);
      if (hideFilters) window.panelBranchFilterShell?.(tabs)?.classList.add('hidden');
    });
    document.querySelectorAll('.gallery-view-controls').forEach((controls) => controls.remove());
    document.querySelectorAll('.gallery-grid').forEach((g) => {
      const rows = visibleRows(g.dataset.galleryCategory);
      g.innerHTML = rows.length
        ? rows.map((item) => window.getGalleryCardHTML(item, 'compact')).join('')
        : window.getGalleryEmptyHTML();
    });
  }
  // Keep cached callers compatible without allowing detailed cards to return.
  window.setGalleryDisplayMode = render;
  window.openGalleryViewer = (id) => {
    const item = items.find((x) => x.id === id);
    if (!item) return;
    viewerRows = visibleRows(item.category);
    viewerIndex = Math.max(
      0,
      viewerRows.findIndex((x) => x.id === id)
    );
    renderGalleryViewer();
  };
  function renderGalleryViewer() {
    const item = viewerRows[viewerIndex];
    if (!item) return closeModal();
    const host = document.getElementById('modalContainer');
    host.innerHTML = window.getGalleryViewerHTML(item, viewerIndex, viewerRows.length);
    if (host.firstElementChild) host.firstElementChild.id = 'galleryViewerModal';
  }
  window.moveGalleryViewer = (step) => {
    if (!viewerRows.length) return;
    viewerIndex = (viewerIndex + step + viewerRows.length) % viewerRows.length;
    renderGalleryViewer();
  };
  document.addEventListener('keydown', (event) => {
    if (!document.getElementById('galleryViewerModal')) return;
    if (event.key === 'ArrowRight') window.moveGalleryViewer(-1);
    if (event.key === 'ArrowLeft') window.moveGalleryViewer(1);
    if (event.key === 'Escape') closeModal();
  });
  window.renderGalleryOwnerTabs = render;
  window.renderAllGallerySections = render;
  function ownerId() {
    return document.getElementById('galleryOwner')?.value || owners[0]?.userId || '';
  }
  function ownerName() {
    return owners.find((o) => String(o.userId) === String(ownerId()))?.name || 'حساب';
  }
  function suggestedTitle(k) {
    const name = ownerName();
    if (k === 'cover') return `کاور ${name}`;
    if (k === 'logo') return `لوگو ${name}`;
    if (k === 'intro_video') return `ویدیو معرفی ${name}`;
    const count = items.filter(
      (x) => x.category === 'gallery' && String(x.ownerId) === String(ownerId())
    ).length + 1;
    return `آیتم شماره ${count} گالری ${name}`;
  }
  window.syncGalleryDefaultTitle = (force = false) => {
    const input = document.getElementById('galleryTitle'),
      chosenCategory = document.getElementById('galleryCategory')?.value || category();
    if (!input || modalItem?.id) return;
    const old = input.dataset.suggested || '',
      next = suggestedTitle(chosenCategory);
    if (force || !input.value || input.value === old) {
      input.value = next;
      input.dataset.suggested = next;
    }
  };
  window.onGalleryCategoryChange = () => {
    const kind = document.getElementById('galleryCategory')?.value || 'gallery';
    const file = document.getElementById('galleryFile');
    if (file) {
      file.value = '';
      file.accept = window.getGalleryAcceptForCategory(kind);
      file.nextElementSibling.textContent = `${window.getGalleryAllowedTypeLabel(kind)}؛ تصویر حداکثر ۱۰ و ویدیو حداکثر ۱۰۰ مگابایت`;
    }
    pendingFile = null;
    window.syncGalleryDefaultTitle(true);
  };
  function openForm(item) {
    modalItem = item || null;
    pendingFile = null;
    document.getElementById('modalContainer').innerHTML = item
      ? window.getGalleryEditModalHTML(item, hideFilters)
      : window.getGalleryAddModalHTML(category(), hideFilters);
    window.syncGalleryDefaultTitle(true);
  }
  window.openAddGalleryModal = () => {
    if (!owners.length) return alert('حسابی برای ثبت رسانه یافت نشد.');
    openForm(null);
  };
  window.editGalleryItem = (id) => {
    const item = items.find((x) => x.id === id);
    if (item) openForm(item);
  };
  function snapshot() {
    return {
      item: modalItem,
      category: modalItem?.category || category(),
      chosenCategory: document.getElementById('galleryCategory')?.value || category(),
      ownerId: ownerId(),
      title: document.getElementById('galleryTitle')?.value || '',
      summary: document.getElementById('gallerySummary')?.value || '',
      description: document.getElementById('galleryDesc')?.value || '',
    };
  }
  function restore(s) {
    modalItem = s.item;
    document.getElementById('modalContainer').innerHTML = s.item
      ? window.getGalleryEditModalHTML(s.item, hideFilters)
      : window.getGalleryAddModalHTML(s.chosenCategory, hideFilters);
    document.getElementById('galleryOwner').value = s.ownerId;
    if (document.getElementById('galleryCategory')) document.getElementById('galleryCategory').value = s.chosenCategory;
    document.getElementById('galleryTitle').value = s.title;
    document.getElementById('gallerySummary').value = s.summary;
    document.getElementById('galleryDesc').value = s.description;
  }
  window.onGalleryFileChange = (event) => {
    const file = event.target?.files?.[0];
    if (!file) return;
    window.syncGalleryDefaultTitle(true);
    if (file.type.startsWith('video/')) {
      pendingFile = null;
      return;
    }
    if (!file.type.startsWith('image/')) return alert('نوع فایل مجاز نیست.');
    const state = snapshot(),
      mode =
        state.chosenCategory === 'logo' ? 'avatar' : state.chosenCategory === 'cover' ? 'cover' : 'gallery';
    window.openAdminImageEditor(file, mode, {
      onApply: (blob) => {
        pendingFile = blob;
        restore(state);
      },
      onCancel: () => restore(state),
    });
  };
  function fields(k, required) {
    const raw = document.getElementById('galleryFile')?.files?.[0],
      file = pendingFile || raw,
      title = document.getElementById('galleryTitle')?.value.trim() || '';
    if (required && !file) throw new Error('انتخاب فایل الزامی است.');
    if (!title) throw new Error('عنوان را وارد کنید.');
    const d = new FormData();
    d.set('_token', window.adminCsrfToken || '');
    d.set('ownerId', ownerId());
    d.set('collection', k);
    d.set('title', title);
    d.set('summary', document.getElementById('gallerySummary')?.value.trim() || '');
    d.set('description', document.getElementById('galleryDesc')?.value.trim() || '');
    if (file) d.set('file', file, pendingFile ? 'gallery.jpg' : file.name);
    return d;
  }
  async function save(url, data) {
    try {
      await request(url, { method: 'POST', body: data });
      pendingFile = null;
      closeModal();
      await load();
      window.dispatchEvent(
        new CustomEvent('admin-media-changed', { detail: { source: 'gallery' } })
      );
    } catch (e) {
      alert(e.message);
    }
  }
  window.saveGalleryItem = () => {
    try {
      return save('/analytics/admin-gallery', fields(document.getElementById('galleryCategory')?.value || category(), true));
    } catch (e) {
      alert(e.message);
    }
  };
  window.saveEditedGalleryItem = (id) => {
    const item = items.find((x) => x.id === id);
    if (!item) return;
    try {
      return save(`/analytics/admin-gallery/${id}/update`, fields(item.category, false));
    } catch (e) {
      alert(e.message);
    }
  };
  window.deleteGalleryItem = async (id) => {
    const item = items.find((x) => x.id === id),
      ok = window.AppDialog
        ? await AppDialog.confirmDelete(items, id, 'رسانه')
        : confirm(`رسانه «${item?.title || ''}» حذف شود؟`);
    if (!ok) return;
    try {
      await request(`/analytics/admin-gallery/${id}/delete`, {
        method: 'POST',
        body: new URLSearchParams({ _token: window.adminCsrfToken || '' }),
      });
      await load();
      window.dispatchEvent(
        new CustomEvent('admin-media-changed', { detail: { source: 'gallery' } })
      );
    } catch (e) {
      alert(e.message);
    }
  };
  async function pollGalleryRealtime() {
    if (
      galleryRealtimeBusy ||
      document.hidden ||
      !document.querySelector('[data-gallery-category]')
    )
      return;
    galleryRealtimeBusy = true;
    try {
      const state = await request('/analytics/admin-gallery/realtime-version');
      if (galleryRealtimeVersion === null) {
        galleryRealtimeVersion = state.version;
        return;
      }
      if (state.version !== galleryRealtimeVersion) {
        galleryRealtimeVersion = state.version;
        await load();
        galleryRealtimeChannel?.postMessage({ resource: 'gallery', version: state.version });
      }
    } catch (e) {
    } finally {
      galleryRealtimeBusy = false;
    }
  }
  galleryRealtimeChannel?.addEventListener('message', async (event) => {
    if (event.data?.resource !== 'gallery' || event.data.version === galleryRealtimeVersion) return;
    galleryRealtimeVersion = event.data.version;
    await load();
  });
  function initGallery() {
    if (!document.querySelector('[data-gallery-category]')) return;
    load();
    pollGalleryRealtime();
    setInterval(pollGalleryRealtime, 2000);
    document.addEventListener('visibilitychange', () => {
      if (!document.hidden) pollGalleryRealtime();
    });
  }
  document.addEventListener('DOMContentLoaded', initGallery);
  if (document.readyState !== 'loading') initGallery();
  window.addEventListener('panel-organizations-changed', () => { Promise.resolve(load()).catch(console.error); });
})();
