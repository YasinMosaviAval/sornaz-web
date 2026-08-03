let exceptionIndex = document.querySelectorAll(".availability-exception-row").length;

document.addEventListener("click", function (e) {

    if (e.target.id === "add-exception") {

        document
            .getElementById("availability-exception-list")
            .insertAdjacentHTML(
                "beforeend",
                `
                <div class="availability-exception-row">

                    <input
                        type="date"
                        name="exceptions[${exceptionIndex}][date]">

                    <input
                        type="time"
                        name="exceptions[${exceptionIndex}][start_time]">

                    <input
                        type="time"
                        name="exceptions[${exceptionIndex}][end_time]">

                    <select
                        name="exceptions[${exceptionIndex}][type]">

                        <option value="holiday">تعطیل</option>
                        <option value="closed">بسته</option>
                        <option value="busy">مشغول</option>
                        <option value="vacation">مرخصی</option>
                        <option value="blocked">مسدود</option>
                        <option value="unavailable">در دسترس نیست</option>

                    </select>

                    <input
                        type="text"
                        name="exceptions[${exceptionIndex}][note]"
                        placeholder="توضیح">

                    <button
                        type="button"
                        class="exception-remove">
                        حذف
                    </button>

                </div>
                `
            );

        exceptionIndex++;
    }

    if (e.target.classList.contains("exception-remove")) {
        e.target.closest(".availability-exception-row").remove();
    }

});