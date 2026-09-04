<template id="adminTableTemplate">
    <section class="admin-ui-table overflow-hidden rounded-3xl bg-white shadow">
        <div data-slot="table" class="overflow-x-auto"></div>
        <footer data-slot="pagination" class="flex flex-col items-center justify-between gap-4 border-t px-6 py-4 text-sm text-gray-500 sm:flex-row"></footer>
    </section>
</template>
