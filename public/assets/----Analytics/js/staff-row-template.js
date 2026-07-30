// (function () {
//     window.getStaffRowHTML = function (item, statusClass) {
//         return `
//             <td class="py-4 px-5 font-medium">${item.name}</td>
//             <td class="py-4 px-5">${item.typeLabel}</td>
//             <td class="py-4 px-5">${item.contractTitle}</td>
//             <td class="py-4 px-5">${item.branch}</td>
//             <td class="py-4 px-5">${item.startDate}</td>
//             <td class="py-4 px-5">${item.endDate}</td>
//             <td class="py-4 px-5">${item.price.toLocaleString('fa-IR')} ${item.currency}</td>
//             <td class="py-4 px-5">
//                 <span class="px-3 py-1 rounded-full text-xs ${statusClass}">${item.status}</span>
//             </td>
//             <td class="py-4 px-5 text-left">
//                 <div class="inline-flex flex-nowrap items-center gap-3 whitespace-nowrap">
//                     <button onclick="viewStaff(${item.id})" class="text-indigo-600 hover:underline text-sm leading-6 align-middle">جزئیات</button>
//                     <button onclick="toggleStaffInlineEdit(${item.id})" class="text-gray-500 hover:text-indigo-600 text-sm leading-6 align-middle">ویرایش</button>
//                     <button onclick="deleteStaff(${item.id})" class="text-red-500 hover:text-red-700 text-sm leading-6 align-middle">حذف</button>
//                 </div>
//             </td>
//         `;
//     };
// })();
